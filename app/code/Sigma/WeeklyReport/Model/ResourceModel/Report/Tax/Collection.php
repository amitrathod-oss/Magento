<?php
/**
 * Extended Tax Report Collection with weekly period support.
 *
 * @category  Sigma
 * @package   Sigma_WeeklyReport
 */
declare(strict_types=1);

namespace Sigma\WeeklyReport\Model\ResourceModel\Report\Tax;

class Collection extends \Magento\Tax\Model\ResourceModel\Report\Collection
{
    /**
     * Return an array of columns which are selected - adds 'week' period SQL format support.
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        if ('month' == $this->_period) {
            $this->_periodFormat = $this->getConnection()->getDateFormatSql('period', '%Y-%m');
        } elseif ('year' == $this->_period) {
            $this->_periodFormat = $this->getConnection()->getDateFormatSql('period', '%Y');
        } elseif ('week' == $this->_period) {
            $this->_periodFormat = new \Zend_Db_Expr(
                "DATE_FORMAT(DATE_SUB(period, INTERVAL WEEKDAY(period) DAY), '%Y-%m-%d')"
            );
        } else {
            $this->_periodFormat = $this->getConnection()->getDateFormatSql('period', '%Y-%m-%d');
        }

        if (!$this->isTotals() && !$this->isSubTotals()) {
            $this->_selectedColumns = [
                'period' => $this->_periodFormat,
                'code' => 'code',
                'percent' => 'percent',
                'orders_count' => 'SUM(orders_count)',
                'tax_base_amount_sum' => 'SUM(tax_base_amount_sum)',
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
