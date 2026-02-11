<?php
/**
 * Extended Report Collection with weekly interval support.
 *
 * @category  Sigma
 * @package   Sigma_WeeklyReport
 */
declare(strict_types=1);

namespace Sigma\WeeklyReport\Model\ResourceModel\Report;

class Collection extends \Magento\Reports\Model\ResourceModel\Report\Collection
{
    /**
     * Get intervals - adds 'week' period support.
     *
     * @return array
     */
    protected function _getIntervals()
    {
        if (!$this->_intervals) {
            $this->_intervals = [];
            if (!$this->_from && !$this->_to) {
                return $this->_intervals;
            }
            $dateStart = new \DateTime($this->_from->format('Y-m-d'), $this->_from->getTimezone());
            $dateEnd = new \DateTime($this->_to->format('Y-m-d'), $this->_to->getTimezone());

            $firstInterval = true;
            while ($dateStart <= $dateEnd) {
                switch ($this->_period) {
                    case 'day':
                        $interval = $this->_getDayInterval($dateStart);
                        $dateStart->modify('+1 day');
                        break;
                    case 'week':
                        $interval = $this->_getWeekInterval($dateStart, $dateEnd, $firstInterval);
                        $firstInterval = false;
                        break;
                    case 'month':
                        $interval = $this->_getMonthInterval($dateStart, $dateEnd, $firstInterval);
                        $firstInterval = false;
                        break;
                    case 'year':
                        $interval = $this->_getYearInterval($dateStart, $dateEnd, $firstInterval);
                        $firstInterval = false;
                        break;
                    default:
                        break 2;
                }
                $this->_intervals[$interval['period']] = new \Magento\Framework\DataObject($interval);
            }
        }
        return $this->_intervals;
    }

    /**
     * Get interval for a week.
     *
     * @param \DateTime $dateStart
     * @param \DateTime $dateEnd
     * @param bool $firstInterval
     * @return array
     */
    protected function _getWeekInterval(\DateTime $dateStart, \DateTime $dateEnd, bool $firstInterval): array
    {
        $interval = [];

        // Calculate the Monday of the current week
        $monday = clone $dateStart;
        if ($monday->format('N') != 1) {
            $monday->modify('last monday');
        }

        // Use Monday date as the period key (matches SQL: DATE_SUB(period, INTERVAL WEEKDAY(period) DAY))
        $interval['period'] = $monday->format('Y-m-d');

        if ($firstInterval) {
            $interval['start'] = $this->_localeDate->convertConfigTimeToUtc(
                $dateStart->format('Y-m-d 00:00:00')
            );
        } else {
            $interval['start'] = $this->_localeDate->convertConfigTimeToUtc(
                $monday->format('Y-m-d 00:00:00')
            );
        }

        // End of the week: Sunday, or the end date if it falls before Sunday
        $sunday = clone $monday;
        $sunday->modify('+6 days');

        if ($sunday > $dateEnd) {
            $interval['end'] = $this->_localeDate->convertConfigTimeToUtc(
                $dateEnd->format('Y-m-d 23:59:59')
            );
        } else {
            $interval['end'] = $this->_localeDate->convertConfigTimeToUtc(
                $sunday->format('Y-m-d 23:59:59')
            );
        }

        // Move dateStart to next Monday
        $nextMonday = clone $monday;
        $nextMonday->modify('+7 days');
        $dateStart->setDate(
            (int)$nextMonday->format('Y'),
            (int)$nextMonday->format('m'),
            (int)$nextMonday->format('d')
        );

        return $interval;
    }

    /**
     * Return date periods including 'week'.
     *
     * @return array
     */
    public function getPeriods()
    {
        return [
            'day' => __('Day'),
            'week' => __('Week'),
            'month' => __('Month'),
            'year' => __('Year'),
        ];
    }
}
