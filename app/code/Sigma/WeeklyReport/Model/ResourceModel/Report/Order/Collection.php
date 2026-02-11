<?php
/**
 * Extended Order Report Collection with weekly period support.
 *
 * @category  Sigma
 * @package   Sigma_WeeklyReport
 */
declare(strict_types=1);

namespace Sigma\WeeklyReport\Model\ResourceModel\Report\Order;

class Collection extends \Magento\Sales\Model\ResourceModel\Report\Order\Collection
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
            // Group by Monday date of the week (e.g., "2026-02-02")
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
                'total_qty_ordered' => 'SUM(total_qty_ordered)',
                'total_qty_invoiced' => 'SUM(total_qty_invoiced)',
                'total_income_amount' => 'SUM(total_income_amount)',
                'total_revenue_amount' => 'SUM(total_revenue_amount)',
                'total_profit_amount' => 'SUM(total_profit_amount)',
                'total_invoiced_amount' => 'SUM(total_invoiced_amount)',
                'total_canceled_amount' => 'SUM(total_canceled_amount)',
                'total_paid_amount' => 'SUM(total_paid_amount)',
                'total_refunded_amount' => 'SUM(total_refunded_amount)',
                'total_tax_amount' => 'SUM(total_tax_amount)',
                'total_tax_amount_actual' => 'SUM(total_tax_amount_actual)',
                'total_shipping_amount' => 'SUM(total_shipping_amount)',
                'total_shipping_amount_actual' => 'SUM(total_shipping_amount_actual)',
                'total_discount_amount' => 'SUM(total_discount_amount)',
                'total_discount_amount_actual' => 'SUM(total_discount_amount_actual)',
            ];
        }

        if ($this->isTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns();
        }

        return $this->_selectedColumns;
    }
}
