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
                'name' => 'path',
                'label' => __('path'),
                'type' => 'hidden',
            );
        // business
        $form['business'] = array(
                'name' => 'business',
                'label' => __('Paypal email address'),
                'type' => 'text',
            );
        // currency
        $form['currency'] = array(
                'name' => 'currency',
                'label' => __('Paypal currency'),
                'type' => 'text',
            );
        // cursymbol
        $form['cursymbol'] = array(
                'name' => 'cursymbol',
                'label' => __('Paypal currency symbol'),
                'type' => 'text',
            );
        // location
        $form['location'] = array(
                'name' => 'location',
                'label' => __('Location code (ex GB)'),
                'type' => 'text',
            );
        // custom
        $form['custom'] = array(
                'name' => 'custom',
                'label' => __('Custom attribute'),
                'type' => 'text',
            );
        $this->gatewaySettingForm = $form;
        return $this;
    }

    public function setPayForm()
    {
        $form = array();
        // form cmd
        $form['cmd'] = array(
                'name' => 'cmd',
                'type' => 'hidden',
            );
        // form upload
        $form['upload'] = array(
                'name' => 'upload',
                'type' => 'hidden',
            );
        // form no_note
        $form['no_note'] = array(
                'name' => 'no_note',
                'type' => 'hidden',
            );
        // form bn
        $form['bn'] = array(
                'name' => 'bn',
                'type' => 'hidden',
            );
        // form tax
        $form['tax'] = array(
                'name' => 'tax',
                'type' => 'hidden',
            );
        // form rm
        $form['rm'] = array(
                'name' => 'rm',
                'type' => 'hidden',
            );
        // form business
        $form['business'] = array(
                'name' => 'business',
                'type' => 'hidden',
            );
        // form handling_cart
        $form['handling_cart'] = array(
                'name' => 'handling_cart',
                'type' => 'hidden',
            );
        // form currency_code
        $form['currency_code'] = array(
                'name' => 'currency_code',
                'type' => 'hidden',
            );
        // form lc
        $form['lc'] = array(
                'name' => 'lc',
                'type' => 'hidden',
            );
        // form return
        $form['return'] = array(
                'name' => 'return',
                'type' => 'hidden',
            );
        // form cbt
        $form['cbt'] = array(
                'name' => 'cbt',
                'type' => 'hidden',
            );
        // form cancel_return
        $form['cancel_return'] = array(
                'name' => 'cancel_return',
                'type' => 'hidden',
            );
        // form custom
        $form['custom'] = array(
                'name' => 'custom',
                'type' => 'hidden',
            );
        // form amount
        $form['amount'] = array(
                'name' => 'amount',
                'type' => 'hidden',
            );
        $this->gatewayPayForm = $form;
        return $this;
    }

    public function getDialogUrl()
    {
        return 'https://www.paypal.com';
    }

    public function getAuthority()
    {
        $this->gatewayPayInformation['cmd'] = '_cart';
        $this->gatewayPayInformation['upload'] = 1;
        $this->gatewayPayInformation['no_note'] = 0;
        $this->gatewayPayInformation['bn'] = 'PP-BuyNowBF';
        $this->gatewayPayInformation['tax'] = 0;
        $this->gatewayPayInformation['rm'] = 1;

        $this->gatewayPayInformation['business'] = $this->gatewayOption['business'];
        $this->gatewayPayInformation['handling_cart'] = 0;
        $this->gatewayPayInformation['currency_code'] = $this->gatewayOption['currency'];
        $this->gatewayPayInformation['lc'] = $this->gatewayOption['location'];
        $this->gatewayPayInformation['return'] = $this->gatewayBackUrl;
        $this->gatewayPayInformation['cbt'] = __('Back to website');
        $this->gatewayPayInformation['cancel_return'] = $this->gatewayCancelUrl;
        $this->gatewayPayInformation['custom'] = $this->gatewayOption['custom'];

        $this->gatewayPayInformation['amount_1'] = intval($this->gatewayInvoice['amount']);
    }

    public function setRedirectUrl()
    {
        $this->getAuthority();
        $this->gatewayRedirectUrl = 'https://www.paypal.com/cgi-bin/webscr';
    }
    
    public function verifyPayment($request, $processing)
    {
        // Some good example for verify
        // https://developer.paypal.com/docs/classic/ipn/ht_ipn/
        // https://stackoverflow.com/questions/4848227/validate-that-ipn-call-is-from-paypal
        // http://www.emanueleferonato.com/2011/09/28/using-php-with-paypals-ipn-instant-paypal-notification-to-automate-your-digital-delivery/
        // https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNIntro/

        // Get user id
        $user = $processing['uid'];
        // Get unique transaction id.
        if ($request['tx']) {
            $tx = $request['tx'];
        }

        // Init
        cURL $ch = curl_init();

        // Set request options
        curl_setopt_array($ch, array(
            CURLOPT_URL             => 'https://www.sandbox.paypal.com/cgi-bin/webscr',
            CURLOPT_POST            => TRUE,
            CURLOPT_POSTFIELDS      => http_build_query(array(
                'cmd'  => '_notify-synch',
                'tx'   => $tx,
                'at'   => $identity,
            )),
            CURLOPT_RETURNTRANSFER  => TRUE,
            CURLOPT_HEADER          => FALSE,
            // CURLOPT_SSL_VERIFYPEER  => TRUE,
            // CURLOPT_CAINFO          => 'cacert.pem',
        ));

        // Execute request and get response and status code
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // Close connection
        curl_close($ch);

        // Check
        if($status == 200 AND strpos($response, 'SUCCESS') === 0) {
            $invoice = Pi::api('invoice', 'payment')->updateInvoice($processing['invoice']);
            $result['status'] = 1;
            $message = __('Your payment were successfully.');
            // Set log
            $log = array();
            $log['gateway'] = $this->gatewayAdapter;
            $log['authority'] = $request['RefId'];
            $log['value'] = Json::encode($request);
            $log['invoice'] = $invoice['id'];
            $log['amount'] = $invoice['amount'];
            $log['status'] = $result['status'];
            $log['message'] = $message;
            Pi::api('log', 'payment')->setLog($log);
        } else {
            $invoice = Pi::api('invoice', 'payment')->getInvoice($processing['invoice']);
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