<?php

declare(strict_types=1);

namespace Sigma\InfiniteScroll\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class InfiniteScroll extends Template
{
    /**
     * Check if infinite scroll is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->_scopeConfig->isSetFlag(
            'sigma_infinitescroll/general/enabled',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get scroll threshold in pixels
     *
     * @return int
     */
    public function getThreshold(): int
    {
        return (int) $this->_scopeConfig->getValue(
            'sigma_infinitescroll/general/threshold',
            ScopeInterface::SCOPE_STORE
        ) ?: 300;
    }

    /**
     * Get JSON config for JS component
     *
     * @return string
     */
    public function getJsConfig(): string
    {
        return json_encode([
            'threshold' => $this->getThreshold()
        ]);
    }
}
