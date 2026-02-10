# Sigma_CustomSupport

Custom CRUD module providing a frontend customer support form with Google reCAPTCHA and admin grid management.

## Features

- Frontend support form at `/customersupport`
- Custom database table: `sigma_custom_support`
- Client-side validation (Magento data-validate)
- Server-side validation (controller)
- Google reCAPTCHA v2 integration (frontend JS + backend API verification)
- Admin grid with View, Edit, Delete operations
- Admin form for updating records
- Repository pattern (Service Contracts)

## Installation

```bash
bin/magento module:enable Sigma_CustomSupport
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

## Google reCAPTCHA Setup

### Step 1: Get reCAPTCHA Keys

1. Go to [Google reCAPTCHA Admin](https://www.google.com/recaptcha/admin)
2. Register a new site
3. Choose **reCAPTCHA v2 → "I'm not a robot" Checkbox**
4. Add your domain(s)
5. Copy the **Site Key** and **Secret Key**

### Step 2: Configure in Magento Admin

1. Go to **Stores > Configuration > General > Customer Support**
2. Under **Google reCAPTCHA Settings**:
   - **Enable reCAPTCHA**: Yes
   - **Site Key**: Paste your site key
   - **Secret Key**: Paste your secret key
3. Click **Save Config**
4. Flush cache: `bin/magento cache:flush`

## Database Table

**Table**: `sigma_custom_support`

| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK, AI) | Record ID |
| name | VARCHAR(255) | Customer name |
| email | VARCHAR(255) | Customer email |
| contact_number | VARCHAR(20) | Contact number |
| message | TEXT | Support message |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

## Frontend URL

`/customersupport`

### Validation Rules

**Client-side** (Magento jQuery validation):
- Name: Required, 2-255 characters
- Email: Required, valid email format
- Contact Number: Required, valid phone format
- Message: Required, minimum 10 characters
- reCAPTCHA: Must be completed (when enabled)

**Server-side** (Controller):
- Form key validation
- All field validations (same rules)
- reCAPTCHA token verification via Google API

## Admin Panel

### Grid: View All Records

Navigate to: **Customer Support > Support Requests**

Features:
- Sortable columns
- Filterable columns
- Full-text search
- Date range filters
- Edit/Delete actions per row

### Edit Form

Click **Edit** on any row to:
- View record details
- Update name, email, contact number, message
- Save changes
- Delete record

## File Structure

```
Sigma/CustomSupport/
├── Api/
│   ├── Data/SupportInterface.php
│   └── SupportRepositoryInterface.php
├── Block/
│   ├── Adminhtml/Support/Edit/
│   │   ├── DeleteButton.php
│   │   └── SaveButton.php
│   └── SupportForm.php
├── Controller/
│   ├── Adminhtml/Support/
│   │   ├── Delete.php
│   │   ├── Edit.php
│   │   ├── Index.php
│   │   └── Save.php
│   └── Index/
│       ├── Index.php
│       └── Save.php
├── Model/
│   ├── ResourceModel/
│   │   └── Support/
│   │       └── Collection.php
│   │   └── Support.php
│   ├── Support/
│   │   └── DataProvider.php
│   ├── Support.php
│   └── SupportRepository.php
├── Ui/Component/Listing/Column/
│   └── Actions.php
├── etc/
│   ├── adminhtml/
│   │   ├── menu.xml
│   │   ├── routes.xml
│   │   └── system.xml
│   ├── frontend/
│   │   └── routes.xml
│   ├── acl.xml
│   ├── config.xml
│   ├── db_schema.xml
│   ├── di.xml
│   └── module.xml
├── view/
│   ├── adminhtml/
│   │   ├── layout/
│   │   │   ├── sigma_customsupport_support_edit.xml
│   │   │   └── sigma_customsupport_support_index.xml
│   │   └── ui_component/
│   │       ├── sigma_customsupport_form.xml
│   │       └── sigma_customsupport_listing.xml
│   └── frontend/
│       ├── layout/
│       │   └── customersupport_index_index.xml
│       └── templates/
│           └── form.phtml
├── README.md
└── registration.php
```

## Requirements

- Magento 2.4.x
- PHP 8.1+
- Google reCAPTCHA v2 keys (for reCAPTCHA feature)
