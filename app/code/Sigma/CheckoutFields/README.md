# Sigma_CheckoutFields Module

## Overview

| Property | Value |
|----------|-------|
| **Module Name** | Sigma_CheckoutFields |
| **Developer** | Amit Rathod |
| **Completion Date** | February 5, 2026 |
| **Magento Version** | 2.4.x |

## Description

Customizes the checkout shipping address form fields:
- Street Address with 3 input lines
- Middle Name field (optional)
- Company field (required)
- Fax field (visible)

## Features

| Field | Modification |
|-------|--------------|
| **Street Address** | 3 input boxes instead of default 2 |
| **Middle Name** | Added as optional field (not required) |
| **Company** | Made mandatory (required) |
| **Fax** | Displayed on checkout form |

## Directory Structure

```
app/code/Sigma/CheckoutFields/
├── registration.php
├── README.md
├── Plugin/
│   └── Checkout/
│       └── LayoutProcessorPlugin.php
└── etc/
    ├── module.xml
    ├── config.xml
    └── frontend/
        └── di.xml
```

## Installation

```bash
bin/magento module:enable Sigma_CheckoutFields
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

## Admin Configuration

To ensure 3 street lines work properly, verify:

**Path:** Stores → Configuration → Customers → Customer Configuration → Name and Address Options

| Setting | Value |
|---------|-------|
| Number of Lines in a Street Address | 3 |
| Show Middle Name (initial) | Optional |
| Show Fax | Required or Optional |

## Technical Notes

- Uses `LayoutProcessor` plugin with `sortOrder="200"` to run after core processors
- Modifies JavaScript layout array for checkout components
- Does not require database changes
- Works with all themes

---

*Developer: Amit Rathod | Last Updated: February 5, 2026*
