/**
 * Mixin for set-shipping-information action
 *
 * @category  Sigma
 * @package   Sigma_CheckoutGst
 */

define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, quote) {
    'use strict';

    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var shippingAddress = quote.shippingAddress(),
                gstValue = null;

            // Get GST from form input
            var $gstInput = $('input[name="custom_attributes[company_gst_no]"]');
            if ($gstInput.length && $gstInput.val()) {
                gstValue = $gstInput.val().toUpperCase();
            }

            // Set to extension attributes for backend processing
            if (gstValue && shippingAddress) {
                if (!shippingAddress.extensionAttributes) {
                    shippingAddress.extensionAttributes = {};
                }
                shippingAddress.extensionAttributes.company_gst_no = gstValue;

                // Set to customAttributes array
                if (!shippingAddress.customAttributes) {
                    shippingAddress.customAttributes = [];
                }

                var found = false;
                if (Array.isArray(shippingAddress.customAttributes)) {
                    shippingAddress.customAttributes.forEach(function (attr, index) {
                        if (attr.attribute_code === 'company_gst_no') {
                            shippingAddress.customAttributes[index].value = gstValue;
                            found = true;
                        }
                    });
                    if (!found) {
                        shippingAddress.customAttributes.push({
                            attribute_code: 'company_gst_no',
                            value: gstValue
                        });
                    }
                }
            }

            return originalAction();
        });
    };
});
