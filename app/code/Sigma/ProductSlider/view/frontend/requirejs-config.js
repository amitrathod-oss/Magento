/**
 * Sigma ProductSlider RequireJS Configuration
 *
 * @category  Sigma
 * @package   Sigma_ProductSlider
 */

var config = {
    map: {
        '*': {
            sigmaProductSlider: 'Sigma_ProductSlider/js/product-slider',
            slick: 'Sigma_ProductSlider/js/lib/slick.min'
        }
    },
    shim: {
        slick: {
            deps: ['jquery']
        }
    }
};
