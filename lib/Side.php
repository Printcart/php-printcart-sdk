<?php
/**
 * Created by PhpStorm.
 * @author Printcart <printcart@gmail.com>
 * Created at 01/18/21 10:00 AM GTM+07:00
 *
 * @see https://docs.printcart.com/rest-api-reference/ Printcart API Reference
 */

namespace PHPPrintcart;


/**
 * --------------------------------------------------------------------------
 * Side -> Child Resources
 * --------------------------------------------------------------------------
 */
class Side extends PrintcartResource
{
    /**
     * @inheritDoc
     */
    public $resourceKey = 'sides';

    /**
     * @inheritDoc
     */
    protected $childResource = array(
        'Template',
    );
}