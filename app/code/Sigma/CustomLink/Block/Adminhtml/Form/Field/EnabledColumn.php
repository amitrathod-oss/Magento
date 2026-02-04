<?php
/**
 * Enabled Column Renderer
 *
 * @category  Sigma
 * @package   Sigma_CustomLink
 */

declare(strict_types=1);

namespace Sigma\CustomLink\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;

/**
 * Class EnabledColumn
 *
 * Renders Yes/No dropdown for "Enabled" column
 */
class EnabledColumn extends Select
{
    /**
     * Set element name
     *
     * @param string $value
     * @return EnabledColumn
     */
    public function setInputName($value): EnabledColumn
    {
        return $this->setName($value);
    }

    /**
     * Set element ID
     *
     * @param string $value
     * @return EnabledColumn
     */
    public function setInputId($value): EnabledColumn
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }

        return parent::_toHtml();
    }

    /**
     * Get source options
     *
     * @return array
     */
    private function getSourceOptions(): array
    {
        return [
            ['label' => __('Yes'), 'value' => '1'],
            ['label' => __('No'), 'value' => '0']
        ];
    }
}
