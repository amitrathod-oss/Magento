<?php
/**
 * Product Collection Provider Interface
 *
 * @category  Sigma
 * @package   Sigma_ProductSlider
 */

declare(strict_types=1);

namespace Sigma\ProductSlider\Api;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Sigma\ProductSlider\Api\Data\SliderConfigInterface;

/**
 * Interface ProductCollectionProviderInterface
 *
 * Provides product collections for the slider
 */
interface ProductCollectionProviderInterface
{
    /**
     * Product source types
     */
    public const SOURCE_CATEGORY = 'category';
    public const SOURCE_NEW = 'new';
    public const SOURCE_BESTSELLER = 'bestseller';
    public const SOURCE_FEATURED = 'featured';
    public const SOURCE_SALE = 'sale';
    public const SOURCE_RANDOM = 'random';

    /**
     * Get product collection based on configuration
     *
     * @param SliderConfigInterface $config
     * @return Collection
     */
    public function getCollection(SliderConfigInterface $config): Collection;

    /**
     * Get products as array
     *
     * @param SliderConfigInterface $config
     * @return array
     */
    public function getProducts(SliderConfigInterface $config): array;
}
