# Vrio Laravel Package

A Laravel package to interact with the [Vrio CRM API](https://vrio.app) for managing customers, orders, cards, and more. Designed for Laravel 10, 11, and 12.

---

## Features

- Create and retrieve customers
- Add and list payment cards
- Create and retrieve orders
- Handles API errors gracefully with structured responses
- Supports Facade and dependency injection
- Auto-injects API credentials from configuration

---

## Installation

### 1. Add the package to your project

If you are using it as a local package:

```bash
# Add repository to your root composer.json
"repositories": [
    {
        "type": "path",
        "url": "./packages/sudipta/vrio"
    }
]

# Require package
composer require "sudipta/vrio:dev-main"
