<?php
/**
 * Slider Configuration Data Model
 *
 * @category  Sigma
 * @package   Sigma_ProductSlider
 */

declare(strict_types=1);

namespace Sigma\ProductSlider\Model\Data;

use Magento\Framework\DataObject;
use Sigma\ProductSlider\Api\Data\SliderConfigInterface;

/**
 * Class SliderConfig
 *
 * Data transfer object for slider configuration
 */
class SliderConfig extends DataObject implements SliderConfigInterface
{
    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return (string) $this->getData(self::TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setTitle(string $title): SliderConfigInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @inheritdoc
     */
    public function getProductSource(): string
    {
        $source = $this->getData(self::PRODUCT_SOURCE);
        return $source !== null ? (string) $source : 'category';
    }

    /**
     * @inheritdoc
     */
    public function setProductSource(string $source): SliderConfigInterface
    {
        return $this->setData(self::PRODUCT_SOURCE, $source);
    }

    /**
     * @inheritdoc
     */
    public function getCategoryIds(): array
    {
        $ids = $this->getData(self::CATEGORY_IDS);
        
        if (empty($ids)) {
            return [];
        }
        
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        
        return array_map('intval', array_filter($ids));
    }

    /**
     * @inheritdoc
     */
    public function setCategoryIds(array $categoryIds): SliderConfigInterface
    {
        return $this->setData(self::CATEGORY_IDS, $categoryIds);
    }

    /**
     * @inheritdoc
     */
    public function getProductCount(): int
    {
        $count = $this->getData(self::PRODUCT_COUNT);
        return $count !== null ? (int) $count : 10;
    }

    /**
     * @inheritdoc
     */
    public function setProductCount(int $count): SliderConfigInterface
    {
        return $this->setData(self::PRODUCT_COUNT, $count);
    }

    /**
     * @inheritdoc
     */
    public function getSlidesToShow(): int
    {
        $slides = $this->getData(self::SLIDES_TO_SHOW);
        return $slides !== null ? (int) $slides : 4;
    }

    /**
     * @inheritdoc
     */
    public function getSlidesToShowTablet(): int
    {
        $slides = $this->getData(self::SLIDES_TO_SHOW_TABLET);
        return $slides !== null ? (int) $slides : 3;
    }

    /**
     * @inheritdoc
     */
    public function getSlidesToShowMobile(): int
    {
        $slides = $this->getData(self::SLIDES_TO_SHOW_MOBILE);
        return $slides !== null ? (int) $slides : 2;
    }

    /**
     * @inheritdoc
     */
    public function isAutoplay(): bool
    {
        return (bool) $this->getData(self::AUTOPLAY);
    }

    /**
     * @inheritdoc
     */
    public function getAutoplaySpeed(): int
    {
        $speed = $this->getData(self::AUTOPLAY_SPEED);
        return $speed !== null ? (int) $speed : 3000;
    }

    /**
     * @inheritdoc
     */
    public function isShowNav(): bool
    {
        $showNav = $this->getData(self::SHOW_NAV);
        return $showNav === null || (bool) $showNav;
    }

    /**
     * @inheritdoc
     */
    public function isShowDots(): bool
    {
        return (bool) $this->getData(self::SHOW_DOTS);
    }

    /**
     * @inheritdoc
     */
    public function isInfiniteLoop(): bool
    {
        $infinite = $this->getData(self::INFINITE_LOOP);
        return $infinite === null || (bool) $infinite;
    }

    /**
     * @inheritdoc
     */
    public function isShowAddToCart(): bool
    {
        $show = $this->getData(self::SHOW_ADD_TO_CART);
        return $show === null || (bool) $show;
    }

    /**
     * @inheritdoc
     */
    public function isShowWishlist(): bool
    {
        $show = $this->getData(self::SHOW_WISHLIST);
        return $show === null || (bool) $show;
    }

    /**
     * @inheritdoc
     */
    public function isShowCompare(): bool
    {
        $show = $this->getData(self::SHOW_COMPARE);
        return $show === null || (bool) $show;
    }

    /**
     * @inheritdoc
     */
    public function toJsConfig(): array
    {
        return [
            'slidesToShow' => $this->getSlidesToShow(),
            'slidesToShowTablet' => $this->getSlidesToShowTablet(),
            'slidesToShowMobile' => $this->getSlidesToShowMobile(),
            'autoplay' => $this->isAutoplay(),
            'autoplaySpeed' => $this->getAutoplaySpeed(),
            'arrows' => $this->isShowNav(),
            'dots' => $this->isShowDots(),
            'infinite' => $this->isInfiniteLoop(),
            'slidesToScroll' => 1,
            'responsive' => [
                [
                    'breakpoint' => 1024,
                    'settings' => [
                        'slidesToShow' => $this->getSlidesToShowTablet(),
                        'slidesToScroll' => 1
                    ]
                ],
                [
                    'breakpoint' => 768,
                    'settings' => [
                        'slidesToShow' => $this->getSlidesToShowMobile(),
                        'slidesToScroll' => 1
                    ]
                ]
            ]
        ];
    }
}
