<?php

declare(strict_types=1);

namespace Sigma\CustomSupport\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class SupportForm extends Template
{
    public function getFormAction(): string
    {
        return $this->getUrl('customersupport/index/save');
    }

    public function isRecaptchaEnabled(): bool
    {
        return $this->_scopeConfig->isSetFlag(
            'sigma_customsupport/recaptcha/enabled',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getRecaptchaSiteKey(): string
    {
        return (string) $this->_scopeConfig->getValue(
            'sigma_customsupport/recaptcha/site_key',
            ScopeInterface::SCOPE_STORE
        );
    }
}
