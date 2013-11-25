<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
return array(
    // route name
    'payment'  => array(
        'name'      => 'payment',
        'type'      => 'Module\Payment\Route\Payment',
        'options'   => array(
            'route'     => '/payment',
            'defaults'  => array(
                'module'        => 'payment',
                'controller'    => 'index',
                'action'        => 'index'
            )
        ),
    )
);