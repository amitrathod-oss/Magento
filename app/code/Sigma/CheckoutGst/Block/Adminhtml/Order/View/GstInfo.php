<?php
/**
 * GST Info Block for Admin Order View
 *
 * @category  Sigma
 * @package   Sigma_CheckoutGst
 */

declare(strict_types=1);

namespace Sigma\CheckoutGst\Block\Adminhtml\Order\View;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order;

/**
 * Block to display GST information on admin order view
 */
class GstInfo extends Template
{
    /**
     * @var Registry
     */
    private Registry $registry;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get current order
     *
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->registry->registry('current_order');
    }

    /**
     * Get Company GST Number
     *
     * @return string
     */
    public function getCompanyGstNo(): string
    {
        $order = $this->getOrder();
        return $order ? (string) $order->getData('company_gst_no') : '';
    }

    /**
     * Check if GST number exists
     *
     * @return bool
     */
    public function hasGstNo(): bool
    {
        return !empty($this->getCompanyGstNo());
    }
}
