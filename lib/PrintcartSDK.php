<?php

/**
 * Created by PhpStorm.
 * @author Printcart <printcart@gmail.com>
 * Created at 01/18/21 10:00 AM GTM+07:00
 *
 * @see https://docs.printcart.com/rest-api-reference/ Printcart API Reference
 */

namespace PHPPrintcart;


/*
|--------------------------------------------------------------------------
| Printcart API SDK Client Class
|--------------------------------------------------------------------------
|
| This class initializes the resource objects
|
| Usage:
| //For private app
| $config = array(
|    'Username' => 'printcart@gmail.com',
|    'Password' => 'printcart',
| );

| //Create the printcart client object
| $printcart = new PrintcartSDK($config);
|
| //Get list of all products
| $products = $printcart->Product->get();
|
| //Get a specific product by product ID
| $products = $printcart->Product($productID)->get();
|
| //Update a product
| $updateInfo = array('title' => 'New Product Title');
| $products = $printcart->Product($productID)->put($updateInfo);
|
| //Delete a product
| $products = $printcart->Product($productID)->delete();
|
| //Create a new product
| $productInfo = array(
|    "name" => "T-Shirt",
|    "enable_design" => true,
|    "product_image_id": "e8304b84-a015-3bd4-a7d3-7a978f465df2",
| );
| $products = $printcart->Product->post($productInfo);
|
*/

use PHPPrintcart\Exception\SdkException;

/**
 * @property-read Product $Product
 * @property-read Side $Side
 * @property-read Image $Image
 * @property-read Font $Font
 * @property-read Design $Design
 * @property-read Template $Template
 * @property-read Storage $Storage
 * @property-read ClipartStorage $ClipartStorage
 * @property-read Project $Project
 * @property-read Account $Account
 * @property-read Webhook $Webhook
 * @property-read Store $Store
 * @property-read Integration $Integration
 *
 * @method Product Product(string $id = null)
 * @method Side Side(string $id = null)
 * @method Image Image(string $id = null)
 * @method Font Font(string $id = null)
 * @method Design Design(string $id = null)
 * @method Template Template(string $id = null)
 * @method Storage Storage(string $id = null)
 * @method ClipartStorage ClipartStorage(string $id = null)
 * @method Project Project(string $id = null)
 * @method Account Account(string $id = null)
 * @method Webhook Webhook(integer $id = null)
 * @method Integration Integration(string $source = null)
 */
class PrintcartSDK
{
    /**
     * List of available resources which can be called from this client
     *
     * @var string[]
     */
    protected $resources = array(
        'Product',
        'Side',
        'Image',
        'Font',
        'Design',
        'Template',
        'Storage',
        'ClipartStorage',
        'Clipart',
        'Project',
        'Account',
        'Webhook',
        'Store',
        'Integration',
    );

    /**
     * @var string Default Printcart API version
     */
    public static $defaultApiVersion = 'v1';

    /**
     * @var string Default Printcart API version
     */
    // public static $apiUrl = 'http://localhost:8001';
    public static $apiUrl = 'https://api.printcart.com';

    /**
     * Shop / API configurations
     *
     * @var array
     */
    public static $config = array();

    /*
     * PrintcartSDK constructor
     *
     * @param array $config
     *
     * @return void
     */
    public function __construct($config = array())
    {
        if (!empty($config)) {
            PrintcartSDK::config($config);
        }
    }

    /**
     * Return PrintcartResource instance for a resource.
     * @example $printcart->Product->get(); //Returns all available Products
     * Called like an object properties (without parenthesis)
     *
     * @param string $resourceName
     *
     * @return PrintcartResource
     */
    public function __get($resourceName)
    {
        return $this->$resourceName();
    }

    /**
     * Return PrintcartResource instance for a resource.
     * Called like an object method (with parenthesis) optionally with the resource ID as the first argument
     * @example $printcart->Product($productID); //Return a specific product defined by $productID
     *
     * @param string $resourceName
     * @param array $arguments
     *
     * @throws SdkException if the $name is not a valid PrintcartResource resource.
     *
     * @return PrintcartResource
     */
    public function __call($resourceName, $arguments)
    {
        if (!in_array($resourceName, $this->resources)) {
            $message = "Invalid resource name $resourceName. Pls check the API Reference to get the appropriate resource name.";
            throw new SdkException($message);
        }

        $resourceClassName = __NAMESPACE__ . "\\$resourceName";

        //If first argument is provided, it will be considered as the ID of the resource.
        $resourceID = !empty($arguments) ? $arguments[0] : null;

        //Initiate the resource object
        $resource = new $resourceClassName($resourceID);

        return $resource;
    }

    /**
     * Configure the SDK client
     *
     * @param array $config
     *
     * @return PrintcartSDK
     */
    public static function config($config)
    {
        /**
         * Reset config to it's initial values
         */
        self::$config = array(
            'ApiVersion' => self::$defaultApiVersion
        );

        foreach ($config as $key => $value) {
            self::$config[$key] = $value;
        }

        self::$config['ApiUrl'] = self::$apiUrl . '/' . self::$config['ApiVersion'] . '/';

        if (isset($config['Guzzle']) && is_array($config['Guzzle'])) {
            GuzzleRequest::config($config['Guzzle']);
        }

        return new PrintcartSDK;
    }

    /**
     * Get the api url of the configured shop
     *
     * @return string
     */
    public static function getApiUrl()
    {
        return self::$config['ApiUrl'];
    }
}
