<?php
/**
 * Yes/No Source Model for Dynamic Rows
 *
 * @category  Sigma
 * @package   Sigma_CustomLink
 */

declare(strict_types=1);

namespace Sigma\CustomLink\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class YesNo
 *
 * Provides yes/no options for dynamic rows dropdown
 */
class YesNo implements OptionSourceInterface
{
    /**
     * Option values
     */
    private const VALUE_YES = '1';
    private const VALUE_NO = '0';

    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::VALUE_NO,
                'label' => __('No')
            ],
            [
                'value' => self::VALUE_YES,
                'label' => __('Yes')
            ]
        ];
    }
}
