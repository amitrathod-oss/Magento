<?php
/**
 * Product Source Source Model
 *
 * @category  Sigma
 * @package   Sigma_ProductSlider
 */

declare(strict_types=1);

namespace Sigma\ProductSlider\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Sigma\ProductSlider\Api\ProductCollectionProviderInterface;

/**
 * Class ProductSource
 *
 * Provides options for product source selection
 */
class ProductSource implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => ProductCollectionProviderInterface::SOURCE_CATEGORY,
                'label' => __('From Selected Categories')
            ],
            [
                'value' => ProductCollectionProviderInterface::SOURCE_NEW,
                'label' => __('New Products')
            ],
            [
                'value' => ProductCollectionProviderInterface::SOURCE_BESTSELLER,
                'label' => __('Bestsellers')
            ],
            [
                'value' => ProductCollectionProviderInterface::SOURCE_FEATURED,
                'label' => __('Featured Products')
            ],
            [
                'value' => ProductCollectionProviderInterface::SOURCE_SALE,
                'label' => __('On Sale Products')
            ],
            [
                'value' => ProductCollectionProviderInterface::SOURCE_RANDOM,
                'label' => __('Random Products')
            ]
        ];
    }
}
