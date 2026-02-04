<?php
/**
 * Sigma PlpCustomization Module Registration
 *
 * @category  Sigma
 * @package   Sigma_PlpCustomization
 */

declare(strict_types=1);

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Sigma_PlpCustomization',
    __DIR__
);
