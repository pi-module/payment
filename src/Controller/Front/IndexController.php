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
        // Check user is login
        if (!Pi::service('user')->hasIdentity()) {
            return $this->jumpToDenied();
        }
        // List of all user invoices

        // set view
        $this->view()->setTemplate('empty');
    }

    public function invoiceAction()
    {
        // Check user is login
        if (!Pi::service('user')->hasIdentity()) {
            return $this->jumpToDenied();
        }
        // Get invoice
        //$id = $this->params('id');
        $id = 2;
        $invoice = Pi::api('payment', 'invoice')->getInvoice($id);
        if (empty($invoice)) {
            return $this->jumpToDenied();
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
    }

    public function createAction()
    {
        $this->view()->setTemplate('empty');

        $description = array(
            array(
                'title' => 'Test shop product 1',
                'price' => '340000',
                'description' => 'weqwe qwe we awe qwe awdasd asd asd asd ',
            ),
            array(
                'title' => 'Test shop product 2',
                'price' => '100000',
                'description' => 'weqadaqweqwe awdasd asd asd asd ',
            ),
        );
        $description = json_encode($description);

        $invoice = Pi::api('payment', 'invoice')->createInvoice('shop', 'product', '123', '100000', 'Mellat', $description);

        echo '<pre>';
        print_r($invoice);
        echo '</pre>';
        
    }

    public function setPayment($invoice)
    {
        // Get gateway object
        $gateway = Pi::api('payment', 'gateway')->getGateway($invoice['adapter']);
        $gateway->setInvoice($invoice);

        echo '<pre>';
        print_r($gateway);
        echo '</pre>';

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