<?php
/**
 * Checkout Layout Processor Plugin
 *
 * @category  Sigma
 * @package   Sigma_CheckoutGst
 */

declare(strict_types=1);

namespace Sigma\CheckoutGst\Plugin\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor;

/**
 * Adds Company GST No field to checkout shipping form
 */
class LayoutProcessorPlugin
{
    /**
     * Add GST field to shipping address form
     *
     * @param LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(LayoutProcessor $subject, array $jsLayout): array
    {
        $gstField = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
                'id' => 'company_gst_no'
            ],
            'dataScope' => 'shippingAddress.custom_attributes.company_gst_no',
            'label' => 'Company GST No',
            'provider' => 'checkoutProvider',
            'sortOrder' => 150,
            'validation' => [
                'required-entry' => true
            ],
            'visible' => true,
            'value' => ''
        ];

        $fieldsetPath = &$jsLayout['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children'];

        if (isset($fieldsetPath)) {
            $fieldsetPath['company_gst_no'] = $gstField;
        }

        return $jsLayout;
    }
}
