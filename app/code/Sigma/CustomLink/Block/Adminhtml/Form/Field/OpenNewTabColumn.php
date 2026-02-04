<?php
/**
 * Open New Tab Column Renderer
 *
 * @category  Sigma
 * @package   Sigma_CustomLink
 */

declare(strict_types=1);

namespace Sigma\CustomLink\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;

/**
 * Class OpenNewTabColumn
 *
 * Renders Yes/No dropdown for "Open in New Tab" column
 */
class OpenNewTabColumn extends Select
{
    /**
     * Set element name
     *
     * @param string $value
     * @return OpenNewTabColumn
     */
    public function setInputName($value): OpenNewTabColumn
    {
        return $this->setName($value);
    }

    /**
     * Set element ID
     *
     * @param string $value
     * @return OpenNewTabColumn
     */
    public function setInputId($value): OpenNewTabColumn
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
            ['label' => __('No'), 'value' => '0'],
            ['label' => __('Yes'), 'value' => '1']
        ];
    }
}
