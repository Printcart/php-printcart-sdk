<?php
/**
 * Created by PhpStorm.
 * @author Printcart <printcart@gmail.com>
 * Created at 01/18/21 10:00 AM GTM+07:00
 *
 * @see https://docs.printcart.com/rest-api-reference/ Printcart API Reference
 */

namespace PHPPrintcart;

use PHPPrintcart\Exception\ApiException;
use PHPPrintcart\Exception\SdkException;
use PHPPrintcart\GuzzleRequest;
use PHPPrintcart\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/*
|--------------------------------------------------------------------------
| Printcart API SDK Base Class
|--------------------------------------------------------------------------
|
| This class handles get, post, put, delete and any other custom actions for the API
|
*/

abstract class PrintcartResource
{
    /**
     * HTTP request headers
     *
     * @var array
     */
    protected $httpHeaders = array();

    /**
     * The base URL of the API Resource
     *
     * Example : https://yourshop.printcart.com/products
     *
     * @var string
     */
    protected $resourceUrl;

    /**
     * Key of the API Resource which is used to fetch data from request responses
     *
     * @var string
     */
    protected $resourceKey;

    /**
     * List of child Resource names / classes
     *
     * If any array item has an associative key => value pair, value will be considered as the resource name
     * (by which it will be called) and key will be the associated class name.
     *
     * @var array
     */
    protected $childResource = array();

    /**
     * The ID of the resource
     *
     * If provided, the actions will be called against that specific resource ID
     *
     * @var integer
     */
    public $id;

    
    /**
     * Create a new Printcart API resource instance.
     *
     * @param integer $id
     * @param string $parentResourceUrl
     *
     * @throws SdkException if Either Auth is not found in configuration
     */

    public function __construct($id = null, $parentResourceUrl = '')
    {
        $this->id = $id;

        $config = PrintcartSDK::$config;

        $this->resourceUrl = ($parentResourceUrl ? $parentResourceUrl . '/' :  $config['ApiUrl']) . $this->getResourcePath() . ($this->id ? '/' . $this->id : '');

        if (!isset($config['Username']) || !isset($config['Password'])) {
            throw new SdkException("Either Auth is required to access the resources. Please check SDK configuration!");
        } else {
            $this->httpHeaders = [
                'auth' => [
                    $config['Username'], 
                    $config['Password']
                ]
            ];
        }
    }

    /**
     * Return PrintcartResource instance for the child resource.
     *
     * @example $printcart->Product($productID)->Image->get(); //Here Product is the parent resource and Image is a child resource
     * Called like an object properties (without parenthesis)
     *
     * @param string $childName
     *
     * @return \PHPPrintcart\PrintcartResource
     */
    public function __get($childName)
    {
        return $this->$childName();
    }

    /**
     * Return PrintcartResource instance for the child resource or call a custom action for the resource
     *
     * @example $printcart->Product($productID)->Design()->get(); //Here Product is the parent resource and Design is a child resource
     * Called like an object method (with parenthesis) optionally with the resource ID as the first argument
     *
     * Note : If the $name starts with an uppercase letter, it's considered as a child class and a custom action otherwise
     *
     * @param string $name
     * @param array $arguments
     *
     * @throws SdkException if the $name is not a valid child resource or custom action method.
     *
     * @return mixed / PrintcartResource
     */
    public function __call($name, $arguments)
    {
        //If the $name starts with an uppercase letter, it's considered as a child class
        //Otherwise it's a custom action
        if (ctype_upper($name[0])) {
            //Get the array key of the childResource in the childResource array
            $childKey = array_search($name, $this->childResource);

            if ($childKey === false) {
                throw new SdkException("Child Resource $name is not available for " . $this->getResourceName());
            }

            //If any associative key is given to the childname, then it will be considered as the class name,
            //otherwise the childname will be the class name
            $childClassName = !is_numeric($childKey) ? $childKey : $name;

            $childClass = __NAMESPACE__ . "\\" . $childClassName;

            //If first argument is provided, it will be considered as the ID of the resource.
            $resourceID = !empty($arguments) ? $arguments[0] : null;

            $api = new $childClass($resourceID, $this->resourceUrl);

            return $api;
        }
    }

    /**
     * Get the resource name (or the class name)
     *
     * @return string
     */
    public function getResourceName()
    {
        return substr(get_called_class(), strrpos(get_called_class(), '\\') + 1);
    }

    /**
     * Get the resource path to be used to generate the api url
     *
     * Normally its the same as the pluralized version of the resource key,
     * when it's different, the specific resource class will override this function
     *
     * @return string
     */
    protected function getResourcePath()
    {
        return $this->resourceKey;
    }

    /**
     * Generate the custom url for api request based on the params and custom action (if any)
     *
     * @param array $urlParams
     *
     * @return string
     */
    public function generateUrl($urlParams = array())
    {
        return $this->resourceUrl . (!empty($urlParams) ? '?' . http_build_query($urlParams) : '');
    }

    /**
     * Generate a HTTP GET request and return results as an array
     *
     * @param array $urlParams Check Printcart API reference of the specific resource for the list of URL parameters
     * @param string $url
     * @param string $dataKey Keyname to fetch data from response array
     *
     * @uses GuzzleRequest::get() to send the HTTP request
     *
     * @throws ApiException if the response has an error specified
     *
     * @return array
     */
    public function get($urlParams = array(), $url = null)
    {
        if (!$url) $url  = $this->generateUrl($urlParams);

        $response = GuzzleRequest::get($url, $this->httpHeaders);

        return $this->processResponse($response);
    }

    /**
     * Call method to count resource
     *
     * @param array $dataArray Check Printcart API reference of the specific resource for the list of required and optional data elements to be provided
     * @param string $url
     * @param bool $wrapData
     *
     * @return array
     *
     * @throws ApiException if the response has an error specified
     * @uses GuzzleRequest::post() to send the HTTP request
     *
     */
    public function count($url = null, $wrapData = true)
    {
        if (!$url) $url = $this->generateUrl().'/count';

        $response = GuzzleRequest::count($url, $this->httpHeaders);

        return $this->processResponse($response);
    }

    /**
     * Call POST method to create a new resource
     *
     * @param array $dataArray Check Printcart API reference of the specific resource for the list of required and optional data elements to be provided
     * @param string $url
     * @param bool $wrapData
     *
     * @return array
     *
     * @throws ApiException if the response has an error specified
     * @uses GuzzleRequest::post() to send the HTTP request
     *
     */
    public function post($dataArray, $url = null, $wrapData = true)
    {
        if (!$url) $url = $this->generateUrl();

        if ($wrapData && !empty($dataArray)) $dataArray = $this->wrapData($dataArray);

        $this->httpHeaders = array_merge($this->httpHeaders,$dataArray);

        $response = GuzzleRequest::post($url, $this->httpHeaders);

        return $this->processResponse($response);
    }

    /**
     * Call PUT method to update an existing resource
     *
     * @param array $dataArray Check Printcart API reference of the specific resource for the list of required and optional data elements to be provided
     * @param string $url
     * @param bool $wrapData
     *
     * @return array
     *@throws GuzzleException if response received with unexpected HTTP code.
     *
     * @throws ApiException if the response has an error specified
     * @uses GuzzleRequest::put() to send the HTTP request
     *
     */
    public function put($dataArray, $url = null, $wrapData = true)
    {
        if (!$url) $url = $this->generateUrl();

        if ($wrapData && !empty($dataArray)) $dataArray = $this->wrapData($dataArray);

        $this->httpHeaders = array_merge($this->httpHeaders,$dataArray);

        $response = GuzzleRequest::put($url, $this->httpHeaders);

        return $this->processResponse($response);
    }

    /**
     * Call PUT BATCH method to update multiple existing resources
     *
     * @param array $dataArray Check Printcart API reference of the specific resource for the list of required and optional data elements to be provided
     * @param string $url
     * @param bool $wrapData
     *
     * @return array
     *@throws GuzzleException if response received with unexpected HTTP code.
     *
     * @throws ApiException if the response has an error specified
     * @uses GuzzleRequest::put() to send the HTTP request
     *
     */
    public function put_batch($dataArray, $url = null, $wrapData = true)
    {
        if (!$url) $url = $this->generateUrl().'/batch';

        if ($wrapData && !empty($dataArray)) $dataArray = $this->wrapData($dataArray);

        $this->httpHeaders = array_merge($this->httpHeaders,$dataArray);

        $response = GuzzleRequest::put($url, $this->httpHeaders);

        return $this->processResponse($response);
    }

    /**
     * Call DELETE method to delete an existing resource
     *
     * @param array $urlParams Check Printcart API reference of the specific resource for the list of URL parameters
     * @param string $url
     *
     * @uses GuzzleRequest::delete() to send the HTTP request
     *
     * @throws ApiException if the response has an error specified
     * @throws GuzzleException if response received with unexpected HTTP code.
     *
     * @return array an empty array will be returned if the request is successfully completed
     */
    public function delete($urlParams = array(), $url = null)
    {
        if (!$url) $url = $this->generateUrl($urlParams);

        $response = GuzzleRequest::delete($url, $this->httpHeaders);

        return $this->processResponse($response);
    }

    /**
     * Call DELETE BATCH method to delete multiple existing resources
     *
     * @param array $urlParams Check Printcart API reference of the specific resource for the list of URL parameters
     * @param string $url
     *
     * @uses GuzzleRequest::delete() to send the HTTP request
     *
     * @throws ApiException if the response has an error specified
     * @throws GuzzleException if response received with unexpected HTTP code.
     *
     * @return array an empty array will be returned if the request is successfully completed
     */
    public function delete_batch($dataArray = array(), $urlParams = array(), $url = null, $wrapData = true)
    {
        if (!$url) $url = $this->generateUrl($urlParams).'/batch';

        if ($wrapData && !empty($dataArray)) $dataArray = $this->wrapData($dataArray);

        $this->httpHeaders = array_merge($this->httpHeaders,$dataArray);

        $response = GuzzleRequest::delete($url, $this->httpHeaders);

        return $this->processResponse($response);
    }

    /**
     * Wrap data array with resource key
     *
     * @param array $dataArray
     * @param string $dataKey
     *
     * @return array
     */
    protected function wrapData($dataArray, $dataKey = null)
    {
        return array(\GuzzleHttp\RequestOptions::JSON => $dataArray);
    }

    /**
     * Convert an array to string
     *
     * @param array $array
     *
     * @internal
     *
     * @return string
     */
    protected function castString($array)
    {
        if ( ! is_array($array)) return (string) $array;

        $string = '';
        $i = 0;
        foreach ($array as $key => $val) {
            //Add values separated by comma
            //prepend the key string, if it's an associative key
            //Check if the value itself is another array to be converted to string
            $string .= ($i === $key ? '' : "$key - ") . $this->castString($val) . ', ';
            $i++;
        }

        //Remove trailing comma and space
        $string = rtrim($string, ', ');

        return $string;
    }

    /**
     * Process the request response
     *
     * @param array $responseArray Request response in array format
     * @param string $dataKey Keyname to fetch data from response array
     *
     * @return array
     * @throws GuzzleException if response received with unexpected HTTP code.
     *
     * @throws ApiException if the response has an error specified
     */
    public function processResponse($responseArray)
    {
        if ($responseArray === null) {
            //Something went wrong, Checking HTTP Codes
            $httpOK = 200; //Request Successful, OK.
            $httpCreated = 201; //Create Successful.
            $httpDeleted = 204; //Delete Successful.

            //should be null if any other library used for http calls
            $httpCode = GuzzleRequest::$lastHttpCode;

            if ($httpCode != null && $httpCode != $httpOK && $httpCode != $httpCreated && $httpCode != $httpDeleted) {
                throw new Exception\GuzzleException("Request failed with HTTP Code $httpCode.", $httpCode);
            }
        }

        return $responseArray->getBody()->getContents();
    }
}