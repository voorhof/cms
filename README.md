# CMS Bootstrap for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/voorhof/cms.svg?style=flat-square)](https://packagist.org/packages/voorhof/cms)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/voorhof/cms/fix-php-code-style-issues.yml?branch=master&label=code%20style&style=flat-square)](https://github.com/voorhof/cms/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/voorhof/cms.svg?style=flat-square)](https://packagist.org/packages/voorhof/cms)

This is a basic CMS template for Laravel using [Bootstrap 5](https://getbootstrap.com/) as the frontend toolkit.  
It includes routes, controllers, views, ... for User and Role management,  
together with an example Post model and all CRUD functionality.

## Installation

You can install the package via composer.

```bash
composer require voorhof/cms
```

Run this command to install the basic CMS structure,  
it will copy all necessary resource files to your app and update existing ones (a backup option is available).   
After installation, the database will automatically be refreshed and seeded with factory data.

```bash
php artisan cms:install
```

After installation some basic data will have been seeded to the database,  
please customize the CmsSeeder file to your development needs.

Under the hood [Laravel Permission](https://spatie.be/docs/laravel-permission/v6) from Spatie is used for assigning roles.  
Everything needed for this project is already in place after installation,  
but if you feel the need to re-publish the config file and database migration in the future, you can with the command below.  
Keep in mind that the default Spatie Role model has been extended, so you will probably have to update that again in the permissions.php config file.

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Although it is not required for installation, a basic user auth scaffolding for registering, login,... should be present in the app.  
If you don't have an auth starter kit already installed, [Voorhof Bries](https://github.com/voorhof/bries) is also available in this CMS package.   
When you choose to use Bries, be sure to execute this artisan command **BEFORE cms:install**

```bash
php artisan bries:install
```

## Testing

After installation there will be new test files present in the app,  
be sure to run composer test to make sure everything is working as expected.

```bash
php artisan test
```


## Credits

- [David Carton](https://github.com/voorhof)
- [All Contributors](https://github.com/voorhof/cms/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
