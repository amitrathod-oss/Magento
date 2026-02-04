<?php
/**
 * Sigma CustomLink Configuration Model
 *
 * @category  Sigma
 * @package   Sigma_CustomLink
 */

declare(strict_types=1);

namespace Sigma\CustomLink\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Sigma\CustomLink\Api\Data\CustomLinkInterface;
use Sigma\CustomLink\Model\Data\CustomLinkFactory;

/**
 * Class Config
 *
 * Configuration model for custom navigation links
 */
class Config
{
    /**
     * Configuration paths
     */
    private const XML_PATH_ENABLED = 'catalog/custom_navigation_link/enabled';
    private const XML_PATH_LINKS = 'catalog/custom_navigation_link/links';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var Json
     */
    private Json $jsonSerializer;

    /**
     * @var CustomLinkFactory
     */
    private CustomLinkFactory $customLinkFactory;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var array|null
     */
    private ?array $linksCache = null;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param Json $jsonSerializer
     * @param CustomLinkFactory $customLinkFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Json $jsonSerializer,
        CustomLinkFactory $customLinkFactory,
        LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->jsonSerializer = $jsonSerializer;
        $this->customLinkFactory = $customLinkFactory;
        $this->logger = $logger;
    }

    /**
     * Check if custom links feature is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get all configured custom links
     *
     * @param int|null $storeId
     * @return CustomLinkInterface[]
     */
    public function getLinks(?int $storeId = null): array
    {
        if ($this->linksCache !== null) {
            return $this->linksCache;
        }

        $links = [];

        try {
            $linksData = $this->getLinksData($storeId);

            foreach ($linksData as $linkData) {
                $link = $this->createLinkFromData($linkData);
                
                // Only include enabled links
                if ($link->isEnabled()) {
                    $links[] = $link;
                }
            }

            // Sort links by position
            usort($links, function (CustomLinkInterface $a, CustomLinkInterface $b) {
                return $a->getPosition() <=> $b->getPosition();
            });

            $this->linksCache = $links;
        } catch (\Exception $e) {
            $this->logger->error(
                'Sigma_CustomLink: Error loading custom links configuration',
                ['exception' => $e->getMessage()]
            );
        }

        return $links;
    }

    /**
     * Get raw links data from configuration
     *
     * @param int|null $storeId
     * @return array
     */
    private function getLinksData(?int $storeId = null): array
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_LINKS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (empty($value)) {
            return [];
        }

        // Handle both serialized string and array
        if (is_string($value)) {
            try {
                $value = $this->jsonSerializer->unserialize($value);
            } catch (\InvalidArgumentException $e) {
                $this->logger->warning(
                    'Sigma_CustomLink: Failed to unserialize links data',
                    ['value' => $value, 'exception' => $e->getMessage()]
                );
                return [];
            }
        }

        return is_array($value) ? $value : [];
    }

    /**
     * Create CustomLink object from array data
     *
     * @param array $data
     * @return CustomLinkInterface
     */
    private function createLinkFromData(array $data): CustomLinkInterface
    {
        return $this->customLinkFactory->create([
            CustomLinkInterface::FIELD_TITLE => $data['title'] ?? '',
            CustomLinkInterface::FIELD_URL => $data['url'] ?? '#',
            CustomLinkInterface::FIELD_POSITION => (int) ($data['position'] ?? 100),
            CustomLinkInterface::FIELD_OPEN_NEW_TAB => (bool) ($data['open_new_tab'] ?? false),
            CustomLinkInterface::FIELD_ENABLED => isset($data['enabled']) ? (bool) $data['enabled'] : true
        ]);
    }

    /**
     * Clear cached links
     *
     * @return void
     */
    public function clearCache(): void
    {
        $this->linksCache = null;
    }

    /**
     * Check if any links are configured
     *
     * @param int|null $storeId
     * @return bool
     */
    public function hasLinks(?int $storeId = null): bool
    {
        return !empty($this->getLinks($storeId));
    }
}
