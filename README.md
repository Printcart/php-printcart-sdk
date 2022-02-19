# PHP Printcart SDK

PHPPrintcart is a simple SDK implementation of Printcart API. It helps accessing the API in an object oriented way.

## Installation

Install with Composer

```shell
composer require php-printcart-sdk
```

### Requirements

PHPPrintcart uses Guzzle extension for handling http calls. So you need to have the guzzlehttp extension installed and enabled with PHP.

> However if you prefer to use any other available package library for handling HTTP calls, you can easily do so by modifying 1 line in each of the `get()`, `post()`, `put()`, `delete()` methods in `PHPPrintcart\GuzzleRequest` class.

##### How to get the permanent access token for a shop?

There is a AuthHelper class to help you getting the permanent access token from the shop using oAuth.

```php
$config = array(
    'Username' => 'pcsia_b9a7d6fa332b74bdd073cabbac0e6ee539ed3b361aca0c2f7b9bbfe683430cce',
    'Password' => 'pcsup_02be5f225e8ddb1ff1569cf4bac0c9346c60928394902ab2f193fcd5bfc2657d'
);

PHPPrintcart\PrintcartSDK::config($config);
```

#### Get the PrintcartSDK Object

```php
$printcart = new PHPPrintcart\PrintcartSDK;
```

You can provide the configuration as a parameter while instantiating the object (if you didn't configure already by calling `config()` method)

```php
$printcart = new PHPPrintcart\PrintcartSDK($config);
```

##### Now you can do `get()`, `post()`, `put()`, `put_batch()`, `delete()`, `delete_batch()` calling the resources in the object oriented way. All resources are named as same as it is named in printcart API reference. (See the resource map below.)

> All the requests returns an array (which can be a single resource array or an array of multiple resources) if succeeded. When no result is expected (for example a DELETE request), an empty array will be returned.

- Get all product list (GET request)

```php
$products = $printcart->Product->get();
```

- Get count of total products (GET request)

```php
$products = $printcart->Product->count();
```

- Get any specific product with ID (GET request)

```php
$productID = '1b665d2f-5a29-3e03-8698-01e4dc603fa9';
$product = $printcart->Product($productID)->get();
```

- Create a new product (POST Request)

```php
$product = array(
    "name" => "T-shirt",
    "dynamic_side" => 1,
    "viewport_width" => 50.5,
    "viewport_height" => 50.5,
    "scale" => 50.5,
    "dpi" => 100,
    "dimension_unit" => "inch",
    "status" => "publish",
    "enable_design" => 1,
    "max_file_upload" => 50,
    "min_jpg_dpi" => 10,
    "allowed_file_types" => [
        "jpg",
        "pdf",
        "png"
    ]
);

$printcart->Product()->post($product);
```

> Note that you don't need to wrap the data array with the resource key (`order` in this case), which is the expected syntax from Printcart API. This is automatically handled by this SDK.

- Update a product (PUT Request)

```php
$updateInfo = array(
    "name" => "T-shirt update",
);

$printcart->Product($productID)->put($updateInfo);
```

- Update multiple products (PUT Request)

```php
$updateInfo = array(
    'products' => [
        [
            "id" => "0dcccd18-18e1-4fbf-b26b-234944746ee9",
            "name" => 'T-shirt update'
        ],
        [
            "id" => "41fec099-789e-444e-81d5-a29078f175b6",
            "name" => "Bag update"
        ],
    ]
);

$printcart->Product()->put_batch($updateInfo);
```

- Remove a product (DELETE request)

```php
$printcart->Product($productID)->delete();
```

- Remove multiple products (DELETE request)

```php
$array = array(
    'products' => [
        [
            "id" => "0dcccd18-18e1-4fbf-b26b-234944746ee9",
        ],
        [
            "id" => "41fec099-789e-444e-81d5-a29078f175b6",
        ],
    ]
);

$printcart->Product()->delete_batch($array);
```

- Remove a Webhook (DELETE request)

```php
$webHookID = 12345678;

$printcart->Webhook($webHookID)->delete();
```

### The child resources can be used in a nested way.

> You must provide the ID of the parent resource when trying to get any child resource

- For example, get the designs of a product (GET request)

```php
$productID = '1b665d2f-5a29-3e03-8698-01e4dc603fa9';
$productDesigns = $printcart->Product($productID)->Design->get();
```

## Reference

- [Printcart API Reference](https://docs.printcart.com/rest-api-reference/)

## Paid Support

You can hire the author of this SDK for setting up your project with PHPPrintcart SDK.

[Hire at my website](https://printcart.com/)
