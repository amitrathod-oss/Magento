<?php
/**
 * Custom Link Data Model
 *
 * @category  Sigma
 * @package   Sigma_CustomLink
 */

declare(strict_types=1);

namespace Sigma\CustomLink\Model\Data;

use Magento\Framework\DataObject;
use Sigma\CustomLink\Api\Data\CustomLinkInterface;

/**
 * Class CustomLink
 *
 * Data transfer object for custom navigation links
 */
class CustomLink extends DataObject implements CustomLinkInterface
{
    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return (string) $this->getData(self::FIELD_TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setTitle(string $title): CustomLinkInterface
    {
        return $this->setData(self::FIELD_TITLE, $title);
    }

    /**
     * @inheritdoc
     */
    public function getUrl(): string
    {
        $url = $this->getData(self::FIELD_URL);
        return $url !== null ? (string) $url : '#';
    }

    /**
     * @inheritdoc
     */
    public function setUrl(string $url): CustomLinkInterface
    {
        return $this->setData(self::FIELD_URL, $url);
    }

    /**
     * @inheritdoc
     */
    public function getPosition(): int
    {
        $position = $this->getData(self::FIELD_POSITION);
        return $position !== null ? (int) $position : 100;
    }

    /**
     * @inheritdoc
     */
    public function setPosition(int $position): CustomLinkInterface
    {
        return $this->setData(self::FIELD_POSITION, $position);
    }

    /**
     * @inheritdoc
     */
    public function isOpenNewTab(): bool
    {
        return (bool) $this->getData(self::FIELD_OPEN_NEW_TAB);
    }

    /**
     * @inheritdoc
     */
    public function setOpenNewTab(bool $openNewTab): CustomLinkInterface
    {
        return $this->setData(self::FIELD_OPEN_NEW_TAB, $openNewTab);
    }

    /**
     * @inheritdoc
     */
    public function isEnabled(): bool
    {
        $enabled = $this->getData(self::FIELD_ENABLED);
        // Default to enabled if not explicitly set
        return $enabled === null || $enabled === '' || (bool) $enabled;
    }

    /**
     * @inheritdoc
     */
    public function setEnabled(bool $enabled): CustomLinkInterface
    {
        return $this->setData(self::FIELD_ENABLED, $enabled);
    }
}
