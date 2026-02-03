# Extendable Order & Payment Management API (Laravel)

## Overview
This Laravel 10 API provides JWT-secured endpoints for order and payment management. The payment layer uses a strategy pattern so you can add new gateways with minimal changes. The API is RESTful, validates input, paginates list endpoints, and enforces business rules like "payments only for confirmed orders" and "no deleting orders with payments".

## Features
### Authentication (JWT)
- User registration and login using JWT.
- Tokens are signed with `JWT_SECRET` and include a configurable TTL.

### Order Management
- Create orders with customer details and line items.
- Update orders (including items and status).
- Delete orders **only when no payments exist**.
- List orders with optional status filtering and pagination.

### Payment Management
- Process payments via strategy-driven gateways (e.g., credit_card, paypal).
- Payments can **only** be processed for confirmed orders.
- List payments globally or by order, with pagination.

## Setup Instructions
### 1) Install Dependencies
```bash
composer install
```

### 2) Configure Environment
Copy `.env.example` to `.env`, then set:
```env
APP_KEY=base64:...
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
JWT_SECRET=your-secret
JWT_TTL=60

# Optional gateway credentials
CREDIT_CARD_API_KEY=your-key
PAYPAL_CLIENT_ID=your-client-id
PAYPAL_CLIENT_SECRET=your-client-secret
```

Create the SQLite file:
```bash
touch database/database.sqlite
```

### 3) Run Migrations
```bash
php artisan migrate
```

### 4) Start the Server
```bash
php artisan serve
```

## API Summary
### Auth
- `POST /api/auth/register`
- `POST /api/auth/login`

### Orders (JWT required)
- `GET /api/orders?status=pending|confirmed|cancelled`
- `POST /api/orders`
- `GET /api/orders/{order}`
- `PUT /api/orders/{order}`
- `DELETE /api/orders/{order}`

### Payments (JWT required)
- `POST /api/payments`
- `GET /api/payments`
- `GET /api/orders/{order}/payments`

## Payment Gateway Extensibility
Gateways live under `app/Payments/Gateways` and implement `PaymentGatewayInterface`. To add one:
1. Create a new gateway class that implements `process(Order $order, array $payload)`.
2. Register it in `config/payment.php`.
3. Add required secrets to `.env`.

Example registration:
```php
'stripe' => [
    'class' => App\Payments\Gateways\StripeGateway::class,
    'config' => [
        'api_key' => env('STRIPE_API_KEY'),
    ],
],
```

## Postman Documentation
Import `ApisTaskCollection.postman_collection.json` to view requests and example responses for:
- Authentication
- Orders
- Payments

## Notes
- Payment processing is simulated; set `simulate_status` to `pending`, `successful`, or `failed` to control outcomes during testing.
- Order totals are recalculated from line items on create and update.
