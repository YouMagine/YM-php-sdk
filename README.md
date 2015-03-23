*PLEASE NOTE: This v1 SDK is work in progress for the new YouMagine API, referred
to as v1. This new API version is not final, so the use of it is not recommended
until the official release. Please watch this repository if you want to stay
informed. Please refer to
[The master branch](https://github.com/YouMagine/sdk-php) for the SDK for the
current stable API*

# YouMagine PHP SDK for API v1

A PHP library that you can use to interface with YouMagine.

## Quickstart with example application

If you are running Linux or MacOSX, you can quickstart with the console commands
below (PHP 5.4 or higher is required to run PHP's built-in webserver)


```
git clone -b api-v1 https://github.com/YouMagine/sdk-php.git
cd sdk-php
php -S localhost:8000 -c php.ini
```

Then visit [http://localhost:8000/youmagine-php-api-example.php](http://localhost:8000/youmagine-php-api-example.php) in your browser

If your PHP version is < 5.4, we recommend either to update your PHP version
(find some assistance [here](http://php.net/manual/en/install.php)) or install
another type of webserver, for example
[Apache](https://www.apachefriends.org/index.html).

## Use the SDK in your application

NOTE: when your are developing you own application that
integrates with YouMagine, you first need to register your application with
YouMagine. Please follow the guidelines at https://api.youmagine.com/api.

When using the PHP SDK in your own web application, you only need the
youmagine.php file.

```
wget https://raw.githubusercontent.com/YouMagine/sdk-php/api-v1/youmagine.php
```

Then include the file...

``` php
include_once youmagine.php
```

... and instantiate the YouMagine class:

```php
$youMagine = new YouMagine('your youmagine application name here');
```

The YouMagine SDK is using HTTPS by default (it is recommended because you do
not want to send and receive authentication tokens over plain HTTP). If,
however, it is not possible for your application to use HTTPS, you can switch to
HTTP:

```php
$youMagine = new YouMagine('your youmagine application name here', array(
    'https' => false
));
```

## Questions?

If you have any questions regarding the PHP SDK or the API, feel free to contact
us: webapps@youmagine.com
