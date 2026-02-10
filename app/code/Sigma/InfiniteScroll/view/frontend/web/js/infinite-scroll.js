define([
    'jquery',
    'mage/url'
], function ($, urlBuilder) {
    'use strict';

    return function (config, element) {
        var threshold = config.threshold || 300,
            isLoading = false,
            isFinished = false,
            currentPage = 1,
            observer = null,
            productListSelector = '.products.wrapper .product-items',
            toolbarSelector = '.toolbar.toolbar-products',
            pagerSelector = '.toolbar .pages',
            nextPageSelector = '.pages-item-next a',
            loaderHtml = '<div class="sigma-infinite-loader" id="sigma-infinite-loader">' +
                '<div class="sigma-spinner"></div>' +
                '<span>Loading more products...</span></div>';

        /**
         * Initialize infinite scroll
         */
        function init() {
            var $productList = $(productListSelector);

            if (!$productList.length) {
                return;
            }

            // Hide bottom pagination (keep top toolbar for sorting/view mode)
            hidePagination();

            // Add loader element
            $productList.closest('.products.wrapper').after(loaderHtml);

            // Check if there's a next page
            if (!getNextPageUrl()) {
                isFinished = true;
                return;
            }

            // Use Intersection Observer for performance
            if ('IntersectionObserver' in window) {
                initIntersectionObserver();
            } else {
                initScrollFallback();
            }
        }

        /**
         * Hide pagination from bottom toolbar only
         */
        function hidePagination() {
            var $toolbars = $(toolbarSelector);
            if ($toolbars.length > 1) {
                // Hide the bottom toolbar entirely (pagination + amounts)
                $toolbars.last().addClass('sigma-hidden-pagination');
            }

            // Also hide page links from top toolbar but keep sorter
            $(pagerSelector).addClass('sigma-hidden-pagination');
        }

        /**
         * Get next page URL from pagination links
         *
         * @returns {string|null}
         */
        function getNextPageUrl() {
            var $nextLink = $(nextPageSelector);
            if ($nextLink.length) {
                return $nextLink.attr('href');
            }
            return null;
        }

        /**
         * Initialize Intersection Observer (modern, performant)
         */
        function initIntersectionObserver() {
            var sentinel = document.getElementById('sigma-infinite-scroll-sentinel');

            if (!sentinel) {
                return;
            }

            observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting && !isLoading && !isFinished) {
                        loadNextPage();
                    }
                });
            }, {
                rootMargin: threshold + 'px'
            });

            observer.observe(sentinel);
        }

        /**
         * Fallback scroll handler for older browsers
         */
        function initScrollFallback() {
            var scrollTimer = null;

            $(window).on('scroll.sigmaInfinite', function () {
                if (scrollTimer) {
                    clearTimeout(scrollTimer);
                }
                scrollTimer = setTimeout(function () {
                    if (isLoading || isFinished) {
                        return;
                    }
                    var scrollPosition = $(window).scrollTop() + $(window).height(),
                        documentHeight = $(document).height();

                    if (scrollPosition >= documentHeight - threshold) {
                        loadNextPage();
                    }
                }, 100);
            });
        }

        /**
         * Fetch and append next page of products
         */
        function loadNextPage() {
            var nextUrl = getNextPageUrl();

            if (!nextUrl || isLoading) {
                return;
            }

            isLoading = true;
            currentPage++;
            showLoader();

            $.ajax({
                url: nextUrl,
                type: 'GET',
                dataType: 'html',
                success: function (response) {
                    var $response = $('<div/>').html(response),
                        $newProducts = $response.find(productListSelector + ' > li'),
                        $newPager = $response.find(pagerSelector),
                        $currentList = $(productListSelector);

                    if ($newProducts.length) {
                        // Append new products with fade-in animation
                        $newProducts.addClass('sigma-product-fadein');
                        $currentList.append($newProducts);

                        // Trigger content updated for Magento JS widgets
                        $('body').trigger('contentUpdated');

                        // Update pagination in DOM (hidden) for next page detection
                        updatePagination($response);

                        // Trigger animation
                        setTimeout(function () {
                            $newProducts.addClass('sigma-product-visible');
                        }, 50);

                        // Check if more pages exist
                        if (!$response.find(nextPageSelector).length) {
                            isFinished = true;
                            showEndMessage();
                            destroyObserver();
                        }
                    } else {
                        isFinished = true;
                        showEndMessage();
                        destroyObserver();
                    }
                },
                error: function () {
                    isFinished = true;
                    console.error('Sigma InfiniteScroll: Failed to load next page.');
                },
                complete: function () {
                    isLoading = false;
                    hideLoader();
                }
            });
        }

        /**
         * Update pagination links in DOM for next page detection
         *
         * @param {jQuery} $response
         */
        function updatePagination($response) {
            var $newPager = $response.find(pagerSelector).first(),
                $existingPager = $(pagerSelector).first();

            if ($newPager.length && $existingPager.length) {
                $existingPager.html($newPager.html());
            } else if ($newPager.length) {
                $(toolbarSelector).first().append($newPager);
                $newPager.addClass('sigma-hidden-pagination');
            }
        }

        /**
         * Show loading spinner
         */
        function showLoader() {
            $('#sigma-infinite-loader').addClass('active');
        }

        /**
         * Hide loading spinner
         */
        function hideLoader() {
            $('#sigma-infinite-loader').removeClass('active');
        }

        /**
         * Show end of products message
         */
        function showEndMessage() {
            var endHtml = '<div class="sigma-infinite-end">' +
                '<span>All products loaded</span></div>';
            $('#sigma-infinite-loader').replaceWith(endHtml);
        }

        /**
         * Clean up observer
         */
        function destroyObserver() {
            if (observer) {
                observer.disconnect();
                observer = null;
            }
            $(window).off('scroll.sigmaInfinite');
        }

        // Initialize on DOM ready
        $(function () {
            init();
        });
    };
});
