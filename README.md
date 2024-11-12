# Athena Framework

Athena is a lightweight, simple, and easy-to-use PHP MVC framework designed for building web applications. It provides a clean structure and basic features while maintaining flexibility and ease of use.

## Features

- Simple MVC Architecture
- Clean Route System
- View Layout System
- Basic Controller Structure
- Simple Response Handling
- Environment Configuration
- Helper Functions
- PSR-4 Autoloading

## Requirements

- PHP >= 7.4
- Composer
- Apache/Nginx Web Server

## Installation

1. Clone the repository:
```bash
git clone https://github.com/mefistofeles00/athena.git
```

2. Install dependencies:
```bash
cd athena
composer install
```

3. Create `.env` file:
```bash
cp .env.example .env
```

4. Configure your web server or use PHP's built-in server:
```bash
php -S localhost:8000 -t public
```

## Project Structure

```plaintext
athena/
├── app/                    # Application core code
│   ├── Controllers/       # Controller classes
│   ├── Models/           # Model classes
│   ├── Middleware/       # Middleware classes
│   ├── Helpers/         # Helper classes
│   └── Router.php       # Router class
├── bootstrap/            # Application bootstrap files
├── config/              # Configuration files
├── public/              # Public directory (web root)
├── routes/              # Route definitions
├── storage/            # Storage directory
├── views/              # View files
└── vendor/            # Composer dependencies
```

## Basic Usage

### Routing

Define routes in `routes/web.php`:
```php
$router->get('/', 'HomeController@index');
$router->get('/about', 'HomeController@about');
$router->post('/users', 'UserController@store');
```

### Controllers

Create a controller in `app/Controllers`:
```php
<?php

namespace App\Controllers;

use App\Helpers\View;

class HomeController extends Controller
{
    public function index()
    {
        return View::make('home.index', [
            'title' => 'Home Page'
        ]);
    }
}
```

### Views

Create views in the `views` directory:
```php
<?php 
use App\Helpers\View;
View::setLayout('app');
?>

<div class="container">
    <h1><?php echo $title; ?></h1>
    <p>Welcome to Athena Framework!</p>
</div>
```

### Layouts

Create layouts in `views/layouts`:
```php
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title; ?></title>
</head>
<body>
    <?php require VIEW_PATH . '/partials/header.php'; ?>
    <main>
        <?php echo $content; ?>
    </main>
    <?php require VIEW_PATH . '/partials/footer.php'; ?>
</body>
</html>
```

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Author

Inanc Eroglu
- Email: 0mefistofeles0@gmail.com
- GitHub: github.com/mefistofeles00

## Acknowledgments

- Inspired by modern PHP frameworks
- Built with simplicity in mind
- Perfect for learning MVC architecture

## Security

If you discover any security related issues, please email 0mefistofeles0@gmail.com instead of using the issue tracker.

## Support

For support, please create an issue in the GitHub repository or contact the author directly.