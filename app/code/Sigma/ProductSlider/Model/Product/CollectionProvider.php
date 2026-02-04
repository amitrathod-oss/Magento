<?php
/**
 * Product Collection Provider
 *
 * @category  Sigma
 * @package   Sigma_ProductSlider
 */

declare(strict_types=1);

namespace Sigma\ProductSlider\Model\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Helper\Stock as StockHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestsellersCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Sigma\ProductSlider\Api\Data\SliderConfigInterface;
use Sigma\ProductSlider\Api\ProductCollectionProviderInterface;

/**
 * Class CollectionProvider
 *
 * Provides product collections for the slider widget
 */
class CollectionProvider implements ProductCollectionProviderInterface
{
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $productCollectionFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    private CategoryRepositoryInterface $categoryRepository;

    /**
     * @var StockHelper
     */
    private StockHelper $stockHelper;

    /**
     * @var Visibility
     */
    private Visibility $productVisibility;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var BestsellersCollectionFactory
     */
    private BestsellersCollectionFactory $bestsellersCollectionFactory;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param CollectionFactory $productCollectionFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StockHelper $stockHelper
     * @param Visibility $productVisibility
     * @param StoreManagerInterface $storeManager
     * @param BestsellersCollectionFactory $bestsellersCollectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        CategoryRepositoryInterface $categoryRepository,
        StockHelper $stockHelper,
        Visibility $productVisibility,
        StoreManagerInterface $storeManager,
        BestsellersCollectionFactory $bestsellersCollectionFactory,
        LoggerInterface $logger
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryRepository = $categoryRepository;
        $this->stockHelper = $stockHelper;
        $this->productVisibility = $productVisibility;
        $this->storeManager = $storeManager;
        $this->bestsellersCollectionFactory = $bestsellersCollectionFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function getCollection(SliderConfigInterface $config): Collection
    {
        $collection = $this->createBaseCollection();
        
        $source = $config->getProductSource();
        
        switch ($source) {
            case self::SOURCE_CATEGORY:
                $this->applyCategoryFilter($collection, $config->getCategoryIds());
                break;
            case self::SOURCE_NEW:
                $this->applyNewProductsFilter($collection);
                break;
            case self::SOURCE_BESTSELLER:
                $this->applyBestsellerFilter($collection);
                break;
            case self::SOURCE_FEATURED:
                $this->applyFeaturedFilter($collection);
                break;
            case self::SOURCE_SALE:
                $this->applySaleFilter($collection);
                break;
            case self::SOURCE_RANDOM:
                $collection->getSelect()->orderRand();
                break;
            default:
                $this->applyCategoryFilter($collection, $config->getCategoryIds());
        }
        
        // Apply stock filter - only in-stock products
        $this->stockHelper->addInStockFilterToCollection($collection);
        
        // Limit results
        $collection->setPageSize($config->getProductCount());
        
        return $collection;
    }

    /**
     * @inheritdoc
     */
    public function getProducts(SliderConfigInterface $config): array
    {
        return $this->getCollection($config)->getItems();
    }

    /**
     * Create base product collection with common filters
     *
     * @return Collection
     */
    private function createBaseCollection(): Collection
    {
        /** @var Collection $collection */
        $collection = $this->productCollectionFactory->create();
        
        $collection
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', Status::STATUS_ENABLED)
            ->setVisibility($this->productVisibility->getVisibleInCatalogIds())
            ->addStoreFilter($this->storeManager->getStore()->getId())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addUrlRewrite();
        
        return $collection;
    }

    /**
     * Apply category filter to collection
     *
     * @param Collection $collection
     * @param array $categoryIds
     * @return void
     */
    private function applyCategoryFilter(Collection $collection, array $categoryIds): void
    {
        if (empty($categoryIds)) {
            return;
        }

        try {
            $collection->addCategoriesFilter(['in' => $categoryIds]);
        } catch (\Exception $e) {
            $this->logger->error(
                'Sigma_ProductSlider: Failed to apply category filter',
                ['category_ids' => $categoryIds, 'error' => $e->getMessage()]
            );
        }
    }

    /**
     * Apply new products filter
     *
     * @param Collection $collection
     * @return void
     */
    private function applyNewProductsFilter(Collection $collection): void
    {
        $todayStartOfDayDate = date('Y-m-d 00:00:00');
        $todayEndOfDayDate = date('Y-m-d 23:59:59');

        $collection->addAttributeToFilter(
            'news_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            'news_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            [
                ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
                ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
            ]
        )->addAttributeToSort('news_from_date', 'desc');
    }

    /**
     * Apply bestseller filter
     *
     * @param Collection $collection
     * @return void
     */
    private function applyBestsellerFilter(Collection $collection): void
    {
        try {
            $bestsellers = $this->bestsellersCollectionFactory->create()
                ->setPeriod('month')
                ->addStoreFilter($this->storeManager->getStore()->getId())
                ->setPageSize(100);

            $productIds = [];
            foreach ($bestsellers as $item) {
                $productIds[] = $item->getProductId();
            }

            if (!empty($productIds)) {
                $collection->addIdFilter($productIds);
                $collection->getSelect()->order(
                    new \Zend_Db_Expr('FIELD(e.entity_id, ' . implode(',', $productIds) . ')')
                );
            }
        } catch (\Exception $e) {
            $this->logger->error(
                'Sigma_ProductSlider: Failed to get bestsellers',
                ['error' => $e->getMessage()]
            );
            // Fallback to random products
            $collection->getSelect()->orderRand();
        }
    }

    /**
     * Apply featured products filter
     *
     * @param Collection $collection
     * @return void
     */
    private function applyFeaturedFilter(Collection $collection): void
    {
        // Featured products typically use a custom attribute
        // Check if 'featured' attribute exists
        try {
            $collection->addAttributeToFilter('featured', 1);
        } catch (\Exception $e) {
            // If featured attribute doesn't exist, fallback to random
            $this->logger->info(
                'Sigma_ProductSlider: Featured attribute not found, using random products'
            );
            $collection->getSelect()->orderRand();
        }
    }

    /**
     * Apply sale products filter
     *
     * @param Collection $collection
     * @return void
     */
    private function applySaleFilter(Collection $collection): void
    {
        $todayDate = date('Y-m-d');

        $collection->addAttributeToFilter(
            'special_price',
            ['notnull' => true]
        )->addAttributeToFilter(
            'special_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            'special_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        );
    }
}
