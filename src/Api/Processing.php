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
 * Pi::api('processing', 'payment')->setProcessing($invoice);
 * Pi::api('processing', 'payment')->getProcessing();
 * Pi::api('processing', 'payment')->checkProcessing();
 * Pi::api('processing', 'payment')->removeProcessing();
 */

class Processing extends AbstractApi
{
    public function setProcessing($invoice)
    {
        // create processing
        $row = Pi::model('processing', $this->getModule())->createRow();
        $row->uid = Pi::user()->getId();
        $row->ip = Pi::user()->getIp();
        $row->invoice = $invoice['id'];
        $row->random_id = $invoice['random_id'];
        $row->adapter = $invoice['adapter'];
        $row->time_create = time();
        $row->save();
    }

    public function getProcessing()
    {
    	$uid = Pi::user()->getId();
    	$row = Pi::model('processing', $this->getModule())->find($uid, 'uid');
    	if (is_object($row)) {
    		$row = $row->toArray();
            return $row;
    	} else {
    		return false;
    	}
    }

    public function checkProcessing()
    {
    	$uid = Pi::user()->getId();
    	$row = Pi::model('processing', $this->getModule())->find($uid, 'uid');
    	if (is_object($row)) {
    		$time = time() - 900;
    		if ($time > $row->time_create) {
    			$this->removeProcessing();
                return true;
    		} else {
    			return false;
    		}	
    	} else {
    		return true;
    	}
    }

    public function removeProcessing()
    {
        $uid = Pi::user()->getId();
        $row = Pi::model('processing', $this->getModule())->find($uid, 'uid');
        $row->delete();
    }
}	