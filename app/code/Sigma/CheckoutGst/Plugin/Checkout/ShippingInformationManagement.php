<?php
/**
 * Plugin for saving GST during shipping information save
 *
 * @category  Sigma
 * @package   Sigma_CheckoutGst
 */

declare(strict_types=1);

namespace Sigma\CheckoutGst\Plugin\Checkout;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Api\ShippingInformationManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Saves company GST number to quote during checkout
 */
class ShippingInformationManagement
{
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $quoteRepository;

    /**
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(CartRepositoryInterface $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Save GST to quote before shipping information is processed
     *
     * @param ShippingInformationManagementInterface $subject
     * @param int $cartId
     * @param ShippingInformationInterface $addressInformation
     * @return array
     */
    public function beforeSaveAddressInformation(
        ShippingInformationManagementInterface $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ): array {
        $shippingAddress = $addressInformation->getShippingAddress();
        $gstNo = $this->extractGstNumber($shippingAddress);

        if ($gstNo) {
            $quote = $this->quoteRepository->getActive($cartId);
            $quote->setData('company_gst_no', strtoupper($gstNo));
            $this->quoteRepository->save($quote);
        }

        return [$cartId, $addressInformation];
    }

    /**
     * Extract GST number from shipping address
     *
     * @param mixed $shippingAddress
     * @return string|null
     */
    private function extractGstNumber($shippingAddress): ?string
    {
        if (!$shippingAddress) {
            return null;
        }

        // Try extension attributes
        $extAttr = $shippingAddress->getExtensionAttributes();
        if ($extAttr && method_exists($extAttr, 'getCompanyGstNo')) {
            $gst = $extAttr->getCompanyGstNo();
            if ($gst) {
                return $gst;
            }
        }

        // Try custom attributes
        $customAttr = $shippingAddress->getCustomAttributes();
        if (is_array($customAttr)) {
            foreach ($customAttr as $key => $attr) {
                if (is_object($attr) && method_exists($attr, 'getAttributeCode')) {
                    if ($attr->getAttributeCode() === 'company_gst_no') {
                        return $attr->getValue();
                    }
                } elseif ($key === 'company_gst_no') {
                    return is_array($attr) ? ($attr['value'] ?? null) : $attr;
                }
            }
        }

        // Try getData
        $data = $shippingAddress->getData('custom_attributes');
        if (is_array($data) && isset($data['company_gst_no'])) {
            return is_array($data['company_gst_no']) 
                ? ($data['company_gst_no']['value'] ?? null) 
                : $data['company_gst_no'];
        }

        return null;
    }
}
