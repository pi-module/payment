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
    'category' => array(
        array(
            'title' => __('Admin'),
            'name' => 'admin'
        ),
    ),
    'item' => array(
    	// Admin
        'admin_perpage' => array(
            'category' => 'admin',
            'title' => __('Perpage'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 50
        ),
    ),
);