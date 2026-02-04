<?php
/**
 * Backend Model for Custom Links Configuration
 *
 * @category  Sigma
 * @package   Sigma_CustomLink
 */

declare(strict_types=1);

namespace Sigma\CustomLink\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class Links
 *
 * Backend model for handling serialized custom links data
 */
class Links extends ArraySerialized
{
    /**
     * @var Json
     */
    private Json $jsonSerializer;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @param Json|null $serializer
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = [],
        ?Json $serializer = null
    ) {
        $this->jsonSerializer = $serializer ?? new Json();
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data,
            $serializer
        );
    }

    /**
     * Process data before saving
     *
     * @return Links
     */
    public function beforeSave(): Links
    {
        $value = $this->getValue();

        if (is_array($value)) {
            // Remove empty rows and the __empty placeholder
            $value = $this->filterEmptyRows($value);
            
            // Validate and sanitize each link entry
            $value = $this->sanitizeLinks($value);
            
            // Re-index array to ensure proper ordering
            $value = array_values($value);
        }

        $this->setValue($value);
        return parent::beforeSave();
    }

    /**
     * Filter out empty rows from the configuration
     *
     * @param array $value
     * @return array
     */
    private function filterEmptyRows(array $value): array
    {
        return array_filter($value, function ($row) {
            if (!is_array($row)) {
                return false;
            }
            
            // Remove __empty placeholder row
            if (isset($row['__empty'])) {
                return false;
            }
            
            // Ensure required fields are present and not empty
            $title = trim($row['title'] ?? '');
            $url = trim($row['url'] ?? '');
            
            return !empty($title) && !empty($url);
        });
    }

    /**
     * Sanitize link entries
     *
     * @param array $links
     * @return array
     */
    private function sanitizeLinks(array $links): array
    {
        $sanitized = [];
        
        foreach ($links as $key => $link) {
            if (!is_array($link)) {
                continue;
            }
            
            $sanitized[$key] = [
                'title' => trim((string) ($link['title'] ?? '')),
                'url' => trim((string) ($link['url'] ?? '#')),
                'position' => (int) ($link['position'] ?? 100),
                'open_new_tab' => (bool) ($link['open_new_tab'] ?? false),
                'enabled' => isset($link['enabled']) ? (bool) $link['enabled'] : true
            ];
        }
        
        return $sanitized;
    }

    /**
     * Process data after loading
     *
     * @return Links
     */
    protected function _afterLoad(): Links
    {
        parent::_afterLoad();
        
        $value = $this->getValue();
        
        // Ensure we always have an array
        if (!is_array($value)) {
            $this->setValue([]);
        }
        
        return $this;
    }
}
