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
    // SQL schema/data file
    'sqlfile' => 'sql/mysql.sql',
    // Tables to be removed during uninstall, optional - the table list will be generated automatically upon installation
    // will be fix
    'schema' => array(
        'invoice' => 'table',
        'gateway' => 'table',
        'log' => 'table',
    )
);