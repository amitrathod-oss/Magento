# Sigma_ClickAndCollect

A Magento 2 module that adds a "Click and Collect" shipping method, allowing customers to pick up their orders from a physical store location.

## Overview

This module provides a custom shipping carrier that enables store pickup as a delivery option during checkout. Customers can choose to collect their orders in-store, typically at no additional cost.

## Features

- **Custom Shipping Method**: Adds "Click and Collect" as a shipping option
- **Free Pickup Option**: Configure with zero shipping cost
- **Store Information Display**: Shows store address, hours, and pickup instructions
- **Country Restrictions**: Limit availability to specific countries
- **Admin Configuration**: Full control via Magento Admin Panel
- **Handling Fee Support**: Optional additional fee for order processing

## Installation

```bash
# Enable the module
bin/magento module:enable Sigma_ClickAndCollect

# Run setup upgrade
bin/magento setup:upgrade

# Compile DI
bin/magento setup:di:compile

# Clear cache
bin/magento cache:flush
```

## Admin Configuration

Navigate to **Stores > Configuration > Sales > Delivery Methods > Click and Collect**

### Available Settings

| Setting | Description |
|---------|-------------|
| **Enabled** | Enable/disable the shipping method |
| **Title** | Header shown in shipping method section (e.g., "Store Pickup") |
| **Method Name** | Name displayed for the method (e.g., "Click and Collect") |
| **Price** | Shipping cost (set to 0 for free pickup) |
| **Handling Fee** | Additional processing fee |
| **Store Address** | Physical store address for pickup |
| **Store Hours** | Operating hours for pickup |
| **Pickup Instructions** | Instructions for customers (ID requirements, etc.) |
| **Ship to Applicable Countries** | All or specific countries |
| **Ship to Specific Countries** | Country selection if specific |
| **Displayed Error Message** | Message when method unavailable |
| **Sort Order** | Display order among shipping methods |

## File Structure

```
Sigma/ClickAndCollect/
├── etc/
│   ├── adminhtml/
│   │   └── system.xml          # Admin configuration
│   ├── config.xml              # Default values
│   └── module.xml              # Module declaration
├── Model/
│   └── Carrier/
│       └── ClickAndCollect.php # Shipping carrier model
├── registration.php            # Module registration
└── README.md
```

## Usage

### Customer Experience

1. Add products to cart
2. Proceed to checkout
3. Select "Click and Collect" as the shipping method
4. Complete the order
5. Receive order confirmation with pickup details

### Default Configuration

- **Title**: Store Pickup
- **Method Name**: Click and Collect
- **Price**: Free ($0.00)
- **Handling Fee**: $0

## Technical Details

### Carrier Code
`clickandcollect`

### Key Methods

| Method | Description |
|--------|-------------|
| `collectRates()` | Returns available shipping rates |
| `getAllowedMethods()` | Returns allowed shipping methods |
| `getStoreAddress()` | Retrieves configured store address |
| `getStoreHours()` | Retrieves configured store hours |
| `getPickupInstructions()` | Retrieves pickup instructions |

## Customization

### Extending the Module

You can extend the carrier model to add custom logic:

```php
<?php
namespace Vendor\Module\Model\Carrier;

use Sigma\ClickAndCollect\Model\Carrier\ClickAndCollect as BaseCarrier;

class CustomClickAndCollect extends BaseCarrier
{
    public function collectRates(RateRequest $request)
    {
        // Custom logic here
        return parent::collectRates($request);
    }
}
```

### Adding Multiple Store Locations

For multi-store pickup support, consider:
- Creating a store locations database table
- Adding a location selector in checkout
- Modifying the carrier to handle multiple pickup points

## Requirements

- Magento 2.4.x
- PHP 7.4 or higher

## Support

For issues or feature requests, please contact the development team.

## License

Proprietary - Sigma Development
