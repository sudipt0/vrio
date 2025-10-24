# 🧩 Vrio Laravel Package

A Laravel package to seamlessly interact with the [**Vrio CRM API**](https://vrio.app), allowing you to manage **customers**, **orders**, **payment cards**, and more — directly from your Laravel application.  

Supports **Laravel 10, 11, and 12**.

---

## 🚀 Features

- ✅ Create and retrieve **customers**
- 💳 Add and list **payment cards**
- 🧾 Create and retrieve **orders**
- 📦 Manage **products** and **inventory**
- 🔄 Handle **subscriptions** and **recurring billing**
- ⚙️ Graceful error handling with structured responses
- 🧱 Provides **Facade** and **dependency injection** support
- 🔐 Automatically injects API credentials from configuration
- 🧰 Easy configuration publishing and environment setup
- 🧪 Comprehensive test suite with API mocking

---

## 📦 Installation Guide

### 1. Add the Package to Your Project

If you are developing locally and have the package inside `packages/sudipta/vrio`, add this to your root `composer.json` file:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/sudipta/vrio"
        }
    ]
}
```

Then require the package:

```bash
composer require "sudipta/vrio:dev-main"
```

💡 **If the package is published on Packagist**, you can simply run:

```bash
composer require sudipta/vrio
```

### 2. Publish the Configuration File

After installation, publish the package configuration file using the Artisan command:

```bash
php artisan vendor:publish --tag=vrio-config
```

This will create a configuration file at:

```
config/vrio.php
```

### 3. Configure Environment Variables

Add your Vrio CRM API credentials to your `.env` file:

```env
VRIO_BASE_URL=https://api.vrio.app
VRIO_API_KEY=your_api_key_here
VRIO_API_SECRET=your_api_secret_here
```

🧠 You can find your API credentials inside your Vrio CRM dashboard.

### 4. Verify Configuration

Check the generated `config/vrio.php` file to ensure proper setup:

```php
return [
    'base_url' => env('VRIO_BASE_URL', 'https://api.vrio.app'),
    'api_key' => env('VRIO_API_KEY'),
    'api_secret' => env('VRIO_API_SECRET'),
    
    // Optional: Default timeout for API requests (seconds)
    'timeout' => env('VRIO_TIMEOUT', 30),
    
    // Optional: Enable/disable debug mode
    'debug' => env('VRIO_DEBUG', false),
];
```

---

## 🧠 Usage Examples

### Using the Facade

```php
<?php

namespace App\Http\Controllers;

use Sudipta\Vrio\Facades\Vrio;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function createCustomer(Request $request)
    {
        $response = Vrio::createCustomer([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'address' => [
                'line1' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10001',
                'country' => 'US'
            ]
        ]);

        if ($response['success']) {
            return response()->json($response['data'], 201);
        }

        return response()->json([
            'error' => $response['message']
        ], $response['status_code']);
    }

    public function getCustomer($customerId)
    {
        $customer = Vrio::getCustomer($customerId);
        return response()->json($customer);
    }
}
```

### Using Dependency Injection

```php
<?php

namespace App\Http\Controllers;

use Sudipta\Vrio\VrioClient;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $vrio;

    public function __construct(VrioClient $vrio)
    {
        $this->vrio = $vrio;
    }

    public function createOrder(Request $request)
    {
        $order = $this->vrio->createOrder([
            'customer_id' => $request->customer_id,
            'amount' => $request->amount,
            'currency' => $request->currency ?? 'USD',
            'items' => $request->items,
            'metadata' => $request->metadata ?? []
        ]);

        return response()->json($order);
    }

    public function getOrder($orderId)
    {
        $order = $this->vrio->getOrder($orderId);
        return response()->json($order);
    }
}
```

### Handling Payment Cards

```php
<?php

namespace App\Http\Controllers;

use Sudipta\Vrio\Facades\Vrio;

class PaymentController extends Controller
{
    public function addCard(Request $request)
    {
        $response = Vrio::addCard([
            'customer_id' => $request->customer_id,
            'token' => $request->token, // Payment token from your frontend
            'is_default' => $request->is_default ?? false
        ]);

        if ($response['success']) {
            return response()->json([
                'message' => 'Card added successfully',
                'card' => $response['data']
            ]);
        }

        return response()->json([
            'error' => $response['message']
        ], 400);
    }

    public function listCards($customerId)
    {
        $response = Vrio::listCards($customerId);
        return response()->json($response);
    }
}
```

### Managing Products

```php
<?php

namespace App\Http\Controllers;

use Sudipta\Vrio\Facades\Vrio;

class ProductController extends Controller
{
    public function createProduct(Request $request)
    {
        $response = Vrio::createProduct([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'currency' => $request->currency ?? 'USD',
            'sku' => $request->sku,
            'inventory' => $request->inventory ?? 0,
            'metadata' => $request->metadata ?? []
        ]);

        return response()->json($response);
    }

    public function getProducts()
    {
        $response = Vrio::getProducts([
            'page' => request('page', 1),
            'limit' => request('limit', 20)
        ]);

        return response()->json($response);
    }
}
```

---

## ⚠️ Error Handling

All API responses are returned in a structured format:

### Success Example:

```php
[
    'success' => true,
    'data' => [
        'id' => 'cust_123456',
        'name' => 'John Doe',
        'email' => 'john@example.com',
        // ... other customer data
    ],
    'message' => 'Customer created successfully',
    'status_code' => 200
]
```

### Error Example:

```php
[
    'success' => false,
    'message' => 'Invalid API credentials',
    'status_code' => 401,
    'errors' => [
        'auth' => 'API key is invalid'
    ]
]
```

### Handling Errors in Your Application

```php
<?php

try {
    $response = Vrio::createCustomer($data);
    
    if (!$response['success']) {
        // Handle API error
        Log::error('Vrio API Error: ' . $response['message']);
        return back()->withError($response['message']);
    }
    
    // Process successful response
    $customer = $response['data'];
    
} catch (\Exception $e) {
    // Handle network or unexpected errors
    Log::error('Vrio Service Exception: ' . $e->getMessage());
    return back()->withError('Service temporarily unavailable');
}
```

---

## 🧩 Available Methods

### Customer Management

```php
Vrio::createCustomer(array $data)
Vrio::getCustomer(string $customerId)
Vrio::updateCustomer(string $customerId, array $data)
Vrio::listCustomers(array $params = [])
Vrio::searchCustomers(array $criteria)
```

### Order Management

```php
Vrio::createOrder(array $data)
Vrio::getOrder(string $orderId)
Vrio::updateOrder(string $orderId, array $data)
Vrio::listOrders(array $params = [])
Vrio::cancelOrder(string $orderId)
```

### Payment Card Management

```php
Vrio::addCard(array $data)
Vrio::getCard(string $customerId, string $cardId)
Vrio::listCards(string $customerId)
Vrio::updateCard(string $customerId, string $cardId, array $data)
Vrio::deleteCard(string $customerId, string $cardId)
```

### Product Management

```php
Vrio::createProduct(array $data)
Vrio::getProduct(string $productId)
Vrio::updateProduct(string $productId, array $data)
Vrio::listProducts(array $params = [])
Vrio::deleteProduct(string $productId)
```

### Subscription Management

```php
Vrio::createSubscription(array $data)
Vrio::getSubscription(string $subscriptionId)
Vrio::updateSubscription(string $subscriptionId, array $data)
Vrio::cancelSubscription(string $subscriptionId)
Vrio::listSubscriptions(array $params = [])
```

---

## 🧪 Testing

### 1. Install Testing Dependencies

Make sure you have PHPUnit installed in your Laravel application:

```bash
composer require --dev phpunit/phpunit
```

### 2. Create Test Configuration

Create a test case for the Vrio package:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Sudipta\Vrio\Facades\Vrio;
use Illuminate\Support\Facades\Http;

class VrioServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock API responses for testing
        Http::fake([
            'api.vrio.app/*' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 'cust_test123',
                    'name' => 'Test Customer',
                    'email' => 'test@example.com'
                ],
                'message' => 'Customer created successfully',
                'status_code' => 200
            ], 200)
        ]);
    }

    public function test_customer_creation()
    {
        $response = Vrio::createCustomer([
            'name' => 'Test Customer',
            'email' => 'test@example.com'
        ]);

        $this->assertTrue($response['success']);
        $this->assertEquals('cust_test123', $response['data']['id']);
        $this->assertEquals('Customer created successfully', $response['message']);
    }

    public function test_invalid_credentials_handling()
    {
        Http::fake([
            'api.vrio.app/*' => Http::response([
                'success' => false,
                'message' => 'Invalid API credentials',
                'status_code' => 401
            ], 401)
        ]);

        $response = Vrio::getCustomer('invalid_id');

        $this->assertFalse($response['success']);
        $this->assertEquals(401, $response['status_code']);
    }
}
```

### 3. Run Tests

```bash
./vendor/bin/phpunit tests/Feature/VrioServiceTest.php
```

### 4. Environment Testing

For local development, you can use the Vrio sandbox environment:

```env
VRIO_BASE_URL=https://sandbox-api.vrio.app
VRIO_API_KEY=your_sandbox_api_key
VRIO_API_SECRET=your_sandbox_api_secret
```

---

## 🛠️ Development Tips

### Auto-loading During Development

If you're actively developing the package:

```bash
# Update Composer autoload mappings
composer dump-autoload

# Re-publish configuration file if you make changes
php artisan vendor:publish --tag=vrio-config --force
```

### Testing with Laravel Tinker

```bash
php artisan tinker

>>> Vrio::getCustomers();
>>> Vrio::createCustomer(['name' => 'Test', 'email' => 'test@example.com']);
```

### Debug Mode

Enable debug mode for detailed logging:

```env
VRIO_DEBUG=true
```

Then check your Laravel logs:

```php
Log::channel('stack')->debug('Vrio API Request:', $requestData);
Log::channel('stack')->debug('Vrio API Response:', $responseData);
```

---

## 🧱 Folder Structure

Here's the complete package structure:

```
packages/
└── sudipta/
    └── vrio/
        ├── src/
        │   ├── VrioServiceProvider.php
        │   ├── Facades/
        │   │   └── Vrio.php
        │   ├── VrioClient.php
        │   ├── Contracts/
        │   │   └── VrioInterface.php
        │   ├── Exceptions/
        │   │   ├── VrioException.php
        │   │   ├── AuthenticationException.php
        │   │   └── ValidationException.php
        │   └── Http/
        │       └── Responses/
        │           ├── ApiResponse.php
        │           └── ErrorResponse.php
        ├── config/
        │   └── vrio.php
        ├── tests/
        │   ├── Feature/
        │   │   └── VrioServiceTest.php
        │   └── Unit/
        │       └── VrioClientTest.php
        ├── composer.json
        ├── LICENSE.md
        └── README.md
```

---

## 🔧 Advanced Configuration

### Custom HTTP Client

You can extend the package with custom HTTP client configuration:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Sudipta\Vrio\VrioClient;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(VrioClient::class, function ($app) {
            return new VrioClient([
                'base_url' => config('vrio.base_url'),
                'api_key' => config('vrio.api_key'),
                'api_secret' => config('vrio.api_secret'),
                'timeout' => config('vrio.timeout', 30),
                'retry' => [
                    'times' => 3,
                    'sleep' => 100,
                ]
            ]);
        });
    }
}
```

### Event Listeners

Listen to Vrio API events:

```php
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Sudipta\Vrio\Events\VrioApiCalled;
use App\Listeners\LogVrioApiCall;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        VrioApiCalled::class => [
            LogVrioApiCall::class,
        ],
    ];
}
```

---

## 🤝 Contributing

We welcome contributions to make this package better! Here's how you can help:

### 1. Fork the Repository

### 2. Create Your Feature Branch

```bash
git checkout -b feature/amazing-feature
```

### 3. Commit Your Changes

```bash
git commit -m 'Add some amazing feature'
```

### 4. Push to the Branch

```bash
git push origin feature/amazing-feature
```

### 5. Open a Pull Request

### Development Setup

```bash
# Clone your fork
git clone https://github.com/your-username/vrio-laravel.git

# Install dependencies
composer install

# Run tests
./vendor/bin/phpunit

# Check code style
./vendor/bin/phpcs
```

### Coding Standards

- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation
- Add type hints where possible

---

## 🐛 Troubleshooting

### Common Issues

1. **API Credentials Not Working**
   - Verify your API key and secret in the Vrio dashboard
   - Check that environment variables are loaded properly
   - Ensure there are no trailing spaces in your `.env` file

2. **SSL Certificate Issues**
   ```php
   // In config/vrio.php for development
   'verify_ssl' => env('VRIO_VERIFY_SSL', true),
   ```

3. **Timeout Errors**
   ```env
   VRIO_TIMEOUT=60
   ```

### Getting Help

- Check the [Vrio API Documentation](https://docs.vrio.app)
- Create an [issue on GitHub](https://github.com/sudipta/vrio-laravel/issues)
- Contact support at support@vrio.app

---

## 🔄 Changelog

### v1.0.0
- Initial release
- Customer management
- Order processing  
- Payment card handling
- Product catalog management

---

## 🪪 License

This package is open-source and available under the MIT License. See [LICENSE.md](LICENSE.md) for more information.

---

## 📞 Support

- **Documentation:** [https://docs.vrio.app](https://docs.vrio.app)
- **Issues:** [GitHub Issues](https://github.com/sudipta/vrio-laravel/issues)
- **Email:** support@vrio.app

---

Made with ❤️ for Laravel developers by [Sudipta](https://github.com/sudipta)

---

**⭐ If this package helped you, please consider giving it a star on GitHub!**