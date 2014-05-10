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
use Pi\Application\Api\AbstractApi;
use Zend\Json\Json;

/*
 * Pi::api('log', 'payment')->setLot($log);
 * Pi::api('log', 'payment')->getLot($invoice);
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
        $row->message = $log['message'];
        $row->save();
    }

    public function getLot($invoice)
    {
        // set info
        $list = array();
        $where = array('invoice' => $invoice);
        // Get all logs
        $select = Pi::model('log', $this->getModule())->select()->where($where);
        $rowset = Pi::model('log', $this->getModule())->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = $row->toArray();
            $list[$row->id]['value'] = Json::decode($list[$row->id]['value'], true);
            $list[$row->id]['time_create_view'] = _date($list[$row->id]['time_create']);
            $list[$row->id]['amount_view'] = _currency($list[$row->id]['amount']);
        }
        // return
        return $list;
    }
}	