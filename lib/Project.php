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
 * Project -> Child Resources
 * --------------------------------------------------------------------------
 */
class Project extends PrintcartResource
{
    /**
     * @inheritDoc
     */
    public $resourceKey = 'projects';

    /**
     * @inheritDoc
     */
    protected $childResource = array(
        'Design',
        'Product',
    );
}