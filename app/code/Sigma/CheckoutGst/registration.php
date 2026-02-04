<?php
/**
 * Sigma CheckoutGst Module Registration
 *
 * @category  Sigma
 * @package   Sigma_CheckoutGst
 */

declare(strict_types=1);

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Sigma_CheckoutGst',
    __DIR__
);
