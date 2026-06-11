# NexRun Functional Specification Document (FSD)

Version: 1.0

Project: NexRun Premium Sportswear & Footwear E-Commerce Platform

Frontend: React

Backend: Laravel

Database: MySQL

---

# 1. Functional Overview

This document defines the detailed operational behavior, workflows, validation rules, permissions, and state transitions for all NexRun modules.

---

# 2. User Registration Workflow

## Registration Methods

* Email Registration
* Google Login
* Facebook Login
* Phone OTP Registration

---

## Registration Flow

Step 1

User opens registration page.

Step 2

User selects registration method.

Step 3

User submits required information.

Step 4

System validates information.

Step 5

Verification process initiated.

Step 6

User account created.

Step 7

Welcome notification sent.

---

## Validation Rules

Email:

* Must be unique
* RFC compliant

Password:

* Minimum 8 characters
* At least 1 uppercase letter
* At least 1 number
* At least 1 special character

Phone:

* Must be unique
* OTP verification required

---

# 3. Login Workflow

Supported Methods:

* Email + Password
* Google
* Facebook
* OTP Login

---

## Login Flow

User Login

→ Credential Validation

→ Authentication

→ Session Creation

→ Dashboard/Homepage

---

## Failed Login Rules

* 5 failed attempts
* Temporary lock for 15 minutes

---

# 4. Product Catalog Module

## Functionalities

* Product listing
* Product details
* Product filtering
* Product sorting
* Product search
* Product recommendations

---

## Product Search Filters

Category

Brand

Gender

Size

Color

Price Range

Availability

Popularity

Newest

---

## Product Detail Page Components

* Product Images
* Product Gallery
* Product Variants
* Reviews
* Related Products
* Size Guide
* Shipping Information

---

# 5. Product CRUD Matrix

| Operation      | Admin | Warehouse | Customer |
| -------------- | ----- | --------- | -------- |
| Create Product | Yes   | No        | No       |
| View Product   | Yes   | Yes       | Yes      |
| Update Product | Yes   | No        | No       |
| Delete Product | Yes   | No        | No       |

---

# 6. Inventory Management Workflow

Inventory managed per:

* Product
* Variant
* Size
* Color

---

## Inventory Update Flow

Stock Received

→ Inventory Update

→ Stock Recalculation

→ Availability Update

→ Storefront Update

---

## Low Stock Rule

Trigger alert when:

Stock < 10 units

---

## Inventory Validation

Cannot sell:

* Negative inventory
* Out-of-stock variants

---

# 7. Shopping Cart Workflow

Customer Actions

* Add Item
* Update Quantity
* Remove Item
* Save Cart

---

## Add To Cart Process

Select Product

→ Select Variant

→ Inventory Validation

→ Add To Cart

→ Cart Updated

---

## Validation

Quantity must not exceed available stock.

---

# 8. Wishlist Workflow

Customer can:

* Add product
* Remove product
* Move to cart

---

## Wishlist Rules

Duplicate products not allowed.

Maximum saved items:

500

---

# 9. Checkout Workflow

Customer

→ Cart Review

→ Address Selection

→ Shipping Calculation

→ Coupon Validation

→ Payment Selection

→ Order Creation

→ Payment Processing

→ Confirmation

---

# 10. Payment Processing Workflow

Supported Payments

* SSLCommerz
* bKash
* Nagad
* Rocket
* Stripe
* PayPal
* COD

---

## Payment Flow

Order Created

→ Payment Initiated

→ Gateway Processing

→ Verification

→ Order Confirmation

---

## Failure Handling

Payment Failed

→ Order Remains Pending

→ Inventory Released

→ Retry Available

---

# 11. Order Management Workflow

## Order Lifecycle

Draft

↓

Pending

↓

Paid

↓

Processing

↓

Packed

↓

Shipped

↓

Delivered

---

Alternative States

Cancelled

Returned

Refunded

Failed

---

## State Transition Matrix

| Current State | Next State |
| ------------- | ---------- |
| Pending       | Paid       |
| Paid          | Processing |
| Processing    | Packed     |
| Packed        | Shipped    |
| Shipped       | Delivered  |
| Delivered     | Returned   |
| Returned      | Refunded   |

---

# 12. Return Management Workflow

Customer submits return request.

↓

Return validation

↓

Admin approval

↓

Warehouse inspection

↓

Refund processing

↓

Case closed

---

## Return Rules

Return period:

7 Days

Product must:

* Be unused
* Include packaging
* Include invoice

---

# 13. Review & Rating Workflow

Eligibility:

Only verified buyers

---

## Review Process

Delivered Order

↓

Review Allowed

↓

Customer Submission

↓

Moderation

↓

Published

---

## Rating Scale

1 Star

2 Star

3 Star

4 Star

5 Star

---

# 14. Coupon Workflow

Coupon Types

* Fixed Amount
* Percentage
* Free Shipping

---

## Validation Rules

Check:

* Active Status
* Expiration Date
* Usage Limit
* Customer Eligibility
* Minimum Purchase

---

# 15. Loyalty Program Workflow

Customer Purchase

↓

Order Delivered

↓

Points Awarded

↓

Balance Updated

↓

Points Redeemed

---

## Loyalty Tiers

Bronze

Silver

Gold

Platinum

---

## Tier Upgrade Rules

Based on:

* Lifetime Spending
* Completed Orders

---

# 16. Referral Program Workflow

Customer Generates Referral Link

↓

Friend Registers

↓

Friend Places Order

↓

Order Delivered

↓

Reward Issued

---

# 17. Recommendation Engine Workflow

Input Sources

* Purchase History
* Wishlist
* Viewed Products
* Search History

---

## Recommendation Types

* Similar Products
* Trending Products
* Frequently Bought Together
* Personalized Suggestions

---

# 18. Notification Workflow

Channels

* Email
* SMS
* In-App

---

## Trigger Events

Registration

Password Reset

Order Placement

Payment Success

Shipment Update

Return Approval

Promotion Campaign

---

# 19. Customer Support Workflow

Customer Creates Ticket

↓

Ticket Assignment

↓

Support Response

↓

Resolution

↓

Customer Confirmation

↓

Closed

---

## Priority Levels

Low

Medium

High

Critical

---

# 20. Reporting Workflows

Sales Report

Generated:

* Daily
* Weekly
* Monthly
* Yearly

---

Inventory Report

Generated:

* Daily

---

Marketing Report

Generated:

* Weekly

---

Customer Report

Generated:

* Monthly

---

# 21. Role Permission Matrix

## Customer

Can:

* Purchase products
* Manage account
* Submit reviews
* Use wishlist

Cannot:

* Access admin areas

---

## Support Agent

Can:

* Manage tickets
* View customer records

Cannot:

* Modify products

---

## Warehouse Manager

Can:

* Manage inventory
* Process returns

Cannot:

* Manage users

---

## Marketing Manager

Can:

* Manage coupons
* Manage campaigns

Cannot:

* Modify inventory

---

## Administrator

Full Access

All modules

All reports

All configurations

---

# 22. Audit Trail Requirements

System must log:

* User logins
* Password changes
* Product updates
* Inventory updates
* Order modifications
* Refund actions
* Coupon changes
* Permission changes

Audit Log Data:

* User ID
* Action
* Module
* Timestamp
* IP Address

---

# 23. Error Handling Requirements

System must handle:

* Payment failures
* Inventory conflicts
* Session expiration
* API failures
* Validation failures

Every critical error must:

* Be logged
* Generate alert
* Be recoverable

---

# 24. Edge Case Handling

Case 1

Product stock reaches zero during checkout.

Action:

Block purchase.

---

Case 2

Coupon expires during checkout.

Action:

Revalidate before payment.

---

Case 3

Multiple customers purchase last item simultaneously.

Action:

Inventory reservation lock.

---

Case 4

Payment success but callback delayed.

Action:

Queue reconciliation process.

---

END OF FUNCTIONAL SPECIFICATION DOCUMENT
