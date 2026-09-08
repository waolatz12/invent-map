# InventMap

> **Multi-tenant Inventory Management API built with Laravel**

InventMap is a production-oriented, API-first inventory management platform designed to provide organizations with a reliable way to manage products, inventory, warehouses, stock movements, transfers, purchasing, sales and inventory reporting.

The system is designed primarily as a backend API that can be consumed by web applications, mobile applications and other authorized clients.

Rather than treating inventory management as a collection of CRUD endpoints, InventMap models inventory as a set of business processes and state transitions. The application emphasizes domain boundaries, tenant isolation, authorization, transactional consistency, API security and automated testing.

---

## Project Status

**Status:** Active Development

InventMap is being developed incrementally, beginning with the authentication, authorization and multi-tenant foundation before implementing the inventory domain.

### Current focus

* Authentication
* Email verification
* Password reset
* Multi-tenancy
* Role-based authorization
* Permission management
* Tenant isolation
* API architecture
* Automated testing

### Planned modules

* Organizations
* Products
* Categories
* Warehouses
* Inventory
* Stock movements
* Inventory adjustments
* Stock transfers
* Purchase orders
* Sales orders
* Suppliers
* Customers
* Reporting
* Audit logging

---

# Table of Contents

* [Problem](#problem)
* [Goals](#goals)
* [Core Capabilities](#core-capabilities)
* [Technical Stack](#technical-stack)
* [Architecture](#architecture)
* [Architectural Principles](#architectural-principles)
* [Design Patterns](#design-patterns)
* [Authentication](#authentication)
* [Authorization](#authorization)
* [Multi-Tenancy](#multi-tenancy)
* [API Design](#api-design)
* [Domain Design](#domain-design)
* [Security](#security)
* [Database](#database)
* [Testing Strategy](#testing-strategy)
* [Project Structure](#project-structure)
* [Development Setup](#development-setup)
* [Demo Credentials](#demo-credentials)
* [API Documentation](#api-documentation)
* [Engineering Decisions](#engineering-decisions)
* [Roadmap](#roadmap)

---

# Problem

Many businesses manage inventory through spreadsheets, disconnected systems or applications that provide limited visibility into stock levels and stock movements.

This creates problems such as:

* Inaccurate stock quantities
* Lack of visibility across warehouses
* Duplicate or inconsistent product records
* Unauthorized inventory adjustments
* Difficult reconciliation of stock movements
* Poor traceability
* Weak access control
* Difficult integration with existing business applications

InventMap addresses these problems through an API-first architecture that allows inventory capabilities to be consumed by multiple client applications.

---

# Goals

InventMap is designed around several engineering goals:

### 1. Correctness

Inventory quantities must remain consistent with the underlying stock movements.

### 2. Security

Users must only access resources they are authorized to access.

### 3. Tenant isolation

Organizations must never be able to access another organization's data.

### 4. Maintainability

Business logic should not become tightly coupled to controllers or HTTP requests.

### 5. Testability

Core business behaviour should be independently testable.

### 6. Extensibility

The architecture should allow additional clients, modules and integrations without rewriting the core application.

### 7. API-first design

The backend should expose predictable APIs that can be consumed by web, mobile and third-party clients.

---

# Core Capabilities

## Identity and Access Management

* User registration
* Email verification
* Login
* Logout
* Password reset
* Password recovery
* API token authentication
* Role-based authorization
* Permission-based authorization

## Organization Management

* Organization creation
* Organization membership
* Organization switching
* Organization-level roles
* Organization-level permissions

## Product Management

* Product creation
* Product updates
* Product archival
* Product categorization
* Product identification

## Inventory

* Stock levels
* Inventory receiving
* Inventory deduction
* Inventory adjustments
* Stock movement history

## Warehousing

* Multiple warehouses
* Warehouse-specific stock
* Stock transfers
* Transfer approval
* Transfer dispatch
* Transfer receiving

## Procurement

* Suppliers
* Purchase orders
* Purchase order receiving

## Sales

* Customers
* Sales orders
* Order fulfillment
* Inventory deduction

## Reporting

* Inventory reports
* Stock movement reports
* Operational reports

---

# Technical Stack

| Technology                | Purpose                                  |
| ------------------------- | ---------------------------------------- |
| PHP                       | Application language                     |
| Laravel                   | Backend framework                        |
| Laravel Sanctum           | API authentication                       |
| Spatie Laravel Permission | Roles and permissions                    |
| PostgreSQL                | Primary database                         |
| Redis                     | Caching / queues / rate limiting support |
| PHPUnit / Pest            | Automated testing                        |
| Docker                    | Local/production containerization        |
| REST                      | API communication                        |
| Git                       | Version control                          |

---

# Architecture

InventMap uses a modular Laravel application architecture.

The application is organized around clear responsibilities rather than placing all business logic inside controllers.

The high-level request flow is:

```text
HTTP Request
     │
     ▼
Authentication
     │
     ▼
Email Verification
     │
     ▼
Organization Context
     │
     ▼
Authorization
     │
     ▼
Form Request Validation
     │
     ▼
DTO
     │
     ▼
Action / Application Service
     │
     ▼
Domain Logic
     │
     ▼
Database Transaction
     │
     ▼
Resource / API Response
```

---

# Architectural Principles

## Separation of Concerns

Controllers are responsible primarily for HTTP concerns.

They should not contain large amounts of business logic.

Instead:

```text
Controller
    ↓
Request validation
    ↓
DTO
    ↓
Action / Service
    ↓
Domain operation
```

---

## Single Responsibility Principle

Classes should have one primary reason to change.

For example:

```text
RegisterUser
```

is responsible for the registration workflow.

It should not also be responsible for:

* formatting HTTP responses
* validating HTTP input
* sending arbitrary emails
* generating reports

---

## Dependency Inversion

Application services depend on abstractions where introducing an abstraction provides genuine value.

Laravel's dependency injection container is used to resolve application dependencies.

---

## Explicit Boundaries

External input enters the application through validation boundaries.

The application does not trust:

* Client-provided IDs
* Organization IDs
* Prices
* Quantities
* Status transitions
* Role IDs

---

# Design Patterns

InventMap deliberately uses established patterns where they solve real problems.

## Action Pattern

Actions represent meaningful application operations.

Examples:

```text
RegisterUser
LoginUser
CreateProduct
AdjustInventory
ReceiveStock
CreateTransfer
ApproveTransfer
```

An action represents something the system does.

---

## DTO Pattern

Data Transfer Objects carry validated data between application boundaries.

Examples:

```text
RegisterUserData
LoginData
CreateProductData
AdjustInventoryData
CreateTransferData
```

DTOs do not perform business operations.

Their responsibility is structured data transport.

---

## Service Layer

Services encapsulate reusable application/domain operations that don't naturally belong to a controller or model.

Example:

```text
AuthenticationService
InventoryService
TransferService
```

---

## Policy Pattern

Policies encapsulate resource-level authorization.

For example:

```text
ProductPolicy
TransferPolicy
PurchaseOrderPolicy
OrganizationPolicy
```

---

## Repository Pattern

Repositories will only be introduced where they provide a meaningful abstraction around complex persistence behaviour.

InventMap intentionally avoids creating repositories for simple Eloquent CRUD operations merely for the sake of applying a pattern.

---

## Strategy Pattern

The Strategy pattern may be used for business rules that have multiple interchangeable implementations.

Potential examples include:

```text
Inventory valuation strategy
Pricing strategy
Stock allocation strategy
```

---

## Factory Pattern

Factories are used extensively in automated tests to create realistic domain data.

---

# Authentication

InventMap uses Laravel Sanctum for API authentication.

Supported authentication scenarios include:

* API bearer tokens
* First-party clients
* Mobile applications
* Controlled third-party integrations

Protected API requests use:

```http
Authorization: Bearer {token}
```

Authentication answers:

> Who is making this request?

Authorization answers:

> What is that user allowed to do?

These concerns are deliberately kept separate.

---

# Email Verification

New accounts must verify their email address.

Registration produces:

```text
User
   ↓
Registered Event
   ↓
Verification Notification
   ↓
Signed Verification URL
   ↓
Verification Endpoint
   ↓
email_verified_at
```

Sensitive business functionality can require:

```text
auth:sanctum
+
verified
```

---

# Password Recovery

InventMap uses Laravel's password broker for password recovery.

The flow is:

```text
Forgot Password
       ↓
Password Reset Notification
       ↓
Reset Token
       ↓
Reset Password
       ↓
Password Updated
       ↓
Existing API Tokens Revoked
```

The forgot-password endpoint does not reveal whether an email address belongs to an existing user.

---

# Authorization

InventMap uses:

* Laravel Policies
* Laravel Gates
* Spatie Laravel Permission

Example permissions:

```text
products.view
products.create
products.update
products.archive

inventory.view
inventory.adjust
inventory.receive
inventory.deduct

transfers.view
transfers.create
transfers.approve
transfers.dispatch
transfers.receive
transfers.cancel

purchase_orders.view
purchase_orders.create
purchase_orders.receive

sales_orders.view
sales_orders.create
sales_orders.fulfill

reports.view

users.manage

organization.view
organization.manage
```

---

# Multi-Tenancy

InventMap is designed as a multi-tenant system.

The tenant is represented by an:

```text
Organization
```

A user may belong to multiple organizations.

For example:

```text
John

Acme Electronics
    → Admin

Global Retail
    → Viewer
```

The current organization is established through:

```http
X-Organization-ID: {organization_id}
```

A request therefore follows:

```text
Authenticated User
        ↓
Current Organization
        ↓
Organization Membership
        ↓
Role
        ↓
Permission
        ↓
Policy
        ↓
Tenant-scoped Resource
```

Tenant isolation is enforced server-side.

Frontend filtering is never considered a security boundary.

---

# Tenant Security

Every tenant-owned resource contains an organization relationship.

Queries must always respect the current tenant.

Unsafe:

```php
Product::findOrFail($id);
```

Preferred:

```php
Product::where(
    'organization_id',
    $organizationId
)->findOrFail($id);
```

The application must never trust an organization ID supplied by the client without verifying membership.

---

# Database

The database is PostgreSQL.

Core identity tables include:

```text
users
organizations
organization_user
personal_access_tokens
```

Authorization tables are provided by Spatie Laravel Permission:

```text
roles
permissions
model_has_roles
model_has_permissions
role_has_permissions
```

The authorization model is organization-aware through Spatie's teams functionality.

---

# Inventory Domain

Inventory is not treated as a simple numeric field.

Stock changes should be represented through inventory movements.

Conceptually:

```text
Opening Stock
     +
Receipts
     +
Transfers In
     -
Sales
     -
Transfers Out
     ±
Adjustments
     =
Current Stock
```

This provides traceability and allows the system to answer:

* Why did stock change?
* Who changed it?
* When did it change?
* What was the previous quantity?
* What was the resulting quantity?
* Which transaction caused the change?

Inventory-changing operations should use database transactions where multiple records must remain consistent.

---

# Transactions and Concurrency

Inventory operations are consistency-sensitive.

Operations such as:

* Stock deduction
* Stock receiving
* Inventory adjustment
* Transfer receiving

must account for concurrent requests.

The implementation will use appropriate database transactions and row-level locking where required.

Example conceptual flow:

```text
BEGIN TRANSACTION

Lock inventory record

Validate available quantity

Create inventory movement

Update stock

Commit
```

This prevents two simultaneous requests from incorrectly consuming the same stock.

---

# API Design

The API is versioned.

Current version:

```text
/api/v1
```

Example:

```http
GET /api/v1/products
POST /api/v1/products
GET /api/v1/products/{product}
PATCH /api/v1/products/{product}
```

Requests use JSON.

Example:

```http
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}
X-Organization-ID: {organization_id}
```

---

# API Response Philosophy

Successful responses follow a predictable structure.

Example:

```json
{
    "message": "Product created successfully.",
    "data": {
        "id": 1,
        "name": "MacBook Pro",
        "sku": "MBP-001"
    }
}
```

Validation errors return appropriate HTTP status codes and structured error information.

The API should not expose internal exceptions, SQL errors or implementation details to clients.

---

# Security

Security is treated as a system-wide concern.

## Authentication

* Sanctum bearer tokens
* Secure password hashing
* Token revocation
* Email verification

## Authorization

* Roles
* Permissions
* Policies
* Organization-aware authorization

## Tenant Isolation

* Server-side tenant enforcement
* Organization membership validation
* Tenant-scoped queries

## Validation

All external input is validated.

The application does not trust:

```text
IDs
prices
quantities
organization IDs
role IDs
status transitions
```

---

# Rate Limiting

Rate limiting is applied to sensitive endpoints.

Examples:

```text
Login
Forgot Password
Password Reset
Email Verification
Public endpoints
Expensive reports
Sensitive write operations
```

---

# Secrets

Secrets are provided through environment variables or secret management systems.

The repository must never contain:

```text
database passwords
API keys
SMTP credentials
production tokens
application secrets
```

---

# HTTPS

Production API traffic must use HTTPS.

---

# Testing Strategy

Testing is divided into unit and feature tests.

## Unit Tests

Unit tests focus on isolated application logic.

Examples:

```text
AuthenticationServiceTest
RegisterUserTest
InventoryCalculationTest
StockAllocationTest
```

---

## Feature Tests

Feature tests validate complete application behaviour.

Examples:

```text
LoginTest
RegistrationTest
EmailVerificationTest
PasswordResetTest
PermissionTest
OrganizationIsolationTest
ProductTest
InventoryTest
TransferTest
```

---

# Security Testing

Security tests are treated as first-class tests.

Important invariants include:

```text
Unauthenticated users cannot access protected resources.

Users cannot access organizations they do not belong to.

Users cannot access another organization's products.

Users cannot perform operations for which they lack permission.

Changing organization context changes the applicable role/permissions.

Unverified users cannot access protected business operations.

Password reset invalidates existing API tokens.
```

---

# Project Structure

```text
InventMap/
│
├── app/
│   ├── Actions/
│   ├── DTOs/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   ├── Policies/
│   ├── Services/
│   └── Support/
│
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│
├── docs/
│   ├── requirements.md
│   ├── architecture.md
│   ├── database-design.md
│   ├── api-design.md
│   ├── business-rules.md
│   ├── security.md
│   └── decisions/
│
├── routes/
│   ├── api.php
│   └── web.php
│
├── tests/
│   ├── Feature/
│   └── Unit/
│
├── README.md
└── ...
```

---

# Development Setup

Clone the repository:

```bash
git clone <repository-url>
cd InventMap
```

Install PHP dependencies:

```bash
composer install
```

Create environment configuration:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Configure the database and mail settings in `.env`.

Run migrations:

```bash
php artisan migrate
```

Seed development data:

```bash
php artisan db:seed
```

Or rebuild the database:

```bash
php artisan migrate:fresh --seed
```

Run the application:

```bash
php artisan serve
```

Run tests:

```bash
php artisan test
```

---

# Demo Credentials

Development credentials are provided for API testing.

### Admin

```text
Email: john@inventmap.test
Password: password
```

### Inventory Manager

```text
Email: sarah@inventmap.test
Password: password
```

### Warehouse Staff

```text
Email: mike@inventmap.test
Password: password
```

These credentials are strictly for local development and testing.

---

# Example Authentication Flow

## Login

```http
POST /api/v1/auth/login
Content-Type: application/json
Accept: application/json
```

```json
{
    "email": "john@inventmap.test",
    "password": "password"
}
```

The API returns a Sanctum bearer token.

---

## Access Protected Endpoint

```http
GET /api/v1/products
Authorization: Bearer {token}
X-Organization-ID: 1
Accept: application/json
```

The server then establishes:

```text
User
 ↓
Organization
 ↓
Role
 ↓
Permission
```

before executing the request.

---

# API Documentation

Detailed API documentation is maintained separately under:

```text
docs/api-design.md
```

Additional API documentation can be generated using the project's OpenAPI/Swagger documentation tooling as the API surface grows.

---

# Engineering Decisions

Important architectural decisions are documented in:

```text
docs/decisions/
```

Examples include:

```text
001-monolith-first.md
002-postgresql.md
003-inventory-ledger.md
004-rest-api.md
005-simple-abstractions.md
```

The purpose of documenting architectural decisions is to make important engineering trade-offs explicit rather than allowing architecture to emerge accidentally.

---

# Engineering Philosophy

InventMap is intentionally built with the following principles:

> **Correctness before cleverness.**

> **Security is enforced by the backend, not trusted to the frontend.**

> **Business rules belong in the application/domain layer, not controllers.**

> **Transactions protect business invariants.**

> **Tests should protect behaviour, not implementation details.**

> **Patterns should solve problems, not exist for decoration.**

> **Architecture should evolve according to actual complexity.**

---

# Roadmap

## Phase 1 — Identity & Security

* [x] Sanctum authentication
* [x] Organization membership
* [x] Role/permission foundation
* [x] Tenant context
* [x] Registration
* [ ] Email verification
* [ ] Password reset
* [ ] Rate limiting
* [ ] Security test suite

## Phase 2 — Product Management

* [ ] Products
* [ ] Categories
* [ ] SKU management
* [ ] Product archival

## Phase 3 — Inventory

* [ ] Warehouses
* [ ] Inventory records
* [ ] Stock movements
* [ ] Inventory receiving
* [ ] Inventory deductions
* [ ] Inventory adjustments

## Phase 4 — Transfers

* [ ] Transfer creation
* [ ] Approval
* [ ] Dispatch
* [ ] Receiving
* [ ] Cancellation
* [ ] Transfer history

## Phase 5 — Procurement

* [ ] Suppliers
* [ ] Purchase orders
* [ ] Purchase receiving

## Phase 6 — Sales

* [ ] Customers
* [ ] Sales orders
* [ ] Fulfillment
* [ ] Inventory deduction

## Phase 7 — Reporting

* [ ] Stock reports
* [ ] Movement reports
* [ ] Inventory valuation
* [ ] Operational reports

## Phase 8 — Production Hardening

* [ ] Audit logging
* [ ] Observability
* [ ] Performance testing
* [ ] Queue processing
* [ ] Redis caching
* [ ] CI/CD
* [ ] Docker production deployment
* [ ] OpenAPI documentation
* [ ] Monitoring and alerting

---

# Why InventMap?

InventMap is intentionally more than a CRUD application.

The project demonstrates practical backend engineering concerns including:

* Object-oriented programming
* SOLID principles
* REST API design
* Authentication
* Authorization
* Multi-tenancy
* Role-based access control
* Database design
* Transactions
* Concurrency
* Validation
* Domain modelling
* DTOs
* Application services
* Policies
* Automated testing
* Security engineering
* API versioning
* Production deployment
* Architectural decision making

The objective is to build a backend that can support real client applications while remaining understandable, testable and maintainable as the domain grows.
