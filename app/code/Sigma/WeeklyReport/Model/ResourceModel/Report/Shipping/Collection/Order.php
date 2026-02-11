<?php
/**
 * Extended Shipping Report Collection with weekly period support.
 *
 * @category  Sigma
 * @package   Sigma_WeeklyReport
 */
declare(strict_types=1);

namespace Sigma\WeeklyReport\Model\ResourceModel\Report\Shipping\Collection;

class Order extends \Magento\Sales\Model\ResourceModel\Report\Shipping\Collection\Order
{
    /**
     * Get selected columns - adds 'week' period SQL format support.
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        $connection = $this->getConnection();

        if ('month' == $this->_period) {
            $this->_periodFormat = $connection->getDateFormatSql('period', '%Y-%m');
        } elseif ('year' == $this->_period) {
            $this->_periodFormat = $connection->getDateExtractSql(
                'period',
                \Magento\Framework\DB\Adapter\AdapterInterface::INTERVAL_YEAR
            );
        } elseif ('week' == $this->_period) {
            $this->_periodFormat = new \Zend_Db_Expr(
                "DATE_FORMAT(DATE_SUB(period, INTERVAL WEEKDAY(period) DAY), '%Y-%m-%d')"
            );
        } else {
            $this->_periodFormat = $connection->getDateFormatSql('period', '%Y-%m-%d');
        }

        if (!$this->isTotals() && !$this->isSubTotals()) {
            $this->_selectedColumns = [
                'period' => $this->_periodFormat,
                'shipping_description' => 'shipping_description',
                'orders_count' => 'SUM(orders_count)',
                'total_shipping' => 'SUM(total_shipping)',
                'total_shipping_actual' => 'SUM(total_shipping_actual)',
            ];
        }

        if ($this->isTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns();
        }

        if ($this->isSubTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns() + ['period' => $this->_periodFormat];
        }

        return $this->_selectedColumns;
    }
}
