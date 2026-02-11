<?php
/**
 * Custom Date renderer for weekly period display in sales report grids.
 * Shows date ranges like "Feb 2 - Feb 8, 2026" for weekly periods.
 *
 * @category  Sigma
 * @package   Sigma_WeeklyReport
 */
declare(strict_types=1);

namespace Sigma\WeeklyReport\Block\Adminhtml\Sales\Grid\Column\Renderer;

use Magento\Framework\DataObject;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;

class Date extends \Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date
{
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    private $localeResolverInterface;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param DateTimeFormatterInterface $dateTimeFormatter
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        DateTimeFormatterInterface $dateTimeFormatter,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        array $data = []
    ) {
        $this->localeResolverInterface = $localeResolver;
        parent::__construct($context, $dateTimeFormatter, $localeResolver, $data);
    }

    /**
     * Renders grid column
     *
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        if ($this->getColumn()->getPeriodType() === 'week') {
            $data = $row->getData($this->getColumn()->getIndex());
            if ($data) {
                return $this->_renderWeekRange($data);
            }
            return $this->getColumn()->getDefault();
        }

        return parent::render($row);
    }

    /**
     * Render a week range label from the Monday date.
     *
     * @param string $mondayDate The Monday date in Y-m-d format
     * @return string e.g., "Feb 2, 2026 - Feb 8, 2026"
     */
    protected function _renderWeekRange(string $mondayDate): string
    {
        try {
            $monday = new \DateTime($mondayDate);
            $sunday = clone $monday;
            $sunday->modify('+6 days');

            $locale = $this->localeResolverInterface->getLocale();
            $formatter = new \IntlDateFormatter(
                $locale,
                \IntlDateFormatter::MEDIUM,
                \IntlDateFormatter::NONE
            );

            $monStr = $formatter->format($monday);
            $sunStr = $formatter->format($sunday);

            return $monStr . ' - ' . $sunStr;
        } catch (\Exception $e) {
            return $mondayDate;
        }
    }
}
