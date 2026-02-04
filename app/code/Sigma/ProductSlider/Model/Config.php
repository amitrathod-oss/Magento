<?php
/**
 * Global Configuration Model
 *
 * @category  Sigma
 * @package   Sigma_ProductSlider
 */

declare(strict_types=1);

namespace Sigma\ProductSlider\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 *
 * Configuration model for global slider settings
 */
class Config
{
    /**
     * Configuration paths
     */
    private const XML_PATH_ENABLED = 'catalog/sigma_product_slider/enabled';
    private const XML_PATH_CACHE_LIFETIME = 'catalog/sigma_product_slider/cache_lifetime';
    private const XML_PATH_LAZY_LOAD = 'catalog/sigma_product_slider/lazy_load';
    private const XML_PATH_IMAGE_WIDTH = 'catalog/sigma_product_slider/default_image_width';
    private const XML_PATH_IMAGE_HEIGHT = 'catalog/sigma_product_slider/default_image_height';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if product slider is enabled globally
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get cache lifetime in seconds
     *
     * @param int|null $storeId
     * @return int
     */
    public function getCacheLifetime(?int $storeId = null): int
    {
        $lifetime = $this->scopeConfig->getValue(
            self::XML_PATH_CACHE_LIFETIME,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        return $lifetime !== null ? (int) $lifetime : 3600;
    }

    /**
     * Check if lazy loading is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isLazyLoadEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_LAZY_LOAD,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get default image width
     *
     * @param int|null $storeId
     * @return int
     */
    public function getDefaultImageWidth(?int $storeId = null): int
    {
        $width = $this->scopeConfig->getValue(
            self::XML_PATH_IMAGE_WIDTH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        return $width !== null ? (int) $width : 300;
    }

    /**
     * Get default image height
     *
     * @param int|null $storeId
     * @return int
     */
    public function getDefaultImageHeight(?int $storeId = null): int
    {
        $height = $this->scopeConfig->getValue(
            self::XML_PATH_IMAGE_HEIGHT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        return $height !== null ? (int) $height : 300;
    }
}
