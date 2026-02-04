<?php
/**
 * PLP Customization View Model
 *
 * @category  Sigma
 * @package   Sigma_PlpCustomization
 */

declare(strict_types=1);

namespace Sigma\PlpCustomization\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Sigma\PlpCustomization\Model\Config;

/**
 * Class PlpConfig
 *
 * ViewModel for PLP customization configuration
 */
class PlpConfig implements ArgumentInterface
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * Constructor
     *
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Check if module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isEnabled();
    }

    /**
     * Check if wishlist should be shown
     *
     * @return bool
     */
    public function isShowWishlist(): bool
    {
        return $this->config->isShowWishlist();
    }

    /**
     * Check if compare should be shown
     *
     * @return bool
     */
    public function isShowCompare(): bool
    {
        return $this->config->isShowCompare();
    }

    /**
     * Check if compare sidebar should be hidden
     *
     * @return bool
     */
    public function isHideCompareSidebar(): bool
    {
        return $this->config->isHideCompareSidebar();
    }

    /**
     * Check if wishlist sidebar should be hidden
     *
     * @return bool
     */
    public function isHideWishlistSidebar(): bool
    {
        return $this->config->isHideWishlistSidebar();
    }

    /**
     * Get CSS class based on configuration
     *
     * @return string
     */
    public function getBodyClass(): string
    {
        $classes = [];
        
        if ($this->isEnabled()) {
            if (!$this->isShowWishlist()) {
                $classes[] = 'sigma-hide-wishlist';
            }
            if (!$this->isShowCompare()) {
                $classes[] = 'sigma-hide-compare';
            }
        }
        
        return implode(' ', $classes);
    }
}
