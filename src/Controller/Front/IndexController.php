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

class IndexController extends ActionController
{
    public function indexAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();
        // List of all user invoices

        $test = Pi::service('url')->assemble('payment', array(
                    'module'        => $this->getModule(),
                    'action'        => 'result',
                ));

        // Set view
        $this->view()->setTemplate('empty');
        $this->view()->assign('test', $test);
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
        $invoice = Pi::api('payment', 'invoice')->getInvoice($id);
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
        // Set form
        $form = $this->setPayment($invoice);
        // Set view
        $this->view()->setTemplate('pay');
        $this->view()->assign('invoice', $invoice);
        $this->view()->assign('form', $form);
    }  

    public function resultAction()
    {
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            // finish payment
            $gateway = Pi::api('payment', 'gateway')->getGateway('Mellat');
            $verify = $gateway->verifyPayment($post);
            if ($verify['status'] == 1) {
                $finish = $gateway->finishPayment($verify);
                if ($finish['status'] == 1) {
                    $url = Pi::api('payment', 'invoice')->updateModuleInvoice($finish['invoice']);
                    $this->jump($url, 'Back to module');
                } else {

                }
            } else {

            }
        } else {

        }
        // Set view
        $this->view()->setTemplate('empty');
        $this->view()->assign('test', array());
    }

    public function setPayment($invoice)
    {
        // Get gateway object
        $gateway = Pi::api('payment', 'gateway')->getGateway($invoice['adapter']);
        $gateway->setInvoice($invoice);
        // Set form
        $form = new PayForm('pay', $gateway->gatewayPayForm);
        $form->setAttribute('action', $gateway->gatewayRedirectUrl);
        // Set form values
        if (!empty($gateway->gatewayPayInformation)) {
            foreach ($gateway->gatewayPayInformation as $key => $value) {
                 $values[$key] = $value;
            }
            $form->setData($values);
        }
        return $form;
    }
}	