# Sigma_TierPricing

A Magento 2 module that provides documentation and CLI utilities for managing tier pricing for specific customer groups on individual products.

## Overview

Tier pricing in Magento allows you to offer quantity-based discounts to customers. This module provides:
- Complete documentation on setting up tier pricing
- CLI commands for programmatic tier price management
- Best practices for tier pricing implementation

## What is Tier Pricing?

Tier pricing enables you to offer different prices based on:
- **Quantity purchased** - E.g., "Buy 10+ and get 10% off"
- **Customer group** - E.g., "Wholesale customers get special pricing"
- **Website** - Different tiers for different store views

## Customer Groups in Magento

| Group ID | Group Name | Description |
|----------|------------|-------------|
| 0 | NOT LOGGED IN | Guest customers |
| 1 | General | Default registered customers |
| 2 | Wholesale | Wholesale customers (if configured) |
| 3 | Retailer | Retailer customers (if configured) |
| 32000 | ALL GROUPS | Applies to all customer groups |

---

## Setup Instructions

### Method 1: Admin Panel (Recommended for Individual Products)

#### Step 1: Navigate to Product Edit Page

1. Go to **Catalog > Products**
2. Click **Edit** on the product you want to configure
3. Scroll down to the **Advanced Pricing** section

#### Step 2: Open Advanced Pricing

1. Click the **Advanced Pricing** link under the Price field
2. A modal window will open with pricing options

#### Step 3: Add Tier Price

1. In the modal, find the **Tier Price** section
2. Click **Add** button to add a new tier

#### Step 4: Configure Tier Price

For each tier, configure:

| Field | Description | Example |
|-------|-------------|---------|
| **Website** | Select website or "All Websites" | All Websites |
| **Customer Group** | Select target customer group | General |
| **Quantity** | Minimum quantity to trigger tier | 5 |
| **Price** | Either Fixed price or Discount percentage | 45.00 (Fixed) or 10% (Discount) |

#### Step 5: Save Product

1. Click **Done** in the modal
2. Click **Save** on the product page
3. Clear cache: `bin/magento cache:flush`

---

### Method 2: CLI Commands (For Bulk/Programmatic Updates)

#### Add Tier Price to a Product

```bash
# Basic usage - Add tier price for General customer group (ID: 1)
bin/magento sigma:tierprice:add <SKU> <QTY> <PRICE>

# Example: Product SKU "24-MB01", quantity 5+, price $40
bin/magento sigma:tierprice:add 24-MB01 5 40

# Specify customer group
bin/magento sigma:tierprice:add 24-MB01 10 35 --group=1

# For all customer groups
bin/magento sigma:tierprice:add 24-MB01 20 30 --group=32000

# For NOT LOGGED IN (guest) customers
bin/magento sigma:tierprice:add 24-MB01 5 42 --group=0
```

#### List Tier Prices for a Product

```bash
bin/magento sigma:tierprice:list <SKU>

# Example
bin/magento sigma:tierprice:list 24-MB01
```

**Sample Output:**
```
Tier Prices for Product: 24-MB01
Product Name: Joust Duffle Bag
Regular Price: $50.00

+----------------+----------+---------+------------+----------+
| Customer Group | Group ID | Min Qty | Tier Price | Discount |
+----------------+----------+---------+------------+----------+
| General        | 1        | 5+      | $45.00     | 10%      |
| General        | 1        | 10+     | $40.00     | 20%      |
| General        | 1        | 20+     | $35.00     | 30%      |
+----------------+----------+---------+------------+----------+
```

---

### Method 3: Import via CSV

#### Step 1: Prepare CSV File

Create a CSV file with the following columns:

```csv
sku,tier_price_website,tier_price_customer_group,tier_price_qty,tier_price
24-MB01,All Websites,General,5,45.00
24-MB01,All Websites,General,10,40.00
24-MB01,All Websites,General,20,35.00
24-MB02,All Websites,General,5,22.00
```

#### Step 2: Import via Admin

1. Go to **System > Data Transfer > Import**
2. Select **Entity Type**: Advanced Pricing
3. Upload your CSV file
4. Click **Check Data** then **Import**

---

### Method 4: Programmatic (PHP Code)

#### Add Tier Price via Code

```php
<?php
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory;

class TierPriceHelper
{
    private $productRepository;
    private $tierPriceFactory;
    
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductTierPriceInterfaceFactory $tierPriceFactory
    ) {
        $this->productRepository = $productRepository;
        $this->tierPriceFactory = $tierPriceFactory;
    }
    
    public function addTierPrice(string $sku, float $qty, float $price, int $customerGroupId = 1): void
    {
        $product = $this->productRepository->get($sku);
        
        $tierPrices = $product->getTierPrices() ?? [];
        
        $newTierPrice = $this->tierPriceFactory->create();
        $newTierPrice->setCustomerGroupId($customerGroupId);
        $newTierPrice->setQty($qty);
        $newTierPrice->setValue($price);
        
        $tierPrices[] = $newTierPrice;
        $product->setTierPrices($tierPrices);
        
        $this->productRepository->save($product);
    }
}
```

---

## Example: General Customer Group Tier Pricing

### Scenario
You want to offer volume discounts on a product (SKU: "WIDGET-001") for **General** customers only:

| Quantity | Regular Price | Tier Price | Discount |
|----------|---------------|------------|----------|
| 1-4      | $100.00       | $100.00    | 0%       |
| 5-9      | $100.00       | $90.00     | 10%      |
| 10-24    | $100.00       | $80.00     | 20%      |
| 25+      | $100.00       | $70.00     | 30%      |

### Setup via CLI

```bash
# Add tier prices for General customer group (ID: 1)
bin/magento sigma:tierprice:add WIDGET-001 5 90 --group=1
bin/magento sigma:tierprice:add WIDGET-001 10 80 --group=1
bin/magento sigma:tierprice:add WIDGET-001 25 70 --group=1

# Verify
bin/magento sigma:tierprice:list WIDGET-001

# Clear cache
bin/magento cache:flush
```

### Setup via Admin

1. Edit product "WIDGET-001"
2. Click **Advanced Pricing**
3. Add tier prices:
   - Website: All Websites, Group: General, Qty: 5, Price: 90.00
   - Website: All Websites, Group: General, Qty: 10, Price: 80.00
   - Website: All Websites, Group: General, Qty: 25, Price: 70.00
4. Click **Done** and **Save**

---

## Frontend Display

When tier pricing is configured, Magento displays it on the product page:

```
As low as $70.00

Buy 5 for $90.00 each and save 10%
Buy 10 for $80.00 each and save 20%
Buy 25 for $70.00 each and save 30%
```

---

## Installation

```bash
# Enable the module
bin/magento module:enable Sigma_TierPricing

# Run setup upgrade
bin/magento setup:upgrade

# Compile DI
bin/magento setup:di:compile

# Clear cache
bin/magento cache:flush
```

---

## File Structure

```
Sigma/TierPricing/
├── Console/
│   └── Command/
│       ├── AddTierPrice.php      # CLI command to add tier prices
│       └── ListTierPrices.php    # CLI command to list tier prices
├── etc/
│   ├── di.xml                    # DI configuration
│   └── module.xml                # Module declaration
├── registration.php              # Module registration
└── README.md                     # This documentation
```

---

## CLI Command Reference

### sigma:tierprice:add

Add a tier price to a product.

```
Usage:
  bin/magento sigma:tierprice:add <sku> <qty> <price> [--group=GROUP] [--website=WEBSITE]

Arguments:
  sku       Product SKU
  qty       Minimum quantity for tier price
  price     Tier price value

Options:
  -g, --group=GROUP       Customer group ID (default: 1 for General)
  -w, --website=WEBSITE   Website ID (default: 0 for all websites)
```

### sigma:tierprice:list

List all tier prices for a product.

```
Usage:
  bin/magento sigma:tierprice:list <sku>

Arguments:
  sku       Product SKU
```

---

## Troubleshooting

### Tier prices not showing on frontend

1. Clear all caches: `bin/magento cache:flush`
2. Reindex: `bin/magento indexer:reindex`
3. Check product is enabled and in stock

### Tier price not applying at checkout

1. Verify quantity in cart meets minimum tier quantity
2. Check customer is logged in to correct customer group
3. Clear quote: Customer should empty cart and re-add products

### CLI command not found

Run DI compile:
```bash
bin/magento setup:di:compile
bin/magento cache:flush
```

---

## Requirements

- Magento 2.4.x
- PHP 7.4 or higher

## Support

For issues or feature requests, please contact the development team.

## License

Proprietary - Sigma Development
