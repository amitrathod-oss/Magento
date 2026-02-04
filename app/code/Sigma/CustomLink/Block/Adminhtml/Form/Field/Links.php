<?php
/**
 * Dynamic Rows Frontend Model for Links Configuration
 *
 * @category  Sigma
 * @package   Sigma_CustomLink
 */

declare(strict_types=1);

namespace Sigma\CustomLink\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Links
 *
 * Renders dynamic rows for custom navigation links configuration
 */
class Links extends AbstractFieldArray
{
    /**
     * @var OpenNewTabColumn|null
     */
    private ?OpenNewTabColumn $openNewTabRenderer = null;

    /**
     * @var EnabledColumn|null
     */
    private ?EnabledColumn $enabledRenderer = null;

    /**
     * Prepare rendering the new field by adding all required columns
     *
     * @return void
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn('title', [
            'label' => __('Link Title'),
            'class' => 'required-entry admin__control-text',
            'style' => 'width: 150px'
        ]);

        $this->addColumn('url', [
            'label' => __('Link URL'),
            'class' => 'required-entry admin__control-text',
            'style' => 'width: 200px'
        ]);

        $this->addColumn('position', [
            'label' => __('Position'),
            'class' => 'validate-number admin__control-text',
            'style' => 'width: 60px'
        ]);

        $this->addColumn('open_new_tab', [
            'label' => __('New Tab'),
            'renderer' => $this->getOpenNewTabRenderer()
        ]);

        $this->addColumn('enabled', [
            'label' => __('Enabled'),
            'renderer' => $this->getEnabledRenderer()
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Link');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $openNewTabRenderer = $this->getOpenNewTabRenderer();
        $enabledRenderer = $this->getEnabledRenderer();

        $openNewTab = $row->getData('open_new_tab');
        if ($openNewTab !== null) {
            $key = 'option_' . $openNewTabRenderer->calcOptionHash((string) $openNewTab);
            $options[$key] = 'selected="selected"';
        }

        $enabled = $row->getData('enabled');
        // Default to enabled if not set
        $enabledValue = ($enabled === null || $enabled === '') ? '1' : (string) $enabled;
        $key = 'option_' . $enabledRenderer->calcOptionHash($enabledValue);
        $options[$key] = 'selected="selected"';

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * Get open new tab column renderer
     *
     * @return OpenNewTabColumn
     * @throws LocalizedException
     */
    private function getOpenNewTabRenderer(): OpenNewTabColumn
    {
        if ($this->openNewTabRenderer === null) {
            $this->openNewTabRenderer = $this->getLayout()->createBlock(
                OpenNewTabColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->openNewTabRenderer;
    }

    /**
     * Get enabled column renderer
     *
     * @return EnabledColumn
     * @throws LocalizedException
     */
    private function getEnabledRenderer(): EnabledColumn
    {
        if ($this->enabledRenderer === null) {
            $this->enabledRenderer = $this->getLayout()->createBlock(
                EnabledColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->enabledRenderer;
    }
}
