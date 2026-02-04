<?php
/**
 * Product Slider Widget Block
 *
 * @category  Sigma
 * @package   Sigma_ProductSlider
 */

declare(strict_types=1);

namespace Sigma\ProductSlider\Block\Widget;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Psr\Log\LoggerInterface;
use Sigma\ProductSlider\Api\Data\SliderConfigInterface;
use Sigma\ProductSlider\Api\ProductCollectionProviderInterface;
use Sigma\ProductSlider\Model\Config;
use Sigma\ProductSlider\Model\Data\SliderConfig;

/**
 * Class ProductSlider
 *
 * Widget block for displaying product slider
 */
class ProductSlider extends AbstractProduct implements BlockInterface, IdentityInterface
{
    /**
     * Cache tag
     */
    public const CACHE_TAG = 'SIGMA_PRODUCT_SLIDER';

    /**
     * @var string
     */
    protected $_template = 'Sigma_ProductSlider::widget/product-slider.phtml';

    /**
     * @var ProductCollectionProviderInterface
     */
    private ProductCollectionProviderInterface $collectionProvider;

    /**
     * @var Config
     */
    private Config $sliderConfig;

    /**
     * @var Json
     */
    private Json $jsonSerializer;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var SliderConfigInterface|null
     */
    private ?SliderConfigInterface $configObject = null;

    /**
     * @var array|null
     */
    private ?array $products = null;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ProductCollectionProviderInterface $collectionProvider
     * @param Config $sliderConfig
     * @param Json $jsonSerializer
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductCollectionProviderInterface $collectionProvider,
        Config $sliderConfig,
        Json $jsonSerializer,
        LoggerInterface $logger,
        array $data = []
    ) {
        $this->collectionProvider = $collectionProvider;
        $this->sliderConfig = $sliderConfig;
        $this->jsonSerializer = $jsonSerializer;
        $this->logger = $logger;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->addData([
            'cache_lifetime' => $this->sliderConfig->getCacheLifetime(),
            'cache_tags' => [
                self::CACHE_TAG,
                \Magento\Catalog\Model\Product::CACHE_TAG
            ]
        ]);
    }

    /**
     * Check if module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->sliderConfig->isEnabled();
    }

    /**
     * Get slider configuration object
     *
     * @return SliderConfigInterface
     */
    public function getSliderConfig(): SliderConfigInterface
    {
        if ($this->configObject === null) {
            $this->configObject = new SliderConfig([
                SliderConfigInterface::TITLE => $this->getData('title') ?? '',
                SliderConfigInterface::PRODUCT_SOURCE => $this->getData('product_source') ?? 'category',
                SliderConfigInterface::CATEGORY_IDS => $this->getData('category_ids') ?? '',
                SliderConfigInterface::PRODUCT_COUNT => $this->getData('product_count') ?? 10,
                SliderConfigInterface::SLIDES_TO_SHOW => $this->getData('slides_to_show') ?? 4,
                SliderConfigInterface::SLIDES_TO_SHOW_TABLET => $this->getData('slides_to_show_tablet') ?? 3,
                SliderConfigInterface::SLIDES_TO_SHOW_MOBILE => $this->getData('slides_to_show_mobile') ?? 2,
                SliderConfigInterface::AUTOPLAY => $this->getData('autoplay') ?? false,
                SliderConfigInterface::AUTOPLAY_SPEED => $this->getData('autoplay_speed') ?? 3000,
                SliderConfigInterface::SHOW_NAV => $this->getData('show_nav') ?? true,
                SliderConfigInterface::SHOW_DOTS => $this->getData('show_dots') ?? false,
                SliderConfigInterface::INFINITE_LOOP => $this->getData('infinite_loop') ?? true,
                SliderConfigInterface::SHOW_ADD_TO_CART => $this->getData('show_add_to_cart') ?? true,
                SliderConfigInterface::SHOW_WISHLIST => $this->getData('show_wishlist') ?? true,
                SliderConfigInterface::SHOW_COMPARE => $this->getData('show_compare') ?? true,
            ]);
        }
        return $this->configObject;
    }

    /**
     * Get slider title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getSliderConfig()->getTitle();
    }

    /**
     * Get products for slider
     *
     * @return array
     */
    public function getProducts(): array
    {
        if ($this->products === null) {
            try {
                $collection = $this->collectionProvider->getCollection($this->getSliderConfig());
                $this->products = $collection->getItems();
            } catch (\Exception $e) {
                $this->logger->error(
                    'Sigma_ProductSlider: Error loading products',
                    ['error' => $e->getMessage()]
                );
                $this->products = [];
            }
        }
        return $this->products;
    }

    /**
     * Check if slider has products
     *
     * @return bool
     */
    public function hasProducts(): bool
    {
        return count($this->getProducts()) > 0;
    }

    /**
     * Get product image URL
     *
     * @param Product $product
     * @param string $imageType
     * @return string
     */
    public function getProductImageUrl(Product $product, string $imageType = 'category_page_grid'): string
    {
        try {
            return $this->_imageHelper
                ->init($product, $imageType)
                ->setImageFile($product->getImage())
                ->getUrl();
        } catch (\Exception $e) {
            return $this->_imageHelper->getDefaultPlaceholderUrl('small_image');
        }
    }

    /**
     * Render product price HTML for the slider
     *
     * @param Product $product
     * @return string
     */
    public function renderProductPrice(Product $product): string
    {
        return $this->getProductPrice($product);
    }

    /**
     * Get JavaScript configuration
     *
     * @return string
     */
    public function getJsConfig(): string
    {
        try {
            return $this->jsonSerializer->serialize($this->getSliderConfig()->toJsConfig());
        } catch (\Exception $e) {
            $this->logger->error(
                'Sigma_ProductSlider: Error serializing JS config',
                ['error' => $e->getMessage()]
            );
            return '{}';
        }
    }

    /**
     * Get unique slider ID
     *
     * @return string
     */
    public function getSliderId(): string
    {
        return 'sigma-slider-' . $this->getNameInLayout() . '-' . uniqid();
    }

    /**
     * Check if lazy loading is enabled
     *
     * @return bool
     */
    public function isLazyLoadEnabled(): bool
    {
        return $this->sliderConfig->isLazyLoadEnabled();
    }

    /**
     * Check if add to cart should be shown
     *
     * @return bool
     */
    public function showAddToCart(): bool
    {
        return $this->getSliderConfig()->isShowAddToCart();
    }

    /**
     * Check if wishlist should be shown
     *
     * @return bool
     */
    public function showWishlist(): bool
    {
        return $this->getSliderConfig()->isShowWishlist();
    }

    /**
     * Check if compare should be shown
     *
     * @return bool
     */
    public function showCompare(): bool
    {
        return $this->getSliderConfig()->isShowCompare();
    }

    /**
     * @inheritdoc
     */
    public function getIdentities(): array
    {
        $identities = [self::CACHE_TAG];
        
        foreach ($this->getProducts() as $product) {
            $identities[] = \Magento\Catalog\Model\Product::CACHE_TAG . '_' . $product->getId();
        }
        
        return $identities;
    }

    /**
     * @inheritdoc
     */
    public function getCacheKeyInfo(): array
    {
        $cacheKey = parent::getCacheKeyInfo();
        $cacheKey['slider_config'] = md5($this->jsonSerializer->serialize($this->getData()));
        $cacheKey['store_id'] = $this->_storeManager->getStore()->getId();
        return $cacheKey;
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml(): string
    {
        if (!$this->isEnabled()) {
            return '';
        }
        
        if (!$this->hasProducts()) {
            return '';
        }
        
        return parent::_toHtml();
    }
}
