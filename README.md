# Blood Management System

A Laravel 12-based web application for managing blood banks, blood bags, refrigerators, and donor safety workflows.

## Table of Contents

- [Overview](#overview)
- [Key Features](#key-features)
- [Technology Stack](#technology-stack)
- [Setup](#setup)
- [Database](#database)

## Overview

This project provides a hospital-style blood management system that tracks:

- Blood bags by type, donor, quantity, status, and expiry
- Blood bank locations and inventory
- Refrigerators and temperature monitoring
- Staff credentials and blood safety checks

It also includes business rules to enforce safe donation workflows and prevent invalid deletions.

## Key Features

- Create, edit, and delete blood bag records
- Mandatory blood safety verification for donation
- Only allow deletion of blood bags when status is `expired`
- AJAX-powered form submission with client-side feedback
- SweetAlert confirmations for deletion actions
- Temperature Monitoring and dedicated 
## Dashboard

The dashboard provides a real-time overview of blood bank safety and inventory:

- **Total Bags**: total number of blood bag records.
- **Active Fridges**: number of refrigerators currently active and monitoring blood storage.
- **Expired Bags**: count of blood bags that have passed their expiry date.
- **Health Score**: percentage of today's refrigerator temperature readings that fall within the safe range of `2°C - 6°C`.
- **Avg Temp Today**: average temperature across all temperature logs recorded today.
- **Stock by Blood Group**: current inventory grouped by blood type.
- **Critical Temperature Alerts**: recent refrigerator records where the temperature exceeded `8°C`, indicating potential spoilage risk.

This helps staff quickly assess blood availability, storage health, and urgent temperature incidents.
## Technology Stack

- PHP 8+
- Laravel 12
- Blade templates
- Bootstrap
- jQuery
- MySQL

## Database and Tables

This project uses a MySQL-compatible database. Below are the main tables and important columns.

- **blood_bags**
  - `id` (bigint, PK) — primary key
  - `blood_group` (string) — e.g. A+, O-
  - `donor_name` (string, nullable)
  - `quantity` (integer)
  - `status` (string) — e.g. `available`, `used`, `expired`
  - `expiry_date` (date)
  - `is_tested` (boolean) — must be true before saving donation
  - `is_secure` (boolean) — must be true before saving donation
  - `blood_bank_id` (bigint, FK)
  - `refrigerator_id` (bigint, FK, nullable)
  - `created_at`, `updated_at` (timestamps)

- **blood_banks**
  - `id`, `name` (string), `location` (string), `contact_email` (string), timestamps

- **blood_bank_users**
  - `id`, `blood_bank_id` (FK), `user_id` (FK), `role` (string), timestamps

- **refrigerators**
  - `id`, `name` (string), `blood_bank_id` (FK), `is_active` (boolean), `min_temp` (decimal), `max_temp` (decimal), timestamps

- **temperature_logs**
  - `id`, `refrigerator_id` (FK), `temperature` (decimal), `recorded_at` (datetime), timestamps

- **temperature_alerts**
  - `id`, `temperature_log_id` (FK), `refrigerator_id` (FK), `level` (string), `message` (text), `resolved_at` (datetime, nullable), timestamps

- **users**
  - `id`, `name`, `email`, `password`, `role` (admin|staff|monitoring_user), `email_verified_at`, timestamps

Notes:
- The `is_tested` and `is_secure` boolean fields were added by a migration and are required when creating a blood bag (validation enforced in the request/controller).
- Deletion of blood bag records is restricted to bags with `status = 'expired'` (enforced server-side).

Migrations:
```bash
php artisan migrate

## Setup

1. Clone the repository:
   ```bash
   git clone <repo-url>
