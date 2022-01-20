<?php
/**
 * Created by PhpStorm.
 * @author Printcart <printcart@gmail.com>
 * Created at 01/18/21 10:00 AM GTM+07:00
 *
 */

namespace PHPPrintcart;


class ConnectTest extends TestResource
{
    /**
     * @inheritDoc
     */
    public $account = array(
        'username' => 'printcart@gmail.com',
        'password' => 'printcart'
    );

    /**
     * @inheritDoc
     */
    public $errorAccount = array(
        'username' => 'printcart@gmail.com',
        'password' => '123456'
    );
}