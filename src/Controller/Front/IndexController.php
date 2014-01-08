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

namespace Module\Payment\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Module\Payment\Form\PayForm;
use Zend\Json\Json;

class IndexController extends ActionController
{
    public function indexAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();
        // Get info
        $module = $this->params('module');
        $list = array();
        $order = array('id DESC', 'time_create DESC');
        $where = array('uid' => Pi::user()->getId());
        $select = $this->getModel('invoice')->select()->where($where)->order($order);
        $rowset = $this->getModel('invoice')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = $row->toArray();
            $list[$row->id]['description'] = Json::decode($list[$row->id]['description']);
            $list[$row->id]['user'] = Pi::user()->get($list[$row->id]['uid'], array('id', 'identity', 'name', 'email'));
            $list[$row->id]['time_create_view'] = _date($list[$row->id]['time_create']);
            $list[$row->id]['time_payment_view'] = ($list[$row->id]['time_payment']) ? _date($list[$row->id]['time_payment']) : __('Not yet');
        }
        // Set view
        $this->view()->setTemplate('list');
        $this->view()->assign('list', $list);
    }

    public function invoiceAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();
        // Get invoice
        $id = $this->params('id');
        $invoice = Pi::api('payment', 'invoice')->getInvoice($id);
        // Check invoice
        if (empty($invoice)) {
           $this->jump(array('', 'action' => 'index'), __('The invoice not found.'));
        }
        // Check invoice is for this user
        if ($invoice['uid'] != Pi::user()->getId()) {
            $this->jump(array('', 'action' => 'index'), __('This is not your invoice.'));
        }
        // set view
        $this->view()->setTemplate('invoice');
        $this->view()->assign('invoice', $invoice);
    }

    public function payAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();
        // Get invoice
        $id = $this->params('id');
        $invoice = Pi::api('payment', 'invoice')->getInvoiceRandomId($id);
        // Check invoice
        if (empty($invoice)) {
           $this->jump(array('', 'action' => 'index'), __('The invoice not found.'));
        }
        // Check invoice not payd
        if ($invoice['status'] != 2) {
            $this->jump(array('', 'action' => 'index'), __('The invoice payd.'));
        }
        // Check invoice is for this user
        if ($invoice['uid'] != Pi::user()->getId()) {
            $this->jump(array('', 'action' => 'index'), __('This is not your invoice.'));
        }
        // Check running pay processing
        $processing = Pi::api('payment', 'processing')->checkProcessing();
        if (!$processing) {
            $message = __('Another payment at process by you, Please finish that payment first or try after 30 minutes');
            $this->jump(array('', 'action' => 'index'), $message);
        }
        // Set pay processing
        Pi::api('payment', 'processing')->setProcessing($invoice);
        // Get gateway object
        $gateway = Pi::api('payment', 'gateway')->getGateway($invoice['adapter']);
        $gateway->setInvoice($invoice);
        // Check error
        if ($gateway->gatewayError) {
            $this->jump(array('', 'action' => 'index'), $gateway->gatewayError);
        }
        // Set form
        $form = new PayForm('pay', $gateway->gatewayPayForm);
        $form->setAttribute('action', $gateway->gatewayRedirectUrl);
        // Set form values
        if (!empty($gateway->gatewayPayInformation)) {
            foreach ($gateway->gatewayPayInformation as $key => $value) {
                if (!empty($value)) {
                    $values[$key] = $value;
                } else {
                    $this->jump(array('', 'action' => 'index'), sprintf(__('Error to get %s.'), $value)); 
                }
            }
            $form->setData($values);
        } else {
            $this->jump(array('', 'action' => 'index'), __('Error to get information.')); 
        }
        // Set view
        $this->view()->setLayout('layout-content');
        $this->view()->setTemplate('pay');
        $this->view()->assign('invoice', $invoice);
        $this->view()->assign('form', $form);
    }  

    public function resultAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();
        // Get processing
        $processing = Pi::api('payment', 'processing')->getProcessing();
        // Check processing
        if (!$processing) {
            $message = __('Your running pay processing not set');
            $this->jump(array('', 'action' => 'index'), $message);
        }
        // Check ip
        if ($processing['ip'] != Pi::user()->getIp()) {
            $message = __('Your IP address changed and processing not valid');
            $this->jump(array('', 'action' => 'index'), $message);
        }
        // Get post
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            // Get gateway
            $gateway = Pi::api('payment', 'gateway')->getGateway($processing['adapter']);
            // verify payment
            $verify = $gateway->verifyPayment($post, $processing);
            // Check error
            if ($gateway->gatewayError) {
                $this->jump(array('', 'action' => 'index'), $gateway->gatewayError);
            }
            // Check status
            if ($verify['status'] == 1) {
                // Update module order / invoice and get back url
                $url = Pi::api('payment', 'invoice')->updateModuleInvoice($verify['invoice']);
                // Remove processing
                Pi::api('payment', 'processing')->removeProcessing();
                // jump to module
                $message = __('Your payment were successfully. Back to module');
                $this->jump($url, $message);
            } else {
                $message = __('Your payment wont successfully.');
            }
        } else {
            $message = __('Did not set any request');
        }
        // Set view
        $this->view()->setTemplate('result');
        $this->view()->assign('message', $message);
    }
}	