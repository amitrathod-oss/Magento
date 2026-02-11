<?php
/**
 * Plugin to add 'Week' option to the Period dropdown in Sales Reports filter form.
 *
 * @category  Sigma
 * @package   Sigma_WeeklyReport
 */
declare(strict_types=1);

namespace Sigma\WeeklyReport\Plugin\Reports\Block\Adminhtml\Filter;

use Magento\Reports\Block\Adminhtml\Filter\Form as FilterForm;

class FormPlugin
{
    /**
     * After setForm: inject the 'week' option into the period_type field.
     *
     * @param FilterForm $subject
     * @param FilterForm $result
     * @return FilterForm
     */
    public function afterSetForm(FilterForm $subject, $result)
    {
        $form = $subject->getForm();
        if ($form) {
            $periodField = $form->getElement('period_type');
            if ($periodField) {
                $periodField->setValues([
                    ['value' => 'day', 'label' => __('Day')],
                    ['value' => 'week', 'label' => __('Week')],
                    ['value' => 'month', 'label' => __('Month')],
                    ['value' => 'year', 'label' => __('Year')],
                ]);
            }
        }
        return $result;
    }
}
