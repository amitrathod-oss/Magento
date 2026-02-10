<?php

declare(strict_types=1);

namespace Sigma\OfflinePayment\Model;

use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Framework\DataObject;
use Magento\Payment\Model\InfoInterface;

class OfflinePayment extends AbstractMethod
{
    /**
     * Payment method code
     */
    public const CODE = 'sigma_offline_payment';

    /**
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * @var bool
     */
    protected $_isOffline = true;

    /**
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * @var bool
     */
    protected $_canCapturePartial = false;

    /**
     * @var bool
     */
    protected $_canRefund = false;

    /**
     * @var bool
     */
    protected $_canUseInternal = true;

    /**
     * @var bool
     */
    protected $_canUseCheckout = true;

    /**
     * Authorize payment
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function authorize(InfoInterface $payment, $amount): self
    {
        $payment->setTransactionId('offline_auth_' . time());
        $payment->setIsTransactionClosed(false);
        return $this;
    }

    /**
     * Capture payment
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function capture(InfoInterface $payment, $amount): self
    {
        $payment->setTransactionId('offline_capture_' . time());
        $payment->setIsTransactionClosed(true);
        return $this;
    }

    /**
     * Check whether payment method is applicable to quote
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null): bool
    {
        return parent::isAvailable($quote);
    }

    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions(): string
    {
        return (string) $this->getConfigData('instructions');
    }
}
