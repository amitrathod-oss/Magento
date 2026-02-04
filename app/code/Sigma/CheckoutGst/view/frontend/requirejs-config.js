/**
 * Sigma CheckoutGst RequireJS Configuration
 *
 * @category  Sigma
 * @package   Sigma_CheckoutGst
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'Sigma_CheckoutGst/js/action/set-shipping-information-mixin': true
            }
        }
    }
};
