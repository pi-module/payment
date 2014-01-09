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

namespace Module\Payment\Api;

use Pi;
use Pi\Application\AbstractApi;
use Module\Payment\Gateway\AbstractGateway;

/*
 * Pi::api('gateway', 'payment')->getAllGatewayList();
 * Pi::api('gateway', 'payment')->getActiveGatewayList();
 * Pi::api('gateway', 'payment')->getActiveGatewayName();
 * Pi::api('gateway', 'payment')->getGateway($adapter);
 * Pi::api('gateway', 'payment')->getGatewayInfo($adapter);
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

    public function getActiveGatewayName()
    {
        return AbstractGateway::getActiveName();
    }

    public function getGateway($adapter = '')
    {
        return AbstractGateway::getGateway($adapter);
    }

    public function getGatewayInfo($adapter = '')
    {
        return AbstractGateway::getGatewayInfo($adapter);
    }	
}	