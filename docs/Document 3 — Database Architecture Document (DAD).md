# NexRun Database Architecture Document (DAD)

Version: 1.0

Project: NexRun Premium Sportswear & Footwear E-Commerce Platform

Database Engine: MySQL 8+

Architecture Type: Relational Database Architecture

Database Strategy: Modular Enterprise E-Commerce Schema

---

# 1. Database Design Objectives

The database architecture must support:

* 1,000+ products initially
* Future scaling to 100,000+ products
* High-performance product search
* Variant inventory management
* Real-time order processing
* Loyalty and referral systems
* Payment integrations
* Analytics reporting
* Audit logging

---

# 2. Core Entity Groups

The system is divided into the following domains:

1. Authentication Domain
2. Product Domain
3. Inventory Domain
4. Customer Domain
5. Cart Domain
6. Order Domain
7. Payment Domain
8. Marketing Domain
9. Support Domain
10. Analytics Domain
11. Audit Domain

---

# 3. Authentication Domain

## users

Stores all registered users.

Fields:

* id
* uuid
* first_name
* last_name
* email
* phone
* password_hash
* avatar
* status
* email_verified_at
* phone_verified_at
* last_login_at
* created_at
* updated_at
* deleted_at

Indexes:

* email UNIQUE
* phone UNIQUE
* status

---

## roles

Fields:

* id
* role_name
* description

Examples:

* Customer
* Admin
* Warehouse Manager
* Marketing Manager
* Support Agent

---

## permissions

Fields:

* id
* permission_name
* module
* description

---

## role_permissions

Relationship Table

Fields:

* role_id
* permission_id

---

## user_roles

Relationship Table

Fields:

* user_id
* role_id

---

# 4. Customer Domain

## customer_profiles

Fields:

* id
* user_id
* gender
* birth_date
* loyalty_points
* loyalty_tier
* total_spent
* referral_code
* referred_by

---

## customer_addresses

Fields:

* id
* customer_id
* address_type
* recipient_name
* phone
* district
* city
* postal_code
* address_line_1
* address_line_2
* is_default

Address Types:

* Shipping
* Billing

---

# 5. Product Domain

## categories

Fields:

* id
* parent_id
* category_name
* slug
* description
* image
* status

---

## brands

Fields:

* id
* brand_name
* slug
* description
* logo

Note:

NexRun will initially use one brand.

Table maintained for future expansion.

---

## products

Fields:

* id
* sku
* category_id
* brand_id
* product_name
* slug
* short_description
* description
* base_price
* sale_price
* status
* featured
* created_at
* updated_at

---

## product_images

Fields:

* id
* product_id
* image_url
* image_order
* is_primary

---

## sizes

Fields:

* id
* size_code

Examples:

* XS
* S
* M
* L
* XL
* 38
* 39
* 40
* 41
* 42

---

## colors

Fields:

* id
* color_name
* hex_code

---

## product_variants

Fields:

* id
* product_id
* size_id
* color_id
* variant_sku
* barcode
* price_adjustment
* status

Each variant represents:

Product + Size + Color

---

# 6. Inventory Domain

## warehouses

Fields:

* id
* warehouse_name
* location
* status

Initially:

Single warehouse

---

## inventory

Fields:

* id
* warehouse_id
* product_variant_id
* quantity_available
* quantity_reserved
* reorder_level
* updated_at

---

## inventory_transactions

Fields:

* id
* inventory_id
* transaction_type
* quantity
* previous_balance
* new_balance
* remarks
* created_by
* created_at

Transaction Types:

* Purchase
* Sale
* Return
* Adjustment

---

# 7. Cart Domain

## carts

Fields:

* id
* customer_id
* created_at
* updated_at

---

## cart_items

Fields:

* id
* cart_id
* product_variant_id
* quantity
* unit_price

---

# 8. Wishlist Domain

## wishlists

Fields:

* id
* customer_id

---

## wishlist_items

Fields:

* id
* wishlist_id
* product_id

---

# 9. Order Domain

## orders

Fields:

* id
* order_number
* customer_id
* shipping_address_id
* billing_address_id
* subtotal
* discount_amount
* shipping_amount
* tax_amount
* total_amount
* payment_status
* order_status
* placed_at

---

## order_items

Fields:

* id
* order_id
* product_variant_id
* quantity
* unit_price
* discount_amount
* total_price

---

## order_status_history

Fields:

* id
* order_id
* previous_status
* new_status
* changed_by
* changed_at

---

# 10. Payment Domain

## payment_methods

Fields:

* id
* method_name
* status

Examples:

* SSLCommerz
* bKash
* Nagad
* Rocket
* Stripe
* PayPal
* COD

---

## payments

Fields:

* id
* order_id
* payment_method_id
* transaction_reference
* amount
* payment_status
* gateway_response
* paid_at

---

## refunds

Fields:

* id
* payment_id
* refund_amount
* refund_reason
* refund_status
* refunded_at

---

# 11. Marketing Domain

## coupons

Fields:

* id
* coupon_code
* coupon_type
* value
* minimum_purchase
* start_date
* end_date
* usage_limit
* status

---

## coupon_usage

Fields:

* id
* coupon_id
* customer_id
* order_id
* used_at

---

## referrals

Fields:

* id
* referrer_id
* referred_customer_id
* reward_points
* reward_status

---

## loyalty_transactions

Fields:

* id
* customer_id
* transaction_type
* points
* remarks
* created_at

---

# 12. Review Domain

## reviews

Fields:

* id
* product_id
* customer_id
* rating
* review_text
* status
* created_at

---

# 13. Support Domain

## support_tickets

Fields:

* id
* ticket_number
* customer_id
* assigned_to
* priority
* status
* subject
* description
* created_at

---

## support_messages

Fields:

* id
* ticket_id
* sender_id
* message
* created_at

---

# 14. Notification Domain

## notifications

Fields:

* id
* user_id
* notification_type
* title
* message
* read_status
* sent_at

---

# 15. Recommendation Domain

## product_views

Fields:

* id
* customer_id
* product_id
* viewed_at

---

## recommendation_logs

Fields:

* id
* customer_id
* product_id
* recommendation_type
* generated_at

---

# 16. Analytics Domain

## sales_summary_daily

Fields:

* sales_date
* total_orders
* total_revenue
* total_customers

---

## product_analytics

Fields:

* product_id
* views
* purchases
* revenue_generated

---

## customer_analytics

Fields:

* customer_id
* total_orders
* lifetime_value
* average_order_value

---

# 17. Audit Domain

## audit_logs

Fields:

* id
* user_id
* module_name
* action_type
* entity_name
* entity_id
* old_values
* new_values
* ip_address
* user_agent
* created_at

---

# 18. Relationship Mapping

users
↓
customer_profiles

users
↓
user_roles
↓
roles

categories
↓
products
↓
product_variants
↓
inventory

customers
↓
carts
↓
cart_items

customers
↓
orders
↓
order_items

orders
↓
payments
↓
refunds

products
↓
reviews

customers
↓
support_tickets

---

# 19. Soft Delete Strategy

Soft delete enabled for:

* Users
* Products
* Categories
* Coupons
* Reviews

Field:

deleted_at

Purpose:

* Recovery
* Audit retention
* Regulatory compliance

---

# 20. Indexing Strategy

Primary Indexes

* Primary Keys

Secondary Indexes

Products:

* sku
* slug
* category_id
* status

Orders:

* customer_id
* order_status
* placed_at

Inventory:

* product_variant_id

Payments:

* transaction_reference

Reviews:

* product_id

Notifications:

* user_id

---

# 21. Scalability Strategy

Phase 1

Single MySQL Instance

Expected:

* 1,000 Products
* 1,000 Monthly Users

---

Phase 2

Read Replicas

Expected:

* 100,000 Users

---

Phase 3

Database Partitioning

Partition:

* Orders
* Payments
* Audit Logs

---

# 22. Backup Strategy

Daily Backup

Retention:

30 Days

Weekly Snapshot

Retention:

12 Months

Monthly Snapshot

Retention:

5 Years

---

# 23. Data Retention Policy

Audit Logs:

5 Years

Orders:

10 Years

Payments:

10 Years

Customer Data:

Until deletion request

Analytics:

Indefinite

---

END OF DATABASE ARCHITECTURE DOCUMENT
