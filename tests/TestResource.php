<?php
/**
 * Created by PhpStorm.
 * @author Printcart <printcart@gmail.com>
 * Created at 01/18/21 10:00 AM GTM+07:00
 *
 */

namespace PHPPrintcart;

class TestResource extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PrintcartSDK $printcart;
     */
    public static $printcart;

    /**
     * @var account sample for testing guzzle
     */
    public $account;

    /**
     * @var array sample post with invalid data
     */
    public $errorAccount;

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass()
    {
        $config = array(
            'Username' => self::$account['username'], //Your account username
            'Password' => self::$account['password'], //Your account password
        );

        self::$printcart = PrintcartSDK::config($config);
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass()
    {
        self::$printcart = null;
    }
}