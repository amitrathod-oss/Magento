<?php
/**
 * PLP Customization Configuration Model
 *
 * @category  Sigma
 * @package   Sigma_PlpCustomization
 */

declare(strict_types=1);

namespace Sigma\PlpCustomization\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 *
 * Configuration helper for PLP customization settings
 */
class Config
{
    /**
     * Configuration paths
     */
    private const XML_PATH_ENABLED = 'catalog/plp_customization/enabled';
    private const XML_PATH_SHOW_WISHLIST = 'catalog/plp_customization/show_wishlist';
    private const XML_PATH_SHOW_COMPARE = 'catalog/plp_customization/show_compare';
    private const XML_PATH_HIDE_COMPARE_SIDEBAR = 'catalog/plp_customization/hide_compare_sidebar';
    private const XML_PATH_HIDE_WISHLIST_SIDEBAR = 'catalog/plp_customization/hide_wishlist_sidebar';

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
     * Check if module is enabled
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
     * Check if wishlist should be shown on PLP
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isShowWishlist(?int $storeId = null): bool
    {
        if (!$this->isEnabled($storeId)) {
            return true; // Default behavior when module disabled
        }
        
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHOW_WISHLIST,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if compare should be shown on PLP
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isShowCompare(?int $storeId = null): bool
    {
        if (!$this->isEnabled($storeId)) {
            return true; // Default behavior when module disabled
        }
        
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHOW_COMPARE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if compare sidebar should be hidden
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isHideCompareSidebar(?int $storeId = null): bool
    {
        if (!$this->isEnabled($storeId)) {
            return false;
        }
        
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_HIDE_COMPARE_SIDEBAR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if wishlist sidebar should be hidden
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isHideWishlistSidebar(?int $storeId = null): bool
    {
        if (!$this->isEnabled($storeId)) {
            return false;
        }
        
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_HIDE_WISHLIST_SIDEBAR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
