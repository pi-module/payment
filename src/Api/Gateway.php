<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

namespace Module\Payment\Api;

use Pi;
use Pi\Application\AbstractApi;
use Module\Payment\Gateway\AbstractGateway;

/**
 * Payment Gateway APIs
 *
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
class Gateway extends AbstractApi
{
    public function getAllGatewayList()
    {
    	return AbstractGateway::getAllList();
    }

    public function getActiveGatewayList()
    {
    	return AbstractGateway::getActiveList();
    }

    public function getGateway($adapter = '')
    {
        return AbstractGateway::getGateway($adapter);
    }	
}	