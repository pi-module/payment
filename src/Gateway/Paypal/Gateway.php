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

namespace Module\Payment\Gateway\Paypal;

use Pi;
use Module\Payment\Gateway\AbstractGateway;
use Zend\Json\Json;

class Gateway extends AbstractGateway
{  
    public function setAdapter()
    {
        $this->gatewayAdapter = 'Paypal';
    }

    public function setInformation()
    {
        $gateway = array();
        $gateway['title'] = __('Paypal');
        $gateway['path'] = 'Paypal';
        $gateway['type'] = 'online';
        $gateway['version'] = '1.0';
        $gateway['description'] = '';
        $gateway['author'] = 'Hossein Azizabadi <azizabadi@faragostaresh.com>';
        $gateway['credits'] = '@voltan';
        $gateway['releaseDate'] = 1380802565;
        $this->gatewayInformation = $gateway;
        return $gateway;
    }

    public function setSettingForm()
    {
        $form = array();
        // form path
        $form['path'] = array(
            'name'      => 'path',
            'label'     => __('path'),
            'type'      => 'hidden',
            'required'  => true,
        );
        // business
        $form['business'] = array(
            'name'      => 'business',
            'label'     => __('Paypal email address'),
            'type'      => 'text',
            'required'  => true,
        );
        // currency
        $form['currency'] = array(
            'name'      => 'currency',
            'label'     => __('Paypal currency'),
            'type'      => 'text',
            'required'  => true,
        );
        // cursymbol
        $form['cursymbol'] = array(
            'name'      => 'cursymbol',
            'label'     => __('Paypal currency symbol'),
            'type'      => 'text',
            'required'  => true,
        );
        // location
        $form['location'] = array(
            'name'      => 'location',
            'label'     => __('Location code (ex GB)'),
            'type'      => 'text',
            'required'  => true,
        );
        // custom
        $form['custom'] = array(
            'name'      => 'custom',
            'label'     => __('Custom attribute'),
            'type'      => 'text',
            'required'  => true,
        );
        // test_mode
        $form['test_mode'] = array(
            'name'      => 'test_mode',
            'label'     => __('Test mode by sandbox'),
            'type'      => 'checkbox',
            'required'  => false,
        );
        // Username
        $form['username'] = array(
            'name'      => 'username',
            'label'     => __('Username for sandbox'),
            'type'      => 'text',
            'required'  => false,
        );
        // password
        $form['password'] = array(
            'name'      => 'password',
            'label'     => __('Password for sandbox'),
            'type'      => 'text',
            'required'  => false,
        );
        // signature
        $form['signature'] = array(
            'name'      => 'signature',
            'label'     => __('Signature for sandbox'),
            'type'      => 'text',
            'required'  => false,
        );
        $this->gatewaySettingForm = $form;
        return $this;
    }

    public function setPayForm()
    {
        $form = array();
        // form cmd
        $form['cmd'] = array(
            'name'      => 'cmd',
            'type'      => 'hidden',
        );
        // form upload
        $form['upload'] = array(
            'name'      => 'upload',
            'type'      => 'hidden',
        );
        // form return
        $form['return'] = array(
            'name'      => 'return',
            'type'      => 'hidden',
        );
        // form cancel_return
        $form['cancel_return'] = array(
            'name'      => 'cancel_return',
            'type'      => 'hidden',
        );
        // form notify_url
        $form['notify_url'] = array(
            'name'      => 'notify_url',
            'type'      => 'hidden',
        );
        // form business
        $form['business'] = array(
            'name'      => 'business',
            'type'      => 'hidden',
        );
        // form currency_code
        $form['currency_code'] = array(
            'name'      => 'currency_code',
            'type'      => 'hidden',
        );
        // form invoice
        $form['invoice'] = array(
            'name'      => 'invoice',
            'type'      => 'hidden',
        );
        // form item_name_1
        $form['item_name_1'] = array(
            'name'      => 'item_name_1',
            'type'      => 'hidden',
        );
        // form item_number_1
        $form['item_number_1'] = array(
            'name'      => 'item_number_1',
            'type'      => 'hidden',
        );
        // form quantity_1
        $form['quantity_1'] = array(
            'name'      => 'quantity_1',
            'type'      => 'hidden',
        );
        // form amount_1
        $form['amount_1'] = array(
            'name'      => 'amount_1',
            'type'      => 'hidden',
        );
        // Set for test mode
        if ($this->gatewayOption['test_mode']) {
            // username
            $form['username'] = array(
                'name'      => 'username',
                'type'      => 'hidden',
            );
            // password
            $form['password'] = array(
                'name'      => 'password',
                'type'      => 'hidden',
            );
            // signature
            $form['signature'] = array(
                'name'      => 'signature',
                'type'      => 'hidden',
            );
        }
        /* 
        // first_name
        $form['first_name'] = array(
            'name'      => 'first_name',
            'type'      => 'hidden',
        );
        // last_name
        $form['last_name'] = array(
            'name'      => 'last_name',
            'type'      => 'hidden',
        );
        // address1
        $form['address1'] = array(
            'name'      => 'address1',
            'type'      => 'hidden',
        );
        // city
        $form['city'] = array(
            'name'      => 'city',
            'type'      => 'hidden',
        );
        // state
        $form['state'] = array(
            'name'      => 'state',
            'type'      => 'hidden',
        );
        // country
        $form['country'] = array(
            'name'      => 'country',
            'type'      => 'hidden',
        );
        // zip
        $form['zip'] = array(
            'name'      => 'zip',
            'type'      => 'hidden',
        );
        // email
        $form['email'] = array(
            'name'      => 'email',
            'type'      => 'hidden',
        );
        // form no_note
        $form['no_note'] = array(
            'name'      => 'no_note',
            'type'      => 'hidden',
        );
        // form bn
        $form['bn'] = array(
            'name'      => 'bn',
            'type'      => 'hidden',
        );
        // form tax
        $form['tax'] = array(
            'name'      => 'tax',
            'type'      => 'hidden',
        );
        // form rm
        $form['rm'] = array(
            'name'      => 'rm',
            'type'      => 'hidden',
        );
        // form handling_cart
        $form['handling_cart'] = array(
            'name'      => 'handling_cart',
            'type'      => 'hidden',
        );
        // form lc
        $form['lc'] = array(
            'name'      => 'lc',
            'type'      => 'hidden',
        );
        // form cbt
        $form['cbt'] = array(
            'name'      => 'cbt',
            'type'      => 'hidden',
        );
        // form custom
        $form['custom'] = array(
            'name'      => 'custom',
            'type'      => 'hidden',
        );
        // form charset
        $form['charset'] = array(
            'name'      => 'charset',
            'type'      => 'hidden',
        );
        // form shipping_1
        $form['shipping_1'] = array(
            'name'      => 'shipping_1',
            'type'      => 'hidden',
        );
        */
        $this->gatewayPayForm = $form;
        return $this;
    }

    public function getDialogUrl()
    {
        if ($this->gatewayOption['test_mode']) {
            return 'https://www.sandbox.paypal.com';
        } else {
            return 'https://www.paypal.com';
        }
    }

    public function getAuthority()
    {
        // Temporary solution for guide module
        if ($this->gatewayInvoice['module'] == 'guide') {
            $id = $this->gatewayInvoice['item'];
            $order = Pi::api('order', 'guide')->getOrder($id);
            $this->gatewayPayInformation['first_name'] = $order['customerInfo']['first_name'];
            $this->gatewayPayInformation['last_name'] = $order['customerInfo']['last_name'];
            $this->gatewayPayInformation['address1'] = $order['customerInfo']['address'];
            $this->gatewayPayInformation['city'] = $order['customerInfo'][''];
            $this->gatewayPayInformation['state'] = '';
            $this->gatewayPayInformation['country'] = $order['customerInfo']['country'];
            $this->gatewayPayInformation['zip'] = $order['customerInfo']['zip_code'];
            $this->gatewayPayInformation['email'] = $order['user']['email'];
        }

        $this->gatewayPayInformation['cmd'] = '_cart';
        $this->gatewayPayInformation['upload'] = 1;
        $this->gatewayPayInformation['return'] = $this->gatewayFinishUrl;
        $this->gatewayPayInformation['cancel_return'] = $this->gatewayCancelUrl;
        $this->gatewayPayInformation['notify_url'] = $this->gatewayNotifyUrl;
        $this->gatewayPayInformation['invoice'] = $this->gatewayInvoice['random_id'];
        $this->gatewayPayInformation['item_name_1'] = $this->gatewayInvoice['description']['title'];
        $this->gatewayPayInformation['item_number_1'] = $this->gatewayInvoice['description']['number'];
        $this->gatewayPayInformation['quantity_1'] = 1;
        $this->gatewayPayInformation['amount_1'] = $this->gatewayInvoice['amount'];
        $this->gatewayPayInformation['business'] = $this->gatewayOption['business'];
        $this->gatewayPayInformation['currency_code'] = $this->gatewayOption['currency'];
        $this->gatewayPayInformation['logoimg'] = Pi::service('asset')->logo();
        // Set for test mode
        if ($this->gatewayOption['test_mode']) {
            $this->gatewayPayInformation['username'] = $this->gatewayOption['username'];
            $this->gatewayPayInformation['password'] = $this->gatewayOption['password'];
            $this->gatewayPayInformation['signature'] = $this->gatewayOption['signature'];
        }
        /* 
        $this->gatewayPayInformation['first_name'] = '';
        $this->gatewayPayInformation['last_name'] = '';
        $this->gatewayPayInformation['address1'] = '';
        $this->gatewayPayInformation['city'] = '';
        $this->gatewayPayInformation['state'] = '';
        $this->gatewayPayInformation['country'] = '';
        $this->gatewayPayInformation['zip'] = '';
        $this->gatewayPayInformation['email'] = '';
        $this->gatewayPayInformation['no_note'] = 0;
        $this->gatewayPayInformation['bn'] = 'PP-BuyNowBF';
        $this->gatewayPayInformation['tax'] = 0;
        $this->gatewayPayInformation['rm'] = 1;
        $this->gatewayPayInformation['handling_cart'] = 0;
        $this->gatewayPayInformation['lc'] = $this->gatewayOption['location'];
        $this->gatewayPayInformation['cbt'] = __('Back to website');
        $this->gatewayPayInformation['custom'] = $this->gatewayOption['custom'];
        $this->gatewayPayInformation['charset'] = 'utf-8';
        */
    }

    public function setRedirectUrl()
    {
        $this->getAuthority();
        if ($this->gatewayOption['test_mode']) {
            $this->gatewayRedirectUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            $this->gatewayRedirectUrl = 'https://www.paypal.com/cgi-bin/webscr';
        }
    }
    
    /**
     * Verify Payment
     *
     * Some good example for verify
     * https://developer.paypal.com/docs/classic/ipn/ht_ipn/
     * https://stackoverflow.com/questions/4848227/validate-that-ipn-call-is-from-paypal
     * http://www.emanueleferonato.com/2011/09/28/using-php-with-paypals-ipn-instant-paypal-notification-to-automate-your-digital-delivery/
     * https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNIntro/
     *
     * Paypal verify method
     * Source : https://developer.paypal.com/docs/classic/ipn/ht_ipn/
    */
    public function verifyPayment($request, $processing)
    {
        // STEP 1: read POST data
        $req = 'cmd=_notify-validate';
        foreach ($request as $key => $value) {
            $req .= sprintf('&%s=%s', urldecode($key), urldecode($value));
        }
 
        // Step 2: POST IPN data back to PayPal to validate
        if ($this->gatewayOption['test_mode']) {
            $url_parsed = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            $url_parsed = 'https://www.paypal.com/cgi-bin/webscr';
        }
        
        // Check by curl
        $ch = curl_init($url_parsed);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
 
        if (!($res = curl_exec($ch)) ) {
            curl_close($ch);
            return false;
            exit;
        }
        curl_close($ch);

        // STEP 3: Inspect IPN validation result and act accordingly
        if (strcmp ($res, "VERIFIED") == 0) {
            $invoice = Pi::api('invoice', 'payment')->updateInvoice($request['invoice']);
            $result['status'] = 1;
            // Set log
            $log = array();
            $log['gateway'] = $this->gatewayAdapter;
            $log['authority'] = '';
            $log['value'] = Json::encode($request);
            $log['invoice'] = $invoice['id'];
            $log['amount'] = $invoice['amount'];
            $log['status'] = $result['status'];
            $log['message'] = __('Your payment were successfully.');
            Pi::api('log', 'payment')->setLog($log);
        } elseif (strcmp ($res, "INVALID") == 0) {
            $invoice = Pi::api('invoice', 'payment')->getInvoice($request['invoice']);
            $result['status'] = 0;
            $message = __('Error');
        }
        
        // Set result
        $result['adapter'] = $this->gatewayAdapter;
        $result['invoice'] = $invoice['id'];
        return $result;
    }

    public function setMessage($log)
    {
        $message = '';
        return $message;
    }

    public function setPaymentError($id = '')
    {
        // Set error
        $this->gatewayError = '';
    }
}