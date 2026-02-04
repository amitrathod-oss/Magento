<?php
/**
 * GST Number Column for Order Grid
 *
 * @category  Sigma
 * @package   Sigma_CheckoutGst
 */

declare(strict_types=1);

namespace Sigma\CheckoutGst\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Renders GST Number column in order grid
 */
class GstNumber extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = $item['company_gst_no'] ?? '-';
            }
        }

        return $dataSource;
    }
}
