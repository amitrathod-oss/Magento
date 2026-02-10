# Sigma_InfiniteScroll

Replaces default Magento pagination with infinite scroll on product listing (category) pages.

## Features

- Removes default pagination on category pages
- Loads next page products automatically on scroll
- Uses Intersection Observer API for optimal performance
- Smooth fade-in animation for new products
- Loading spinner indicator
- "All products loaded" end message
- Admin configurable (enable/disable, scroll threshold)
- Fallback scroll handler for older browsers
- Triggers `contentUpdated` for Magento widget compatibility

## Installation

```bash
bin/magento module:enable Sigma_InfiniteScroll
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

## Admin Configuration

**Stores > Configuration > Catalog > Infinite Scroll**

| Setting | Default | Description |
|---------|---------|-------------|
| Enable Infinite Scroll | Yes | Enable/disable infinite scroll |
| Scroll Threshold (px) | 300 | Distance from bottom to trigger loading |

## How It Works

1. Page loads with first batch of products (normal Magento behavior)
2. Bottom pagination is hidden via CSS
3. Intersection Observer watches a sentinel element near page bottom
4. When user scrolls near the bottom (within threshold), AJAX fetches next page
5. New product items are extracted from AJAX response and appended
6. Products fade in with smooth animation
7. Repeats until all pages are loaded
8. Shows "All products loaded" when finished

## File Structure

```
Sigma/InfiniteScroll/
├── Block/
│   └── InfiniteScroll.php                    # Block with config
├── etc/
│   ├── adminhtml/
│   │   └── system.xml                        # Admin config
│   ├── config.xml                            # Defaults
│   └── module.xml                            # Module declaration
├── view/frontend/
│   ├── layout/
│   │   └── catalog_category_view.xml         # Layout
│   ├── templates/
│   │   └── infinite-scroll.phtml             # JS init template
│   └── web/
│       ├── css/infinite-scroll.css            # Styles
│       └── js/infinite-scroll.js             # Core JS logic
├── README.md
└── registration.php
```

## Requirements

- Magento 2.4.x
- PHP 8.1+
