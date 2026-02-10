<?php

declare(strict_types=1);

namespace Sigma\OfflinePayment\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Provide checkout config for payment instructions
     *
     * @return array
     */
    public function getConfig(): array
    {
        $isActive = $this->scopeConfig->isSetFlag(
            'payment/sigma_offline_payment/active',
            ScopeInterface::SCOPE_STORE
        );

        if (!$isActive) {
            return [];
        }

        return [
            'payment' => [
                'instructions' => [
                    OfflinePayment::CODE => $this->scopeConfig->getValue(
                        'payment/sigma_offline_payment/instructions',
                        ScopeInterface::SCOPE_STORE
                    )
                ]
            ]
        ];
    }
}
