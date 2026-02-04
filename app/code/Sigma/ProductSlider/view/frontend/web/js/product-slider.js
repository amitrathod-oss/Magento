/**
 * Sigma Product Slider
 * Clean, Minimal JavaScript
 *
 * @category  Sigma
 * @package   Sigma_ProductSlider
 */

define([
    'jquery',
    'slick'
], function ($) {
    'use strict';

    $.widget('sigma.productSlider', {

        options: {
            slidesToShow: 4,
            slidesToShowTablet: 3,
            slidesToShowMobile: 2,
            slidesToScroll: 1,
            autoplay: false,
            autoplaySpeed: 3000,
            arrows: true,
            dots: true,
            infinite: true,
            speed: 400
        },

        /**
         * Initialize widget
         */
        _create: function () {
            this._initSlider();
            this._initLazyLoad();
        },

        /**
         * Initialize Slick slider
         */
        _initSlider: function () {
            var self = this;

            if (this.element.hasClass('slick-initialized')) {
                return;
            }

            this.element.slick({
                slidesToShow: this._toInt(this.options.slidesToShow, 4),
                slidesToScroll: this._toInt(this.options.slidesToScroll, 1),
                autoplay: this._toBool(this.options.autoplay),
                autoplaySpeed: this._toInt(this.options.autoplaySpeed, 3000),
                arrows: this._toBool(this.options.arrows),
                dots: this._toBool(this.options.dots),
                infinite: this._toBool(this.options.infinite),
                speed: this._toInt(this.options.speed, 400),
                cssEase: 'ease',
                pauseOnHover: true,
                swipe: true,
                touchMove: true,
                prevArrow: '<button type="button" class="slick-prev" aria-label="Previous"></button>',
                nextArrow: '<button type="button" class="slick-next" aria-label="Next"></button>',
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: this._toInt(this.options.slidesToShowTablet, 3)
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: this._toInt(this.options.slidesToShowMobile, 2)
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1,
                            arrows: true,
                            dots: true
                        }
                    }
                ]
            });
        },

        /**
         * Initialize lazy loading
         */
        _initLazyLoad: function () {
            var self = this;

            this.element.find('img.lazy').each(function () {
                var $img = $(this);
                var src = $img.data('src');

                if (src) {
                    var img = new Image();
                    img.onload = function () {
                        $img.attr('src', src).addClass('loaded');
                    };
                    img.src = src;
                }
            });
        },

        /**
         * Convert to integer
         */
        _toInt: function (val, def) {
            var num = parseInt(val, 10);
            return isNaN(num) ? def : num;
        },

        /**
         * Convert to boolean
         */
        _toBool: function (val) {
            if (typeof val === 'boolean') return val;
            if (typeof val === 'string') return val === 'true' || val === '1';
            return !!val;
        },

        /**
         * Destroy widget
         */
        _destroy: function () {
            if (this.element.hasClass('slick-initialized')) {
                this.element.slick('unslick');
            }
        }
    });

    return $.sigma.productSlider;
});
