<?php
/**
 * Extended Bestsellers Report Collection with weekly period support.
 *
 * @category  Sigma
 * @package   Sigma_WeeklyReport
 */
declare(strict_types=1);

namespace Sigma\WeeklyReport\Model\ResourceModel\Report\Bestsellers;

class Collection extends \Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection
{
    /**
     * Retrieve selected columns - adds 'week' period SQL format support.
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        $connection = $this->getConnection();

        if (!$this->_selectedColumns) {
            if ($this->isTotals()) {
                $this->_selectedColumns = $this->getAggregatedColumns();
            } else {
                $this->_selectedColumns = [
                    'period' => sprintf('MAX(%s)', $connection->getDateFormatSql('period', '%Y-%m-%d')),
                    $this->getOrderedField() => 'SUM(' . $this->getOrderedField() . ')',
                    'product_id' => 'product_id',
                    'product_name' => 'MAX(product_name)',
                    'product_price' => 'MAX(product_price)',
                ];
                if ('year' == $this->_period) {
                    $this->_selectedColumns['period'] = $connection->getDateFormatSql('period', '%Y');
                } elseif ('month' == $this->_period) {
                    $this->_selectedColumns['period'] = $connection->getDateFormatSql('period', '%Y-%m');
                } elseif ('week' == $this->_period) {
                    $this->_selectedColumns['period'] = new \Zend_Db_Expr(
                        "DATE_FORMAT(DATE_SUB(period, INTERVAL WEEKDAY(period) DAY), '%Y-%m-%d')"
                    );
                }
            }
        }
        return $this->_selectedColumns;
    }

    /**
     * Init collection select - adds 'week' period support using daily table.
     *
     * @return $this
     */
    protected function _applyAggregatedTable()
    {
        $select = $this->getSelect();

        if (!$this->_period) {
            return parent::_applyAggregatedTable();
        }

        if ('year' == $this->_period) {
            $mainTable = $this->getTable($this->getTableByAggregationPeriod('yearly'));
            $select->from($mainTable, $this->_getSelectedColumns());
        } elseif ('month' == $this->_period) {
            $mainTable = $this->getTable($this->getTableByAggregationPeriod('monthly'));
            $select->from($mainTable, $this->_getSelectedColumns());
        } elseif ('week' == $this->_period) {
            // Use daily table for weekly aggregation
            $mainTable = $this->getTable($this->getTableByAggregationPeriod('daily'));
            $select->from($mainTable, $this->_getSelectedColumns());
        } else {
            $mainTable = $this->getTable($this->getTableByAggregationPeriod('daily'));
            $select->from($mainTable, $this->_getSelectedColumns());
        }

        if (!$this->isTotals()) {
            $select->group(['period', 'product_id']);
        }
        $select->where('rating_pos <= ?', $this->_ratingLimit);

        return $this;
    }
}
