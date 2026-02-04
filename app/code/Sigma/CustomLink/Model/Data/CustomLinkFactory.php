<?php
/**
 * Custom Link Factory
 *
 * @category  Sigma
 * @package   Sigma_CustomLink
 */

declare(strict_types=1);

namespace Sigma\CustomLink\Model\Data;

use Magento\Framework\ObjectManagerInterface;
use Sigma\CustomLink\Api\Data\CustomLinkInterface;

/**
 * Class CustomLinkFactory
 *
 * Factory class for creating CustomLink instances
 */
class CustomLinkFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private ObjectManagerInterface $objectManager;

    /**
     * @var string
     */
    private string $instanceName;

    /**
     * Constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        string $instanceName = CustomLink::class
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create CustomLink instance
     *
     * @param array $data
     * @return CustomLinkInterface
     */
    public function create(array $data = []): CustomLinkInterface
    {
        return $this->objectManager->create($this->instanceName, ['data' => $data]);
    }
}
