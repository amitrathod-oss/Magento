# Sigma_OfflinePayment

Custom offline payment method for Magento 2 with auto-invoice generation and amount capture.

## Features

- Offline payment method visible in checkout
- Payment Action: Authorize and Capture (configurable from admin)
- Allowed Countries: USA only (configurable)
- Auto-invoice generation on order placement
- Amount capture on successful payment
- Admin configurable payment instructions
- Proper Magento 2 payment method architecture

## Installation

```bash
bin/magento module:enable Sigma_OfflinePayment
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

## Admin Configuration

Navigate to: **Stores > Configuration > Sales > Payment Methods > Offline Payment Method (Sigma)**

| Setting | Default | Description |
|---------|---------|-------------|
| Enabled | Yes | Enable/disable payment method |
| Title | Offline Payment Method | Title shown in checkout |
| Payment Action | Authorize and Capture | Authorize Only or Authorize and Capture |
| Auto Generate Invoice | Yes | Auto-generate invoice and capture amount |
| New Order Status | Processing | Order status after placement |
| Instructions | Pay offline using cash... | Instructions shown in checkout |
| Payment from Applicable Countries | Specific Countries | All or Specific |
| Payment from Specific Countries | United States | Allowed countries |
| Sort Order | 100 | Display order in checkout |

## How It Works

### Payment Flow

1. Customer selects "Offline Payment Method" at checkout
2. Customer clicks "Place Order"
3. Magento creates the order with `authorize_capture` payment action
4. The `capture()` method marks payment as captured
5. Observer `AutoInvoice` triggers on `sales_order_place_after`
6. Invoice is auto-generated and registered
7. Order status moves to "Processing"
8. Order history shows auto-generated invoice comment

### Auto Invoice Logic

- Triggers only for `sigma_offline_payment` method
- Checks `auto_invoice` admin config flag
- Skips if order already has invoices
- Captures amount via `CAPTURE_ONLINE`
- Adds order comment with invoice number
- Logs errors without breaking order flow

## File Structure

```
Sigma/OfflinePayment/
├── etc/
│   ├── adminhtml/
│   │   └── system.xml              # Admin configuration fields
│   ├── frontend/
│   │   └── di.xml                   # Config provider registration
│   ├── config.xml                   # Default config values
│   ├── events.xml                   # Observer registration
│   └── module.xml                   # Module declaration
├── Model/
│   ├── Source/
│   │   └── PaymentAction.php        # Payment action dropdown source
│   ├── ConfigProvider.php           # Checkout config provider
│   └── OfflinePayment.php           # Payment method model
├── Observer/
│   └── AutoInvoice.php              # Auto-invoice generation
├── view/
│   └── frontend/
│       ├── layout/
│       │   └── checkout_index_index.xml  # Payment renderer registration
│       └── web/
│           ├── js/view/payment/
│           │   ├── method-renderer/
│           │   │   └── offline-payment-method.js  # Method renderer
│           │   └── offline-payment.js              # Renderer list
│           └── template/payment/
│               └── offline-payment.html            # Knockout template
├── README.md
└── registration.php
```

## Requirements

- Magento 2.4.x
- PHP 8.1+
