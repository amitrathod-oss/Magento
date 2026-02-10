define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {
    'use strict';

    rendererList.push({
        type: 'sigma_offline_payment',
        component: 'Sigma_OfflinePayment/js/view/payment/method-renderer/offline-payment-method'
    });

    return Component.extend({});
});
