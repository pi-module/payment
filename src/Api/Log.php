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
 * Pi::api('log', 'payment')->setLog($log);
 * Pi::api('log', 'payment')->getLog($invoice);
 * Pi::api('log', 'payment')->getTrueLog($invoice);
 */

class Log extends AbstractApi
{
    /**
     * Create Invoice
     *
     * @return array
     */
    public function setLog($log)
    {
        // create log
        $row = Pi::model('log', $this->getModule())->createRow();
        $row->invoice = isset($log['invoice']) ? $log['invoice'] : '';
        $row->gateway = isset($log['gateway']) ? $log['gateway'] : '';
        $row->amount = isset($log['amount'])? $log['amount'] : 0;
        $row->authority = isset($log['authority']) ? $log['authority'] : 0;
        $row->status = isset($log['status']) ? $log['status'] : 0;
        $row->message = isset($log['message']) ? $log['message'] : '';
        $row->value = isset($log['value'])? $log['value'] : '';
        $row->time_create = time();
        $row->uid = Pi::user()->getId();
        $row->ip = Pi::user()->getIp();
        $row->save();
    }

    public function getLog($invoice)
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

    public function getTrueLog($invoice)
    {
        // set info
        $log = array();
        $where = array('invoice' => $invoice, 'status' => 1);
        // Get all logs
        $select = Pi::model('log', $this->getModule())->select()->where($where)->limit(1);
        $rowset = Pi::model('log', $this->getModule())->selectWith($select)->current();
        if (is_object($rowset)) {
            $log = $rowset->toArray();
            $log['value'] = Json::decode($log['value'], true);
            $log['time_create_view'] = _date($log['time_create']);
            $log['amount_view'] = _currency($log['amount']);
            $log['gatewayMessage'] = Pi::api('gateway', 'payment')->getGatewayMessage($log['gateway'], $log['value']);
        }
        // return
        return $log;
    }
}	