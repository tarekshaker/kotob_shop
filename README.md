# Kotob Shop

> An e-commerce mobile app based on Symfony 4.2.1 which provide RESTful APIs endpoints that handle
all the shopping process including list products with prices, adding products to a shopping basket
and submitting order.
An admin dashboard is provided to help the admin in manage products, users and orders
using very friendly CRUD tools.

## Quick Start

``` bash
# Install dependencies
composer install

# Edit the env file and add DB params

# Create Database schema
php bin/console doctrine:migrations:diff
# Run migrations
php bin/console doctrine:migrations:migrate

# Promote admin user for admin panel authorization
php bin/console fos:user:promote {user} ROLE_ADMIN

```

## App Info

1. Admin
    1. Dashboard with some statistics for users, products and orders
    2. Users dashboard so admin can add, edit and list users
    3. Products dashboard so admin can add, edit and list products
    4. Orders dashboard so admin can review orders, list orders list and confirm orders

2. Api
    1. Login Endpoint: used by mobile app to authenticate user
    2. Register Endpoint: used by mobile app to add new user
    3. Product Endpoints: used by mobile app to list products and add product to cart
    4. Order Endpoints: used by mobile app to submit the order by user

```

## Most important bundles used:
    1. FOSUser bundle: used to authenticate the users
    2. FOSREST bundle: used to handle RESTful APIs Endpoints
    3. JWT Authentication: used to handle token generation
    4. KNP Pagination: used to handle pagination in APIs


```
### Author

Tarek Shaker

### Version

1.0.0