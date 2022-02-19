<?php
/**
 * Created by PhpStorm.
 * @author Printcart <printcart@gmail.com>
 * Created at 01/18/21 10:00 AM GTM+07:00
 *
 * @see https://docs.printcart.com/rest-api-reference/ Printcart API Reference
 */

namespace PHPPrintcart;


use PHPPrintcart\Exception\GuzzleException;

/*
|--------------------------------------------------------------------------
| GuzzleRequest
|--------------------------------------------------------------------------
|
| This class handles get, post, put, delete HTTP requests
|
*/
class GuzzleRequest
{
    /**
     * HTTP Code of the last executed request
     *
     * @var integer
     */
    public static $lastHttpCode;

    /**
     * HTTP response headers of last executed request
     *
     * @var array
     */
    public static $lastHttpResponseHeaders = array();

    /**
     * Guzzle additional configuration
     *
     * @var array
     */
    protected static $config = array();

    /**
     * Initialize the curl resource
     *
     * @param string $url
     * @param array $httpHeaders
     *
     * @return resource
     */
    protected static function init($method, $url, $httpHeaders = array())
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request($method, $url, $httpHeaders);

        return $response;
    }

    /**
     * Implement a GET request and return output
     *
     * @param string $url
     * @param array $httpHeaders
     *
     * @return string
     */
    public static function get($url, $httpHeaders = array())
    {
        //Initialize the resource
        $response = self::init('GET', $url, $httpHeaders);

        return $response;
    }

    /**
     * Implement a POST request and return output
     *
     * @param string $url
     * @param array $httpHeaders
     *
     * @return string
     */
    public static function post($url, $httpHeaders = array())
    {
        //Initialize the resource
        $response = self::init('POST', $url, $httpHeaders);

        return $response;
    }

    /**
     * Implement a PUT request and return output
     *
     * @param string $url
     * @param array $httpHeaders
     *
     * @return string
     */
    public static function put($url, $httpHeaders = array())
    {
        //Initialize the resource
        $response = self::init('PUT', $url, $httpHeaders);

        return $response;
    }

    /**
     * Implement a DELETE request and return output
     *
     * @param string $url
     * @param array $httpHeaders
     *
     * @return string
     */
    public static function delete($url, $httpHeaders = array())
    {
        //Initialize the resource
        $response = self::init('DELETE', $url, $httpHeaders);

        return $response;
    }

    /**
     * Set Guzzle additional configuration
     *
     * @param array $config
     */
    public static function config($config = array())
    {
        self::$config = $config;
    }

    /**
     * Implement a GET request and return output
     *
     * @param string $url
     * @param array $httpHeaders
     *
     * @return string
     */
    public static function count($url, $httpHeaders = array())
    {
        //Initialize the resource
        $response = self::init('GET', $url, $httpHeaders);

        return $response;
    }
}