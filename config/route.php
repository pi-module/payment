<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

/**
 * Module meta
 *
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
return array(
    // payment route
    'payment'  => array(
        'name'      => 'payment',
        'type'      => 'Module\Payment\Route\Payment',
        'priority'  => 5,
        'options'   => array(
            'route'    => '/payment',
        ),
    ),
);

