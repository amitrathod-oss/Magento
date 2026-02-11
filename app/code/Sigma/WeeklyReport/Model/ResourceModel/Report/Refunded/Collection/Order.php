<?php
/**
 * Extended Refunded Report Collection with weekly period support.
 *
 * @category  Sigma
 * @package   Sigma_WeeklyReport
 */
declare(strict_types=1);

namespace Sigma\WeeklyReport\Model\ResourceModel\Report\Refunded\Collection;

class Order extends \Magento\Sales\Model\ResourceModel\Report\Refunded\Collection\Order
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

        if (!$this->isTotals()) {
            $this->_selectedColumns = [
                'period' => $this->_periodFormat,
                'orders_count' => 'SUM(orders_count)',
                'refunded' => 'SUM(refunded)',
                'online_refunded' => 'SUM(online_refunded)',
                'offline_refunded' => 'SUM(offline_refunded)',
            ];
        }

        if ($this->isTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns();
        }

        return $this->_selectedColumns;
    }
}
