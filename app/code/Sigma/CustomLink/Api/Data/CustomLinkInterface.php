<?php
/**
 * Custom Link Data Interface
 *
 * @category  Sigma
 * @package   Sigma_CustomLink
 */

declare(strict_types=1);

namespace Sigma\CustomLink\Api\Data;

/**
 * Interface CustomLinkInterface
 *
 * Data interface for custom navigation links
 */
interface CustomLinkInterface
{
    /**
     * Field constants
     */
    public const FIELD_TITLE = 'title';
    public const FIELD_URL = 'url';
    public const FIELD_POSITION = 'position';
    public const FIELD_OPEN_NEW_TAB = 'open_new_tab';
    public const FIELD_ENABLED = 'enabled';

    /**
     * Get link title
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Set link title
     *
     * @param string $title
     * @return CustomLinkInterface
     */
    public function setTitle(string $title): CustomLinkInterface;

    /**
     * Get link URL
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Set link URL
     *
     * @param string $url
     * @return CustomLinkInterface
     */
    public function setUrl(string $url): CustomLinkInterface;

    /**
     * Get link position/sort order
     *
     * @return int
     */
    public function getPosition(): int;

    /**
     * Set link position/sort order
     *
     * @param int $position
     * @return CustomLinkInterface
     */
    public function setPosition(int $position): CustomLinkInterface;

    /**
     * Check if link should open in new tab
     *
     * @return bool
     */
    public function isOpenNewTab(): bool;

    /**
     * Set open in new tab flag
     *
     * @param bool $openNewTab
     * @return CustomLinkInterface
     */
    public function setOpenNewTab(bool $openNewTab): CustomLinkInterface;

    /**
     * Check if link is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Set enabled flag
     *
     * @param bool $enabled
     * @return CustomLinkInterface
     */
    public function setEnabled(bool $enabled): CustomLinkInterface;
}
