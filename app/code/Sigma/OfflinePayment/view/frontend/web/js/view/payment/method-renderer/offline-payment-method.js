define([
    'Magento_Checkout/js/view/payment/default'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Sigma_OfflinePayment/payment/offline-payment'
        },

        /**
         * Get payment method code
         * @returns {String}
         */
        getCode: function () {
            return 'sigma_offline_payment';
        },

        /**
         * Check if payment method is active
         * @returns {Boolean}
         */
        isActive: function () {
            return true;
        },

        /**
         * Get payment instructions from config
         * @returns {String}
         */
        getInstructions: function () {
            return window.checkoutConfig.payment.instructions
                ? window.checkoutConfig.payment.instructions[this.getCode()]
                : '';
        }
    });
});
