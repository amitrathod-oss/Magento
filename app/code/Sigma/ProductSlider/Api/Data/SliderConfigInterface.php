<?php
/**
 * Slider Configuration Interface
 *
 * @category  Sigma
 * @package   Sigma_ProductSlider
 */

declare(strict_types=1);

namespace Sigma\ProductSlider\Api\Data;

/**
 * Interface SliderConfigInterface
 *
 * Data interface for slider configuration
 */
interface SliderConfigInterface
{
    /**
     * Configuration keys
     */
    public const TITLE = 'title';
    public const PRODUCT_SOURCE = 'product_source';
    public const CATEGORY_IDS = 'category_ids';
    public const PRODUCT_COUNT = 'product_count';
    public const SLIDES_TO_SHOW = 'slides_to_show';
    public const SLIDES_TO_SHOW_TABLET = 'slides_to_show_tablet';
    public const SLIDES_TO_SHOW_MOBILE = 'slides_to_show_mobile';
    public const AUTOPLAY = 'autoplay';
    public const AUTOPLAY_SPEED = 'autoplay_speed';
    public const SHOW_NAV = 'show_nav';
    public const SHOW_DOTS = 'show_dots';
    public const INFINITE_LOOP = 'infinite_loop';
    public const SHOW_ADD_TO_CART = 'show_add_to_cart';
    public const SHOW_WISHLIST = 'show_wishlist';
    public const SHOW_COMPARE = 'show_compare';

    /**
     * Get slider title
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Set slider title
     *
     * @param string $title
     * @return SliderConfigInterface
     */
    public function setTitle(string $title): SliderConfigInterface;

    /**
     * Get product source type
     *
     * @return string
     */
    public function getProductSource(): string;

    /**
     * Set product source type
     *
     * @param string $source
     * @return SliderConfigInterface
     */
    public function setProductSource(string $source): SliderConfigInterface;

    /**
     * Get category IDs
     *
     * @return array
     */
    public function getCategoryIds(): array;

    /**
     * Set category IDs
     *
     * @param array $categoryIds
     * @return SliderConfigInterface
     */
    public function setCategoryIds(array $categoryIds): SliderConfigInterface;

    /**
     * Get product count
     *
     * @return int
     */
    public function getProductCount(): int;

    /**
     * Set product count
     *
     * @param int $count
     * @return SliderConfigInterface
     */
    public function setProductCount(int $count): SliderConfigInterface;

    /**
     * Get slides to show (desktop)
     *
     * @return int
     */
    public function getSlidesToShow(): int;

    /**
     * Get slides to show for tablet
     *
     * @return int
     */
    public function getSlidesToShowTablet(): int;

    /**
     * Get slides to show for mobile
     *
     * @return int
     */
    public function getSlidesToShowMobile(): int;

    /**
     * Check if autoplay is enabled
     *
     * @return bool
     */
    public function isAutoplay(): bool;

    /**
     * Get autoplay speed
     *
     * @return int
     */
    public function getAutoplaySpeed(): int;

    /**
     * Check if navigation arrows should be shown
     *
     * @return bool
     */
    public function isShowNav(): bool;

    /**
     * Check if pagination dots should be shown
     *
     * @return bool
     */
    public function isShowDots(): bool;

    /**
     * Check if infinite loop is enabled
     *
     * @return bool
     */
    public function isInfiniteLoop(): bool;

    /**
     * Check if add to cart button should be shown
     *
     * @return bool
     */
    public function isShowAddToCart(): bool;

    /**
     * Check if wishlist button should be shown
     *
     * @return bool
     */
    public function isShowWishlist(): bool;

    /**
     * Check if compare button should be shown
     *
     * @return bool
     */
    public function isShowCompare(): bool;

    /**
     * Get configuration as array for JavaScript
     *
     * @return array
     */
    public function toJsConfig(): array;
}
