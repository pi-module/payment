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
use Module\Payment\Form\RemoveForm;
use Zend\Json\Json;

class IndexController extends ActionController
{
    public function indexAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();
        // Get module 
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get info
        $list = array();
        $order = array('id DESC', 'time_create DESC');
        $where = array('uid' => Pi::user()->getId());
        if (!$config['payment_shownotpay']) {
            $where['status'] = 1;
        }
        $select = $this->getModel('invoice')->select()->where($where)->order($order);
        $rowset = $this->getModel('invoice')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = $row->toArray();
            $list[$row->id]['description'] = Json::decode($list[$row->id]['description']);
            $list[$row->id]['user'] = Pi::user()->get($list[$row->id]['uid'], array('id', 'identity', 'name', 'email'));
            $list[$row->id]['time_create_view'] = _date($list[$row->id]['time_create']);
            $list[$row->id]['time_payment_view'] = ($list[$row->id]['time_payment']) ? _date($list[$row->id]['time_payment']) : __('Not yet');
            $list[$row->id]['amount_view'] = _currency($list[$row->id]['amount']);
        }
        // Set view
        $this->view()->setTemplate('list');
        $this->view()->assign('list', $list);
    }

    public function invoiceAction()
    {
        // Check user
        $this->checkUser();
        // Get invoice
        $id = $this->params('id');
        $invoice = Pi::api('invoice', 'payment')->getInvoice($id);
        // Check invoice
        if (empty($invoice)) {
           $this->jump(array('', 'action' => 'index'), __('The invoice not found.'));
        }
        // Check invoice is for this user
        if (Pi::service('authentication')->hasIdentity()) {
            if ($invoice['uid'] != Pi::user()->getId()) {
                $this->jump(array('', 'action' => 'index'), __('This is not your invoice.'));
            }
        } else {
            if (!isset($_SESSION['payment']['invoice_id']) || $_SESSION['payment']['invoice_id'] != $invoice['id']) {
                $this->jump(array('', 'action' => 'index'), __('This is not your invoice.'));
            }
            // Set session
            $_SESSION['payment']['process_update'] = time();
        }
        // set view
        $this->view()->setTemplate('invoice');
        $this->view()->assign('invoice', $invoice);
    }

    public function payAction()
    {
        // Check user
        $this->checkUser();
        // Get invoice
        $id = $this->params('id');
        $invoice = Pi::api('invoice', 'payment')->getInvoiceRandomId($id);
        // Check invoice
        if (empty($invoice)) {
           $this->jump(array('', 'action' => 'index'), __('The invoice not found.'));
        }
        // Check invoice not payd
        if ($invoice['status'] != 2) {
            $this->jump(array('', 'action' => 'index'), __('The invoice payd.'));
        }
        // Check invoice is for this user
        if (Pi::service('authentication')->hasIdentity()) {
            if ($invoice['uid'] != Pi::user()->getId()) {
                $this->jump(array('', 'action' => 'index'), __('This is not your invoice.'));
            }
        } else {
            if (!isset($_SESSION['payment']['invoice_id']) || $_SESSION['payment']['invoice_id'] != $invoice['id']) {
                $this->jump(array('', 'action' => 'index'), __('This is not your invoice.'));
            }
            // Set session
            $_SESSION['payment']['process_update'] = time();
        }
        // Check running pay processing
        $processing = Pi::api('processing', 'payment')->checkProcessing();
        if (!$processing) {
            return $this->redirect()->toRoute('', array(
                'controller' => 'index',
                'action'     => 'remove',
            ));
        }
        // Set pay processing
        Pi::api('processing', 'payment')->setProcessing($invoice);
        // Get gateway object
        $gateway = Pi::api('gateway', 'payment')->getGateway($invoice['adapter']);
        $gateway->setInvoice($invoice);
        // Check error
        if ($gateway->gatewayError) {
            // Remove processing
            Pi::api('processing', 'payment')->removeProcessing();
            $this->jump(array('', 'action' => 'result'), $gateway->gatewayError);
        }
        // Set form
        $form = new PayForm('pay', $gateway->gatewayPayForm);
        $form->setAttribute('action', $gateway->gatewayRedirectUrl);
        // Set form values
        if (!empty($gateway->gatewayPayInformation)) {
            foreach ($gateway->gatewayPayInformation as $key => $value) {
                if ($value || $value == 0) {
                    $values[$key] = $value;
                } else {
                    // Get gateway object
                    $gateway = Pi::api('gateway', 'payment')->getGateway($invoice['adapter']);
                    $this->jump(array('', 'action' => 'result'), sprintf(__('Error to get %s.'), $key)); 
                }
            }
            $form->setData($values);
        } else {
            // Get gateway object
            $gateway = Pi::api('gateway', 'payment')->getGateway($invoice['adapter']);
            $this->jump(array('', 'action' => 'result'), __('Error to get information.')); 
        }
        // Set view
        $this->view()->setLayout('layout-content');
        $this->view()->setTemplate('pay');
        $this->view()->assign('invoice', $invoice);
        $this->view()->assign('form', $form);
    }

    public function resultAction()
    {
        // Check user
        $this->checkUser();
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Get request
        $request = '';
        if ($this->request->isPost()) {
            $request = $this->request->getPost();
        }    
        // Check request
        if (!empty($request)) {
            // Get processing
            $processing = Pi::api('processing', 'payment')->getProcessing();
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
            // Get gateway
            $gateway = Pi::api('gateway', 'payment')->getGateway($processing['adapter']);
            // verify payment
            $verify = $gateway->verifyPayment($request, $processing);
            // Check error
            if ($gateway->gatewayError) {
                // Remove processing
                Pi::api('processing', 'payment')->removeProcessing();
                // Url
                if (!empty($config['payment_gateway_error_url'])) {
                    $url = $config['payment_gateway_error_url'];
                } else {
                    $url = $this->url(array('', 'action' => 'index'));
                }
                // jump
                $this->jump($url, $gateway->gatewayError);
            }
            // Check status
            if ($verify['status'] == 1) {
                // Update module order / invoice and get back url
                $url = Pi::api('invoice', 'payment')->updateModuleInvoice($verify['invoice']);
                // Remove processing
                Pi::api('processing', 'payment')->removeProcessing();
                // jump to module
                $message = __('Your payment were successfully. Back to module');
                $this->jump($url, $message);
            } else {
                // Remove processing
                Pi::api('processing', 'payment')->removeProcessing();
                $message = __('Your payment wont successfully.');
            }
        } else {
            // Remove processing
            Pi::api('processing', 'payment')->removeProcessing();
            $message = __('Did not set any request');
        }
        // Set view
        $this->view()->setTemplate('result');
        $this->view()->assign('message', $message);
    }

    public function notifyAction()
    {
        // Get module 
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get request
        $request = '';
        if ($this->request->isPost()) {
            $request = $this->request->getPost();
        }
        // Check request
        if (!empty($request)) {

            // Start test log
            $log = array(
                'value' => array(
                    'level' => 1,
                    'post'  => $request,
                ),
            );
            Pi::api('log', 'payment')->setLog($log);
            // End test log

            // Get processing
            $processing = Pi::api('processing', 'payment')->getProcessing($request['invoice']);

            // Start test log
            $log = array(
                'value' => array(
                    'level' => 3,
                    'post'  => $request,
                    'processing'  => $processing,
                ),
            );
            Pi::api('log', 'payment')->setLog($log);
            // End test log

            // Check processing
            if ($processing) {

                // Start test log
                $log = array(
                    'value' => array(
                        'level' => 4,
                        'post'  => $request,
                        'processing'  => $processing,
                    ),
                );
                Pi::api('log', 'payment')->setLog($log);
                // End test log

                // Get gateway
                $gateway = Pi::api('gateway', 'payment')->getGateway($processing['adapter']);
                $verify = $gateway->verifyPayment($request, $processing);
                // Check error
                if ($gateway->gatewayError) {
                    // Remove processing
                    Pi::api('processing', 'payment')->removeProcessing($request['invoice']);
                    return false;
                } else {
                    if ($verify['status'] == 1) {
                        Pi::api('invoice', 'payment')->updateModuleInvoice($verify['invoice']);
                        Pi::api('processing', 'payment')->removeProcessing($request['invoice']);
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {

                // Start test log
                $log = array(
                    'value' => array(
                        'level' => 5,
                        'post'  => $request,
                        'processing'  => '',
                    ),
                );
                Pi::api('log', 'payment')->setLog($log);
                // End test log

                return false;
            }
        } else {
            // Start test log
            $log = array(
                'value' => array(
                    'level' => 2,
                    'post'  => '',
                ),
            );
            Pi::api('log', 'payment')->setLog($log);
            // End test log

            return false;
        }
    }

    public function removeAction()
    {
        // Get post
        if ($this->request->isPost()) {
            $data = $this->request->getPost()->toArray();
            if (isset($data['id']) && !empty($data['id'])) {
                Pi::api('processing', 'payment')->removeProcessing();
                $message = __('Your old payment process remove, please try new payment ation');
            } else {
                $message = __('Payment is clean');
            }
            $this->jump(array('', 'action' => 'index'), $message);
        } else {
            $processing = Pi::api('processing', 'payment')->getProcessing();
            if (isset($processing['id']) && !empty($processing['id'])) {
                $values['id'] = $processing['id'];
            } else {
                $message = __('Payment is clean');
                $this->jump(array('', 'action' => 'index'), $message);
            }
            // Set form
            $form = new RemoveForm('Remove');
            $form->setData($values);
            // Set view
            $this->view()->setTemplate('remove');
            $this->view()->assign('form', $form);
        }    
    }

    public function cancelAction()
    {
        // Set return
        $return = array(
            'website' => Pi::url(),
            'module' => $this->params('module'),
            'message' => 'finish',
        );
        // Set view
        $this->view()->setTemplate(false)->setLayout('layout-content');
        return Json::encode($return);
    }

    public function finishAction()
    {
        // Set return
        $return = array(
            'website' => Pi::url(),
            'module' => $this->params('module'),
            'message' => 'finish',
        );
        // Set view
        $this->view()->setTemplate(false)->setLayout('layout-content');
        return Json::encode($return);
    }

    public function errorAction()
    {
        // Set return
        $return = array(
            'website' => Pi::url(),
            'module' => $this->params('module'),
            'message' => 'error',
        );
        // Set view
        $this->view()->setTemplate(false)->setLayout('layout-content');
        return Json::encode($return);  
    }

    public function checkUser()
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Check config
        if ($config['payment_anonymous'] == 0) {
            // Check user is login or not
            Pi::service('authentication')->requireLogin();
        }
        // Check
        if (!Pi::service('authentication')->hasIdentity()) {
            if (!isset($_SESSION['payment']['process']) || $_SESSION['payment']['process'] != 1) {
                $this->jump(array('', 'action' => 'error'));
            }
            // Set session
            $_SESSION['payment']['process_update'] = time();
        }
        //
        return true;
    }
}