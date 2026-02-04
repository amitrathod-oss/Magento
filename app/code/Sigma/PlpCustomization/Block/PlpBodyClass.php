<?php
/**
 * PLP Body Class Block
 *
 * @category  Sigma
 * @package   Sigma_PlpCustomization
 */

declare(strict_types=1);

namespace Sigma\PlpCustomization\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Sigma\PlpCustomization\Model\Config;

/**
 * Class PlpBodyClass
 *
 * Block to add CSS classes to body based on configuration
 */
class PlpBodyClass extends Template
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $data);
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
     * Check if wishlist should be hidden
     *
     * @return bool
     */
    public function isHideWishlist(): bool
    {
        return $this->config->isEnabled() && !$this->config->isShowWishlist();
    }

    /**
     * Check if compare should be hidden
     *
     * @return bool
     */
    public function isHideCompare(): bool
    {
        return $this->config->isEnabled() && !$this->config->isShowCompare();
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
}
