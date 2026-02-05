<?php
/**
 * Checkout Layout Processor Plugin for Field Customizations
 *
 * @category  Sigma
 * @package   Sigma_CheckoutFields
 */

declare(strict_types=1);

namespace Sigma\CheckoutFields\Plugin\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor;

/**
 * Customizes checkout shipping address form fields
 *
 * - Street Address: 3 lines
 * - Middle Name: Optional (visible)
 * - Company: Required (shipping form only)
 * - Fax: Visible but NOT required
 */
class LayoutProcessorPlugin
{
    /**
     * Customize checkout field configurations
     *
     * @param LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(LayoutProcessor $subject, array $jsLayout): array
    {
        // Path to shipping address fields
        $shippingFieldsPath = &$jsLayout['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children'];

        if (!isset($shippingFieldsPath)) {
            return $jsLayout;
        }

        // 1. Street Address - 3 lines
        if (isset($shippingFieldsPath['street'])) {
            $shippingFieldsPath['street']['children'][0]['label'] = __('Street Address Line 1');
            $shippingFieldsPath['street']['children'][1]['label'] = __('Street Address Line 2');
            
            // Add third street line
            $shippingFieldsPath['street']['children'][2] = [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => 'shippingAddress',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input'
                ],
                'dataScope' => 2,
                'provider' => 'checkoutProvider',
                'label' => __('Street Address Line 3'),
                'visible' => true,
                'validation' => [],
                'sortOrder' => 72,
                'options' => [],
                'filterBy' => null,
                'customEntry' => null,
                'additionalClasses' => 'street-line-3'
            ];
        }

        // 2. Middle Name - Make visible and optional
        if (isset($shippingFieldsPath['middlename'])) {
            $shippingFieldsPath['middlename']['visible'] = true;
            $shippingFieldsPath['middlename']['validation'] = [];
            $shippingFieldsPath['middlename']['sortOrder'] = 15;
        } else {
            // Add middlename if not exists
            $shippingFieldsPath['middlename'] = [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => 'shippingAddress',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input'
                ],
                'dataScope' => 'shippingAddress.middlename',
                'label' => __('Middle Name'),
                'provider' => 'checkoutProvider',
                'sortOrder' => 15,
                'validation' => [],
                'visible' => true,
                'options' => [],
                'filterBy' => null,
                'customEntry' => null
            ];
        }

        // 3. Company - Make required ONLY on shipping form (not database level)
        if (isset($shippingFieldsPath['company'])) {
            $shippingFieldsPath['company']['validation'] = [
                'required-entry' => true
            ];
            $shippingFieldsPath['company']['sortOrder'] = 30;
        }

        // 4. Fax - Make visible but NOT required
        if (isset($shippingFieldsPath['fax'])) {
            $shippingFieldsPath['fax']['visible'] = true;
            $shippingFieldsPath['fax']['sortOrder'] = 125;
            $shippingFieldsPath['fax']['validation'] = [];
            $shippingFieldsPath['fax']['required'] = false;
        } else {
            // Add fax field if not exists
            $shippingFieldsPath['fax'] = [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => 'shippingAddress',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input'
                ],
                'dataScope' => 'shippingAddress.fax',
                'label' => __('Fax'),
                'provider' => 'checkoutProvider',
                'sortOrder' => 125,
                'validation' => [],
                'required' => false,
                'visible' => true,
                'options' => [],
                'filterBy' => null,
                'customEntry' => null
            ];
        }

        // Configure billing address fields - ensure fax and company are NOT required on billing
        // This prevents validation errors when "same as shipping" is checked
        $jsLayout = $this->processBillingAddressFields($jsLayout);

        return $jsLayout;
    }

    /**
     * Process billing address fields for all payment methods
     * Make sure fax and company are NOT required on billing to prevent validation errors
     *
     * @param array $jsLayout
     * @return array
     */
    private function processBillingAddressFields(array $jsLayout): array
    {
        // Path to payment methods
        $paymentMethodsPath = $jsLayout['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']['payments-list']['children'] ?? null;

        if ($paymentMethodsPath) {
            foreach ($paymentMethodsPath as $paymentCode => $paymentMethod) {
                // Check if this payment method has a form container with billing address
                if (isset($paymentMethod['children']['form-fields']['children'])) {
                    $billingFields = $paymentMethod['children']['form-fields']['children'];
                    $this->customizeBillingFields($billingFields);
                    $jsLayout['components']['checkout']['children']['steps']['children']
                        ['billing-step']['children']['payment']['children']['payments-list']['children']
                        [$paymentCode]['children']['form-fields']['children'] = $billingFields;
                }
            }
        }

        // Also check for shared billing address form (afterMethods)
        $sharedBillingPath = $jsLayout['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']['afterMethods']['children'] ?? null;

        if ($sharedBillingPath) {
            foreach ($sharedBillingPath as $methodCode => $method) {
                if (isset($method['children']['form-fields']['children'])) {
                    $billingFields = $method['children']['form-fields']['children'];
                    $this->customizeBillingFields($billingFields);
                    $jsLayout['components']['checkout']['children']['steps']['children']
                        ['billing-step']['children']['payment']['children']['afterMethods']['children']
                        [$methodCode]['children']['form-fields']['children'] = $billingFields;
                }
            }
        }

        return $jsLayout;
    }

    /**
     * Customize billing address fields
     * Remove required validation from fax and company to prevent errors with "same as shipping"
     *
     * @param array $billingFields
     * @return void
     */
    private function customizeBillingFields(array &$billingFields): void
    {
        // Make fax NOT required in billing
        if (isset($billingFields['fax'])) {
            $billingFields['fax']['validation'] = [];
            $billingFields['fax']['required'] = false;
            $billingFields['fax']['visible'] = true;
        }

        // Make company NOT required in billing (to work with "same as shipping")
        if (isset($billingFields['company'])) {
            $billingFields['company']['validation'] = [];
            $billingFields['company']['required'] = false;
        }

        // Handle street lines in billing
        if (isset($billingFields['street'])) {
            if (isset($billingFields['street']['children'][0])) {
                $billingFields['street']['children'][0]['label'] = __('Street Address Line 1');
            }
            if (isset($billingFields['street']['children'][1])) {
                $billingFields['street']['children'][1]['label'] = __('Street Address Line 2');
            }
        }
    }
}
