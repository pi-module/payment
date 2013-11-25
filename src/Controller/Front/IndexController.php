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
        if (empty($invoice)) {
           $this->jump(array('', 'action' => 'index'), __('The invoice not found.'));
        }
        // Set Payment
        $form = '';
        if ($invoice['status'] == 2) {
            $form = $this->setPayment($invoice);
        }
        // set view
        $this->view()->setTemplate('invoice');
        $this->view()->assign('form', $form);
        $this->view()->assign('invoice', $invoice);
    }    

    public function resultAction()
    {
        $this->view()->setTemplate('empty');

        echo '<pre>';
        print_r($_POST);
        echo '</pre>';

        echo '<pre>';
        print_r($_GET);
        echo '</pre>';

        // invoice id from session or url
        //$id = '';
        //$invoice = Pi::api('payment', 'invoice')->updateInvoice($id);
        // Update module information
        //$url = Pi::api('payment', 'invoice')->updateModuleInvoice($invoice)
        //return $this->jump($url, 'Back to module');
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