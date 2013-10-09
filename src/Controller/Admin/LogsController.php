<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

namespace Module\Payment\Controller\Admin;

use Pi;
use Pi\Mvc\Controller\ActionController; 

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
class LogsController extends ActionController
{

    public function indexAction()
    {
    	$this->view()->setTemplate('empty');
    }	
}