*PLEASE NOTE: This v1 SDK is work in progress for the new YouMagine API, referred
to as v1. This new API version is not final, so the use of it is not recommended
until the official release. Please watch this repository if you want to stay
informed. Please refer to
[The master branch](https://github.com/YouMagine/sdk-php) for the SDK for the
current stable API*

# YouMagine PHP SDK for API v1

The official PHP library that you can use to interface with YouMagine. Please
note that this SDK is mainly to demonstrate how to consume the API, and only
the most used operations are implemented. However, it is easy to add the methods
you need with only basic PHP knowledge. When you add functionary, please issue
a pull request so we can add your contribution.

## Quickstart with example application

If you are running Linux or MacOSX, you can quickstart with the console commands
below (PHP 5.4 or higher is required to run PHP's built-in webserver)


```
git clone -b api-v1 https://github.com/YouMagine/sdk-php.git
cd sdk-php
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

## Use the SDK in your application

NOTE: when your are developing you own application that
integrates with YouMagine, you first need to register your application with
YouMagine. Please follow the guidelines at https://api.youmagine.com/api. You
will receive a unique API secret so that the API can verify your app's identity.
This should prevent other apps form stealing the authorization your app received
from the visitor.

NOTE: when using a local server to test your PHP application, the visitor's IP
address on your PHP server will be different from the IP address on YouMagine,
and your app will not be able to authorize. For test purposes you could
temporarily fake the IP by changing it before including the SDK:

```php
$_SERVER['REMOTE_ADDR'] = '<your public IP>';
```

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
$youMagine = new YouMagine('your youmagine application name here', array(
    'secret' => 'your app secret here'
));
```

The YouMagine SDK is using HTTPS by default (it is recommended because you do
not want to send and receive authentication tokens over plain HTTP). If,
however, it is not possible for your application to use HTTPS, you can switch to
HTTP:

```php
$youMagine = new YouMagine('your youmagine application name here', array(
    'secret'    => 'your app secret here',
    'https'     => false
));
```

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
