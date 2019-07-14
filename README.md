[![Build Status](https://travis-ci.org/gashey/lumen-passport-resource.svg)](https://travis-ci.org/gashey/lumen-passport-resource)
[![Code Climate](https://codeclimate.com/github/gashey/lumen-passport-resource/badges/gpa.svg)](https://codeclimate.com/github/gashey/lumen-passport-resourece/badges)
[![Total Downloads](https://poser.pugx.org/gashey/lumen-passport-resource/d/total.svg)](https://packagist.org/packages/gashey/lumen-passport-resource)
[![Latest Stable Version](https://poser.pugx.org/gashey/lumen-passport-resource/v/stable.svg)](https://packagist.org/packages/gashey/lumen-passport-resource)
[![Latest Unstable Version](https://poser.pugx.org/gashey/lumen-passport-resource/v/unstable.svg)](https://packagist.org/packages/gashey/lumen-passport-resource)
[![License](https://poser.pugx.org/gashey/lumen-passport-resource/license.svg)](https://packagist.org/packages/gashey/lumen-passport-resource)

# Lumen-Passport as an oauth client grant Resource Server

Making Laravel Passport work with Lumen as a Client Grant Resource Server. This makes it easy to protect routes and check scopes via Passport middleware. This package requires "dusterio/lumen-passport" created by Denis Mysenko.

A simple service provider that makes Laravel Passport work with Lumen as a resource server

## Dependencies

* PHP >= 7.1.3
* Lumen >= 5.3

## Installation via Composer

First install Lumen if you don't have it yet:
```bash
$ composer create-project --prefer-dist laravel/lumen lumen-app
```

Then install Lumen Passport (it will fetch Laravel Passport along):

```bash
$ cd lumen-app
$ composer require gashey/lumen-passport-resource
```

Or if you prefer, edit `composer.json` manually:

```json
{
    "require": {
        "gashey/lumen-passport-resource": "^0.1.3"
    }
}
```

### Modify the bootstrap flow (```bootstrap/app.php``` file)

We need to enable both Laravel Passport provider and Lumen-specific providers:

```php
// Enable Facades
$app->withFacades();

// Enable Eloquent
$app->withEloquent();

// Enable auth middleware (shipped with Lumen)
$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
]);

// Finally register two service providers - original one and Lumen adapter
$app->register(Laravel\Passport\PassportServiceProvider::class);
$app->register(Dusterio\LumenPassport\PassportServiceProvider::class);
$app->register(Gashey\LumenPassport\PassportServiceProvider::class);
```

### Run Migrations for Gashey Passport

```bash
# Create new tables for Gashey Passport 
php artisan migrate
```

### Resource Server Public Key

This package requires your public key from your Passport Authorization Server.

Copy the "oauth-public.key" file from the 'storage folder' of your Authorization Server project into the 'storage folder of your Resource Server project.

## Configuration

Edit config/auth.php to suit your needs. A simple example:

```php
return [
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],

    'guards' => [
        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => \App\User::class
        ]
    ]
];
```

Load the config in `bootstrap/app.php` since Lumen doesn't load config files automatically:

```php
$app->configure('auth');
```

## User model

Make sure your user model uses Passport's ```HasApiTokens``` trait, eg.:

```php
class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use HasApiTokens, Authenticatable, Authorizable;

    /* rest of the model */
}
```

## Running with Apache httpd

If you are using Apache web server, it may strip Authorization headers and thus break Passport.

Add the following either to your config directly or to ```.htaccess```:

```
RewriteEngine On
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]
```

## License

The MIT License (MIT)
Copyright (c) 2019 George Kofi Hagan

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
