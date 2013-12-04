<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

namespace Module\Payment\Gateway;

use Pi;
use Zend\Json\Json;

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
abstract class AbstractGateway
{
    public $gatewayAdapter = '';

    public $gatewayRow = '';

    public $gatewayIsActive = '';

    public $gatewayOption = array();

    public $gatewayPayInformation = array();

    public $gatewaySettingForm = array();

    public $gatewayPayForm = array();

    public $gatewayInformation = array();

    public $gatewayInvoice = array();

    public $gatewayRedirectUrl = '';

    public $gatewayBackUrl = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setInformation();
        $this->setAdapter();
        $this->setRow();
        $this->setOption();
        $this->setSettingForm();
        $this->setPayForm();
        $this->setIsActive();
    }

    abstract public function setAdapter();

    abstract public function setInformation();

    abstract public function setSettingForm();

    abstract public function setRedirectUrl();

    abstract public function verifyPayment($value);

    static public function getAllList()
    {
        $list = array();
        $gatewayPath = 'usr/module/payment/src/Gateway';
        $fullPath = Pi::path($gatewayPath);
        $allPath = scandir($fullPath);
        foreach ($allPath as $path) {
            $dir = sprintf(Pi::path('usr/module/payment/src/Gateway/%s'), $path);
            if (is_dir($dir)) {
                $class = sprintf('Module\Payment\Gateway\%s\Gateway', $path);
                if (class_exists($class)) {
                    $gateway = new $class;
                    if (is_object($gateway)) {
                        $list[$path] = $gateway->canonize();
                    }
                }
            }
        }
        return $list;
    }

    static public function getActiveList()
    {
        $where = array('status' => 1);
        // Get list of story
        $select = Pi::model('gateway', 'payment')->select()->where($where);
        $rowset = Pi::model('gateway', 'payment')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $item[$row->id] = $row->toArray();
            $item[$row->id]['option'] = (array) Json::decode($item[$row->id]['option']);
            $dir = sprintf(Pi::path('usr/module/payment/src/Gateway/%s'), $item[$row->id]['path']);
            if (is_dir($dir)) {
                $class = sprintf('Module\Payment\Gateway\%s\Gateway', $item[$row->id]['path']);
                if (class_exists($class)) {
                    $obj = new $class;
                    if (is_object($obj)) {
                        $list[$item[$row->id]['id']] = $item[$row->id];
                    }
                }
            }
        }
        return $list;
    }

    static public function getActiveName()
    {
        $where = array('status' => 1);
        // Get list of story
        $select = Pi::model('gateway', 'payment')->select()->where($where);
        $rowset = Pi::model('gateway', 'payment')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->path] = $row->path;
        }
        return $list;
    }

    static public function getGateway($adapter = '')
    {
        if (!empty($adapter)) {
            $class = sprintf('Module\Payment\Gateway\%s\Gateway', $adapter);
            if (class_exists($class)) {
                $gateway = new $class;
                if (is_object($gateway)) {
                    return $gateway;
                }    
            }
        } 
        return false;
    }

    static public function getGatewayInfo($adapter = '')
    {
        if (!empty($adapter)) {
            $gateway = Pi::model('gateway', 'payment')->find($adapter, 'path')->toArray();
            return $gateway;
        } 
        return false;
    }

    protected function canonize()
    {
        $canonize = array();
        if ($this->gatewayIsActive == -1) {
            $canonize = $this->gatewayInformation;
        } else {
            $canonize = array_merge($this->gatewayRow, $this->gatewayInformation);
        }
        $canonize['status'] = $this->gatewayIsActive;
        $canonize['option'] = $this->gatewayOption;
        $canonize['adapter'] = $this->gatewayAdapter;
        return $canonize;
    }


    protected function setRow()
    {
        $gateway = Pi::model('gateway', 'payment')->find($this->gatewayAdapter, 'path');
        if (is_object($gateway)) {
            $this->gatewayRow = $gateway->toArray();
        }
        return $this;
    }

    protected function setOption()
    {
        if (is_array($this->gatewayRow) && isset($this->gatewayRow['option'])) {
            $this->gatewayOption =  (array) Json::decode($this->gatewayRow['option']);
        }
        return $this; 
    }

    protected function setIsActive()
    {
        $this->gatewayIsActive = -1;
        if (is_array($this->gatewayRow) && isset($this->gatewayRow['status'])) {
            $this->gatewayIsActive = $this->gatewayRow['status'];
        }
        return $this;
    }

    protected function setBackUrl()
    {
        $this->gatewayBackUrl = Pi::url(Pi::service('url')->assemble('payment', array(
                    'module'        => 'payment',
                    'action'        => 'result',
                )));
    }

    public function setInvoice($invoice = array())
    {
        if (is_array($invoice) && !empty($invoice)) {
            $this->gatewayInvoice = $invoice;
            $this->setBackUrl();
            $this->setRedirectUrl();
        }
        return $this;
    }
}	