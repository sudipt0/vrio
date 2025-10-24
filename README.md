# 🧩 Vrio Laravel Package

A Laravel package to seamlessly interact with the [**Vrio CRM API**](https://vrio.app), allowing you to manage **customers**, **orders**, **payment cards**, and more — directly from your Laravel application.  

Supports **Laravel 10, 11, and 12**.

---

## 🚀 Features

- ✅ Create and retrieve **customers**
- 💳 Add and list **payment cards**
- 🧾 Create and retrieve **orders**
- ⚙️ Graceful error handling with structured responses
- 🧱 Provides **Facade** and **dependency injection** support
- 🔐 Automatically injects API credentials from configuration
- 🧰 Easy configuration publishing and environment setup

---

## 📦 Installation Guide

### 1. Add the Package to Your Project

If you are developing locally and have the package inside `packages/sudipta/vrio`, add this to your root `composer.json` file:

```json
"repositories": [
    {
        "type": "path",
        "url": "./packages/sudipta/vrio"
    }
]
Then require the package:

bash
Copy code
composer require "sudipta/vrio:dev-main"
💡 If the package is published on Packagist, you can simply run:

bash
Copy code
composer require sudipta/vrio
2. Publish the Configuration File
After installation, publish the package configuration file using the Artisan command:

bash
Copy code
php artisan vendor:publish --tag=vrio-config
This will create a configuration file at:

arduino
Copy code
config/vrio.php
3. Configure Environment Variables
Add your Vrio CRM API credentials to your .env file:

env
Copy code
VRIO_BASE_URL=https://api.vrio.app
VRIO_API_KEY=your_api_key_here
VRIO_API_SECRET=your_api_secret_here
🧠 You can find your API credentials inside your Vrio CRM dashboard.

4. Verify Configuration
Check the generated config/vrio.php file to ensure proper setup:

php
Copy code
return [
    'base_url' => env('VRIO_BASE_URL', 'https://api.vrio.app'),
    'api_key' => env('VRIO_API_KEY'),
    'api_secret' => env('VRIO_API_SECRET'),
];
🧠 Usage Examples
Using the Facade
php
Copy code
use Sudipta\Vrio\Facades\Vrio;

// Create a customer
$response = Vrio::createCustomer([
    'name'  => 'John Doe',
    'email' => 'john@example.com',
]);

// Get customer details
$customer = Vrio::getCustomer($response['data']['id']);
Using Dependency Injection
php
Copy code
use Sudipta\Vrio\VrioClient;

class CustomerController extends Controller
{
    protected $vrio;

    public function __construct(VrioClient $vrio)
    {
        $this->vrio = $vrio;
    }

    public function store(Request $request)
    {
        $customer = $this->vrio->createCustomer($request->all());
        return response()->json($customer);
    }
}
Handling Orders and Cards
php
Copy code
// Create a new order
$order = Vrio::createOrder([
    'customer_id' => 'cust_123',
    'amount' => 100.00,
    'currency' => 'USD',
]);

// Add a payment card
$card = Vrio::addCard([
    'customer_id' => 'cust_123',
    'token' => 'card_token_here',
]);
⚠️ Error Handling
All API responses are returned in a structured format:

Success Example:

php
Copy code
[
    'success' => true,
    'data' => [...],
    'message' => 'Customer created successfully',
    'status_code' => 200
]
Error Example:

php
Copy code
[
    'success' => false,
    'message' => 'Invalid API credentials',
    'status_code' => 401
]
🧩 Available Artisan Commands
If your package provides custom Artisan commands, you can run them like so:

bash
Copy code
php artisan vrio:test-connection
🧱 Folder Structure
Here’s what your package directory might look like:

arduino
Copy code
packages/
└── sudipta/
    └── vrio/
        ├── src/
        │   ├── VrioServiceProvider.php
        │   ├── Facades/
        │   │   └── Vrio.php
        │   ├── VrioClient.php
        │   └── ...
        ├── config/
        │   └── vrio.php
        ├── composer.json
        └── README.md
🛠️ Development Tips
If you are actively developing the package:

Update Composer autoload mappings:

bash
Copy code
composer dump-autoload
Re-publish configuration file if you make changes:

bash
Copy code
php artisan vendor:publish --tag=vrio-config --force
Test integration locally using Laravel Tinker:

bash
Copy code
php artisan tinker
>>> Vrio::getCustomers();
🤝 Contributing
Contributions, issues, and feature requests are welcome!
Please open an issue first before submitting major changes.

To contribute:

Fork the repository

Create your feature branch: git checkout -b feature/your-feature

Commit your changes: git commit -m 'Add new feature'

Push to the branch: git push origin feature/your-feature

Open a pull request 🎉

🪪 License
This package is open-source and available under the MIT License.

Made with ❤️ for Laravel developers by Sudipta

yaml
Copy code

---

✅ **You can now save this as:**  
`packages/sudipta/vrio/README.md`  

Would you like me to include a **Testing section** (with sample PHPUnit and mock API examples) next? That would make your package documentation look even more professional for GitHub or Packagist.

