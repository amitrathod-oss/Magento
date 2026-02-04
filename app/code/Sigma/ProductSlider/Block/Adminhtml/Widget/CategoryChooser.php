<?php
/**
 * Category Chooser Block for Widget
 *
 * @category  Sigma
 * @package   Sigma_ProductSlider
 */

declare(strict_types=1);

namespace Sigma\ProductSlider\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CategoryChooser
 *
 * Widget parameter block for category selection
 */
class CategoryChooser extends Template
{
    /**
     * @var CategoryCollectionFactory
     */
    private CategoryCollectionFactory $categoryCollectionFactory;

    /**
     * @var AbstractElement|null
     */
    private ?AbstractElement $element = null;

    /**
     * Constructor
     *
     * @param Context $context
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        CategoryCollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param AbstractElement $element
     * @return AbstractElement
     * @throws LocalizedException
     */
    public function prepareElementHtml(AbstractElement $element): AbstractElement
    {
        $this->element = $element;
        
        $inputId = $element->getId();
        $inputName = $element->getName();
        $value = $element->getValue();
        
        // Get selected category names for display
        $selectedNames = $this->getSelectedCategoryNames($value);
        
        $html = '<div class="sigma-category-chooser" id="' . $inputId . '-container">';
        
        // Hidden input for value
        $html .= '<input type="hidden" 
                         id="' . $inputId . '" 
                         name="' . $inputName . '" 
                         value="' . $this->escapeHtml($value) . '" 
                         class="widget-option" />';
        
        // Display field
        $html .= '<input type="text" 
                         id="' . $inputId . '-display" 
                         value="' . $this->escapeHtml($selectedNames) . '" 
                         class="admin__control-text" 
                         readonly="readonly" 
                         style="width: 300px; margin-right: 10px;" />';
        
        // Button to open chooser
        $html .= '<button type="button" 
                          class="action-default scalable" 
                          onclick="sigmaOpenCategoryChooser(\'' . $inputId . '\')">';
        $html .= '<span>' . __('Select Categories...') . '</span>';
        $html .= '</button>';
        
        // Category tree container
        $html .= '<div id="' . $inputId . '-tree" class="sigma-category-tree" style="display:none; margin-top: 10px; max-height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">';
        $html .= $this->renderCategoryTree($value);
        $html .= '</div>';
        
        $html .= '</div>';
        
        // Add JavaScript
        $html .= $this->getJavaScript($inputId);
        
        $element->setData('after_element_html', $html);
        $element->setValue('');
        
        return $element;
    }

    /**
     * Get selected category names
     *
     * @param string|null $value
     * @return string
     */
    private function getSelectedCategoryNames(?string $value): string
    {
        if (empty($value)) {
            return '';
        }
        
        $categoryIds = array_filter(explode(',', $value));
        
        if (empty($categoryIds)) {
            return '';
        }
        
        try {
            $collection = $this->categoryCollectionFactory->create();
            $collection->addAttributeToSelect('name')
                       ->addFieldToFilter('entity_id', ['in' => $categoryIds]);
            
            $names = [];
            foreach ($collection as $category) {
                $names[] = $category->getName();
            }
            
            return implode(', ', $names);
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Render category tree HTML
     *
     * @param string|null $selectedIds
     * @return string
     */
    private function renderCategoryTree(?string $selectedIds): string
    {
        $selectedArray = $selectedIds ? array_filter(explode(',', $selectedIds)) : [];
        
        try {
            $collection = $this->categoryCollectionFactory->create();
            $collection->addAttributeToSelect(['name', 'is_active'])
                       ->addFieldToFilter('is_active', 1)
                       ->addFieldToFilter('level', ['gt' => 0])
                       ->setOrder('path', 'ASC');
            
            $html = '';
            foreach ($collection as $category) {
                $level = $category->getLevel();
                $padding = ($level - 1) * 20;
                $checked = in_array($category->getId(), $selectedArray) ? 'checked="checked"' : '';
                
                $html .= '<div style="padding-left: ' . $padding . 'px; margin: 5px 0;">';
                $html .= '<label>';
                $html .= '<input type="checkbox" 
                                 class="sigma-category-checkbox" 
                                 value="' . $category->getId() . '" 
                                 ' . $checked . ' /> ';
                $html .= $this->escapeHtml($category->getName());
                $html .= ' <small style="color: #999;">(ID: ' . $category->getId() . ')</small>';
                $html .= '</label>';
                $html .= '</div>';
            }
            
            return $html;
        } catch (\Exception $e) {
            return '<p>' . __('Error loading categories') . '</p>';
        }
    }

    /**
     * Get JavaScript for category chooser
     *
     * @param string $inputId
     * @return string
     */
    private function getJavaScript(string $inputId): string
    {
        return <<<JS
<script type="text/javascript">
//<![CDATA[
function sigmaOpenCategoryChooser(inputId) {
    var treeContainer = document.getElementById(inputId + '-tree');
    if (treeContainer.style.display === 'none') {
        treeContainer.style.display = 'block';
    } else {
        treeContainer.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('{$inputId}-container');
    if (container) {
        var checkboxes = container.querySelectorAll('.sigma-category-checkbox');
        var hiddenInput = document.getElementById('{$inputId}');
        var displayInput = document.getElementById('{$inputId}-display');
        
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                var selected = [];
                var names = [];
                checkboxes.forEach(function(cb) {
                    if (cb.checked) {
                        selected.push(cb.value);
                        names.push(cb.parentNode.textContent.trim().split(' (ID:')[0]);
                    }
                });
                hiddenInput.value = selected.join(',');
                displayInput.value = names.join(', ');
            });
        });
    }
});
//]]>
</script>
JS;
    }
}
