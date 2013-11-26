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
use Zend\Json\Json;

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
class Log extends AbstractApi
{
    /**
     * Create Invoice
     *
     * @return array
     */
    public function setLot($log)
    {
        // create log
        $row = Pi::model('log', $this->getModule())->createRow();
        $row->invoice = $log['invoice'];
        $row->gateway = $log['gateway'];
        $row->time_create = time();
        $row->uid = Pi::user()->getId();
        $row->amount = $log['amount'];
        $row->authority = $log['authority'];
        $row->ip = Pi::user()->getIp();
        $row->status = $log['status'];
        $row->value = $log['value'];
        $row->save();
    }
}	