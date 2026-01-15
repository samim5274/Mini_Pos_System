# Multi-Tenant POS / Inventory Management Backend

**Laravel ভিত্তিক API-first Backend System**

---

## ১. Project Overview

এই প্রজেক্টটি একটি **Multi-Tenant POS & Inventory Management Backend** তৈরি করার জন্য ডিজাইন করা হয়েছে।  
সিস্টেমটি **production-ready**, **scalable**, **secure**, এবং **API-first** আর্কিটেকচার অনুযায়ী তৈরি।  
প্রতিটি ব্যবসা (Tenant) সম্পূর্ণ আলাদা ডেটা ব্যবহার করবে এবং অন্য Tenant-এর ডেটা অ্যাক্সেস করা যাবে না।  

**Core Features:**
- API-first architecture
- Multi-Tenant data isolation
- Role-based Access Control (RBAC)
- Inventory & Order management
- Reporting module
- Secure & validated APIs

---

## ২. Authentication & Authorization

- **Authentication:** Laravel Sanctum (Token-based)
- **User Roles:**
  - **Owner:** সম্পূর্ণ কন্ট্রোল (Products, Orders, Staff, Reports)
  - **Staff:** সীমিত কন্ট্রোল (Orders, View Products)
- **Authorization:** Laravel Policies / Gates (Controller-এর ভিতরে hard-coded logic নেই)
- **Tenant-aware:** Tenant context নির্ধারণ করতে `X-Tenant-ID` header ব্যবহার

**Sample Header:**

---

## ৩. Multi-Tenancy

- প্রতিটি tenant-এর জন্য আলাদা `tenant_id` database field
- **Global Scope** ব্যবহার করে automatic Tenant Isolation
- Tenant Isolation enforced in:
  - Database queries
  - Authorization checks
  - Business logic
- Cross-tenant data access বা inference অসম্ভব

---

## ৪. Inventory & Order Management

### Product Attributes
- Name
- SKU (Tenant-wise unique)
- Price
- Stock Quantity
- Low Stock Threshold

### Order Rules
- Multiple products per order
- Stock deducted automatically on order creation
- Negative inventory prevented
- Database transactions ensure consistency

### Order Status
- Pending
- Paid
- Cancelled

### Order Cancellation
- Stock restored correctly upon order cancellation

---

## ৫. Reporting Module

**Supported Reports:**
1. Daily Sales Summary
2. Top 5 Selling Products (Date Range)
3. Low Stock Report
4. Individual Product Sales
5. Customer-wise Order Summary

**Performance Considerations:**
- Queries optimized
- Eager loading applied
- Proper database indexing
- Avoid N+1 query issues

---

## ৬. Validation & Security

- Form Request Validation for all inputs
- Mass-assignment protection using `$fillable`
- Policy-based Authorization
- Tenant Isolation enforced
- API rate limiting
- Secure error handling without exposing sensitive information

---

## ৭. Performance Optimization

- Eager Loading wherever applicable
- Database indexing on frequently queried columns
- Query optimization
- Pagination applied on list endpoints

---

## ৮. API Design Standards

- RESTful API conventions
- Consistent JSON response format
- Laravel API Resource usage
- Pagination included in list endpoints

**Sample JSON Response**
```json
{
  "success": true,
  "data": {},
  "meta": {}
}







## Installation & Setup

### Project Clone

```bash
git clone <repository-url>
cd <project-folder>
composer install
cp .env.example .env

APP_NAME=MultiTenantPOS
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

php artisan key:generate

php artisan migrate --seed

php artisan storage:link

php artisan serve --port=8080



API Test Software: Postman 

Important: সব API request-এ include করতে হবে

Authorization: Bearer <token>
Content-Type: application/json
X-Tenant-ID: <tenant_uuid>

php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

