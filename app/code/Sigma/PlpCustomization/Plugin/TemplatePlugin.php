<?php
/**
 * Template Plugin for PLP Customization
 *
 * @category  Sigma
 * @package   Sigma_PlpCustomization
 */

declare(strict_types=1);

namespace Sigma\PlpCustomization\Plugin;

use Magento\Framework\View\Element\Template;
use Sigma\PlpCustomization\Model\Config;

/**
 * Class TemplatePlugin
 *
 * Adds PLP customization config to templates
 */
class TemplatePlugin
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * Constructor
     *
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Add config data to template
     *
     * @param Template $subject
     * @return void
     */
    public function beforeToHtml(Template $subject): void
    {
        // Make config available in all templates
        if (!$subject->hasData('sigma_plp_config')) {
            $subject->setData('sigma_plp_config', $this->config);
        }
    }
}
