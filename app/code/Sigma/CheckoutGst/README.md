# Sigma_CheckoutGst Module

## Overview

| Property | Value |
|----------|-------|
| **Module Name** | Sigma_CheckoutGst |
| **Developer** | Amit Rathod |
| **Completion Date** | January 29, 2026 |
| **Magento Version** | 2.4.x |

## Description

Adds a required "Company GST No" field to the checkout shipping form. Saves the value to the database and displays it in the Admin Order Grid and Order View page.

## Features

- ✅ Required GST field on checkout shipping form
- ✅ Client-side validation
- ✅ Server-side saving to database
- ✅ GST column in Admin Order Grid (filterable, sortable)
- ✅ GST Information section on Admin Order View

## Directory Structure

```
app/code/Sigma/CheckoutGst/
├── registration.php
├── Block/Adminhtml/Order/View/GstInfo.php
├── Observer/CopyGstToOrder.php
├── Plugin/Checkout/
│   ├── LayoutProcessorPlugin.php
│   └── ShippingInformationManagement.php
├── Ui/Component/Listing/Column/GstNumber.php
├── etc/
│   ├── module.xml
│   ├── db_schema.xml
│   ├── di.xml
│   ├── events.xml
│   ├── extension_attributes.xml
│   ├── fieldset.xml
│   └── frontend/di.xml
└── view/
    ├── adminhtml/
    │   ├── layout/sales_order_view.xml
    │   ├── templates/order/view/gst-info.phtml
    │   └── ui_component/sales_order_grid.xml
    └── frontend/
        ├── requirejs-config.js
        └── web/js/action/set-shipping-information-mixin.js
```

## Database

| Table | Column | Type |
|-------|--------|------|
| quote | company_gst_no | VARCHAR(50) |
| sales_order | company_gst_no | VARCHAR(50) |
| sales_order_grid | company_gst_no | VARCHAR(50) |

## Installation

```bash
bin/magento module:enable Sigma_CheckoutGst
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

## Admin Locations

- **Order Grid Column**: Sales → Orders → "Company GST No" column
- **Order View**: Sales → Orders → View → "GST Information" section

---

*Developer: Amit Rathod | Last Updated: January 29, 2026*
