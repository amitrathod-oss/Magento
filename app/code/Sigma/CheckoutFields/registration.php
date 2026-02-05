<?php
/**
 * Sigma CheckoutFields Module Registration
 *
 * @category  Sigma
 * @package   Sigma_CheckoutFields
 */

declare(strict_types=1);

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Sigma_CheckoutFields',
    __DIR__
);
