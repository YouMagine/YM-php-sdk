# YouMagine PHP SDK

The official PHP library that you can use to interface with YouMagine.

## Quickstart with example application

If you are running Linux or MacOS X, you can quickstart with the console commands
below (PHP 5.4 or higher is required to run PHP's built-in webserver)


```
git clone https://github.com/YouMagine/YM-php-sdk.git
cd YM-php-sdk
php -S localhost:8000 -c php.ini
```

Then visit [http://localhost:8000/youmagine-php-api-example.php](1) in your
browser.

If your PHP version is < 5.4, we recommend either to update your PHP version
(find some assistance [here](2)) or install another type of webserver, for
example [Apache](3).

[1]: http://localhost:8000/youmagine-php-api-example.php
[2]: http://php.net/manual/en/install.php
[3]: https://www.apachefriends.org/index.html

## Using the SDK in your application

NOTE: when your are developing you own application that
integrates with YouMagine, you first need to register your application with
YouMagine. Please follow the guidelines at https://api.youmagine.com/api.

When using the PHP SDK in your own web application, you only need the
youmagine.php file.

```
wget https://raw.githubusercontent.com/YouMagine/YM-php-sdk/master/youmagine.php
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

## Contributing

You can contribute in two ways. Let us know which functionality you're missing
or what bug you found through the issue tracker. Also, you can fork this
repository and create a pull request. Please try to make your pull requests
isolated, so no unrelated changes in a single pull request. Each set of related
changes should have its own pull request. Also, try to adhere to the coding
style that exists, with respect to whitespace and brackets. Please fork from the
latest master branch before starting to make your changes. Thanks a lot!

## Questions?

If you have any questions regarding the PHP SDK or the API, feel free to contact
us: webapps@youmagine.com
