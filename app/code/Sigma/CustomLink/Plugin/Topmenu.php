<?php
/**
 * Sigma CustomLink Topmenu Plugin
 *
 * @category  Sigma
 * @package   Sigma_CustomLink
 */

declare(strict_types=1);

namespace Sigma\CustomLink\Plugin;

use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\UrlInterface;
use Magento\Theme\Block\Html\Topmenu as Subject;
use Psr\Log\LoggerInterface;
use Sigma\CustomLink\Api\Data\CustomLinkInterface;
use Sigma\CustomLink\Model\Config;

/**
 * Class Topmenu
 *
 * Plugin to add custom navigation links to the top menu
 */
class Topmenu
{
    /**
     * Custom link ID prefix
     */
    private const LINK_ID_PREFIX = 'sigma-custom-link-';

    /**
     * @var NodeFactory
     */
    private NodeFactory $nodeFactory;

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var array
     */
    private array $addedLinks = [];

    /**
     * Constructor
     *
     * @param NodeFactory $nodeFactory
     * @param UrlInterface $urlBuilder
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        NodeFactory $nodeFactory,
        UrlInterface $urlBuilder,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->nodeFactory = $nodeFactory;
        $this->urlBuilder = $urlBuilder;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Add custom menu links to the navigation menu
     *
     * @param Subject $subject
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return void
     */
    public function beforeGetHtml(
        Subject $subject,
        $outermostClass = '',
        $childrenWrapClass = '',
        $limit = 0
    ): void {
        // Check if the custom link feature is enabled
        if (!$this->config->isEnabled()) {
            return;
        }

        try {
            $links = $this->config->getLinks();

            if (empty($links)) {
                return;
            }

            $menu = $subject->getMenu();

            foreach ($links as $index => $link) {
                $this->addLinkToMenu($link, $menu, $index);
            }
        } catch (\Exception $e) {
            $this->logger->error(
                'Sigma_CustomLink: Error adding custom links to menu',
                ['exception' => $e->getMessage()]
            );
        }
    }

    /**
     * Add a single link to the menu
     *
     * @param CustomLinkInterface $link
     * @param \Magento\Framework\Data\Tree\Node $menu
     * @param int $index
     * @return void
     */
    private function addLinkToMenu(
        CustomLinkInterface $link,
        \Magento\Framework\Data\Tree\Node $menu,
        int $index
    ): void {
        $linkTitle = $link->getTitle();
        $linkUrl = $link->getUrl();

        // Validate link data
        if (empty($linkTitle) || empty($linkUrl)) {
            return;
        }

        // Build the full URL
        $fullUrl = $this->buildUrl($linkUrl);
        $linkId = self::LINK_ID_PREFIX . $index;

        // Store link data for later processing
        $this->addedLinks[$linkId] = [
            'url' => $fullUrl,
            'open_new_tab' => $link->isOpenNewTab()
        ];

        // Create the menu node
        $node = $this->nodeFactory->create([
            'data' => [
                'name' => $linkTitle,
                'id' => $linkId,
                'url' => $fullUrl,
                'has_active' => false,
                'is_active' => $this->isLinkActive($fullUrl)
            ],
            'idField' => 'id',
            'tree' => $menu->getTree()
        ]);

        // Add the node to the menu
        $menu->addChild($node);
    }

    /**
     * Modify the HTML output to add target="_blank" for configured links
     *
     * @param Subject $subject
     * @param string $result
     * @return string
     */
    public function afterGetHtml(Subject $subject, string $result): string
    {
        if (!$this->config->isEnabled() || empty($this->addedLinks)) {
            return $result;
        }

        foreach ($this->addedLinks as $linkId => $linkData) {
            if (!$linkData['open_new_tab']) {
                continue;
            }

            $result = $this->addTargetBlankToLink($result, $linkData['url']);
        }

        return $result;
    }

    /**
     * Add target="_blank" attribute to a specific link
     *
     * @param string $html
     * @param string $url
     * @return string
     */
    private function addTargetBlankToLink(string $html, string $url): string
    {
        $escapedUrl = preg_quote($url, '/');
        $pattern = '/(<a[^>]*href=["\']' . $escapedUrl . '["\'][^>]*)>/';
        $replacement = '$1 target="_blank" rel="noopener noreferrer">';

        $result = preg_replace($pattern, $replacement, $html);

        return $result !== null ? $result : $html;
    }

    /**
     * Build full URL from the configured link
     *
     * @param string $url
     * @return string
     */
    private function buildUrl(string $url): string
    {
        // If URL is empty or just a hash, return as-is
        if (empty($url) || $url === '#') {
            return $url;
        }

        // If URL starts with http:// or https://, use as-is
        if ($this->isAbsoluteUrl($url)) {
            return $url;
        }

        // Build relative URL
        $baseUrl = rtrim($this->urlBuilder->getBaseUrl(), '/');
        $path = ltrim($url, '/');

        return $baseUrl . '/' . $path;
    }

    /**
     * Check if URL is absolute
     *
     * @param string $url
     * @return bool
     */
    private function isAbsoluteUrl(string $url): bool
    {
        return (bool) preg_match('/^https?:\/\//i', $url);
    }

    /**
     * Check if the link is currently active
     *
     * @param string $url
     * @return bool
     */
    private function isLinkActive(string $url): bool
    {
        if (empty($url) || $url === '#') {
            return false;
        }

        $currentUrl = $this->urlBuilder->getCurrentUrl();

        return $url === $currentUrl;
    }
}
