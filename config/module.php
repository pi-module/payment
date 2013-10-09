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
    // Module meta
    'meta'  => array(
        // Module title, required
        'title'         => __('Payment'),
        // Description, for admin, optional
        'description'   => __('Payment gateway'),
        // Version number, required
        'version'       => '1',
        // Distribution license, required
        'license'       => 'New BSD',
        // Logo image, for admin, optional
        'logo'          => 'image/logo.png',
        // Readme file, for admin, optional
        'readme'        => 'docs/readme.txt',
        // Direct download link, available for wget, optional
        //'download'      => 'http://dl.xoopsengine.org/core',
        // Demo site link, optional
        'demo'          => 'http://pialog',
        // Module is ready for clone? Default as false
        'clonable'      => false,
    ),
    // Author information
    'author'    => array(
        // Author full name, required
        'Dev'      => 'Hossein Azizabadi',
        // Email address, optional
        'Email'     => 'azizabadi@faragostaresh.com',
        // Website link, optional
        'Website'   => 'http://pialog.org',
        'Architect' => '@voltan',
        'Design'    => '@voltan',
        // Credits and aknowledgement, optional
        'Credits'   => 'Pi Engine Team; Zend Framework Team; FaraGostaresh Team.'
    ),
    // Maintenance actions
    'maintenance'   => array(
        // resource
        'resource' => array(
            // Database meta
            'database' => 'database.php',
            // Bootstrap, priority
            'bootstrap' => 1,
            // Navigation definition
            'navigation'    => 'navigation.php',
            // Routes, first in last out; bigger priority earlier out
            //'route'         => 'route.php',
        )
    )
);