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
use Zend\Json\Json;
use Zend\Math\Rand;

/*
 * Pi::api('invoice', 'payment')->createInvoice($module, $part, $item, $amount, $adapter, $description);
 * Pi::api('invoice', 'payment')->getInvoice($id);
 * Pi::api('invoice', 'payment')->getInvoiceFromItem($item);
 * Pi::api('invoice', 'payment')->updateInvoice($id);
 * Pi::api('invoice', 'payment')->updateModuleInvoice($id);
 * Pi::api('invoice', 'payment')->getInvoiceRandomId($id);
 */

class Invoice extends AbstractApi
{
    /**
     * Create Invoice
     *
     * @return array
     */
    public function createInvoice($module, $part, $item, $amount, $adapter, $description)
    {
    	$result = array();
    	$uid = Pi::user()->getId();
    	if ($uid) {
    		if (empty($module) || 
                empty($part) || 
                empty($item) || 
                empty($amount) || 
                empty($adapter) || 
                empty($description)) 
            {
    			$result['status'] = 0;
    			$result['invoice_url'] = '';
    			$result['message'] = __('Please send all informations for create invoice');
    		} else {
    			// create invoice
    			$row = Pi::model('invoice', $this->getModule())->createRow();
    			$row->module = $module;
    			$row->part = $part;
    			$row->item = $item;
    			$row->amount = $amount;
                $row->adapter = $adapter;
    			$row->description = $description;
    			$row->uid = $uid;
    			$row->ip = Pi::user()->getIp();
    			$row->status = 2;
    			$row->time_create = time();
    			$row->save();
    			// return array
    			$result['status'] = $row->status;
    			$result['invoice_url'] = Pi::service('url')->assemble('payment', array(
                    'module'        => $this->getModule(),
                    'action'        => 'invoice',
                    'id'            => $row->id,
                ));
    			$result['message'] = __('Your invoice create successfully');
    		}
    	} else {
    		$result['status'] = 0;
    		$result['invoice_url'] = '';
    		$result['message'] = __('Please login for create invoice');
    	}
    	return $result;
    }	

    public function getInvoice($id)
    {
        $invoice = array();
        $row = Pi::model('invoice', $this->getModule())->find($id);
        if (is_object($row)) {
            $invoice = $row->toArray();
            $invoice['description'] = (array) Json::decode($invoice['description']);
            $invoice['create'] = _date($invoice['time_create']);
            $invoice['pay'] = Pi::service('url')->assemble('payment', array(
                'module'        => $this->getModule(),
                'action'        => 'pay',
                'id'            => $invoice['id'],
            ));
        }
        return $invoice;
    }

    public function getInvoiceFromItem($item)
    {
        $invoice = array();
        $row = Pi::model('invoice', $this->getModule())->find($item, 'item');
        if (is_object($row)) {
            $invoice = $row->toArray();
            $invoice['description'] = (array) Json::decode($invoice['description']);
            $invoice['create'] = _date($invoice['time_create']);
            $invoice['invoice_url'] = Pi::service('url')->assemble('payment', array(
                    'module'        => $this->getModule(),
                    'action'        => 'invoice',
                    'id'            => $row->id,
                ));
            $invoice['pay_url'] = Pi::service('url')->assemble('payment', array(
                'module'        => $this->getModule(),
                'action'        => 'pay',
                'id'            => $invoice['id'],
            ));
        }
        return $invoice;
    }

    public function getInvoiceRandomId($id)
    {
        $rand = Rand::getInteger(10, 99);
        $invoice = array();
        $row = Pi::model('invoice', $this->getModule())->find($id);
        if (is_object($row)) {
            $row->random_id = sprintf('%s%s', $row->id, $rand);
            $row->save();
            $invoice = $row->toArray();
            $invoice['description'] = (array) Json::decode($invoice['description']);
            $invoice['create'] = _date($invoice['time_create']);
            $invoice['pay'] = Pi::service('url')->assemble('payment', array(
                'module'        => $this->getModule(),
                'action'        => 'pay',
                'id'            => $invoice['id'],
            ));
        }
        return $invoice;
    }

    public function updateInvoice($id)
    {
        $invoice = array();
        $row = Pi::model('invoice', $this->getModule())->find($id, 'random_id');
        if (is_object($row)) {
            $row->status = 1;
            $row->time_payment = time();
            $row->save();
            $invoice = $row->toArray();
        }
        return $invoice;
    }

    public function updateModuleInvoice($id)
    {
        $invoice = $this->getInvoice($id);
        return Pi::api($invoice['part'], $invoice['module'])->updatePayment(
            $invoice['item'], 
            $invoice['amount'], 
            $invoice['adapter']);
    }
}	