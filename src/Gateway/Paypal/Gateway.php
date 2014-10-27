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
            'name'   => 'path',
            'label'  => __('path'),
            'type'   => 'hidden',
        );
        // business
        $form['business'] = array(
            'name'   => 'business',
            'label'  => __('Paypal email address'),
            'type'   => 'text',
        );
        // currency
        $form['currency'] = array(
            'name'   => 'currency',
            'label'  => __('Paypal currency'),
            'type'   => 'text',
        );
        // cursymbol
        $form['cursymbol'] = array(
            'name'   => 'cursymbol',
            'label'  => __('Paypal currency symbol'),
            'type'   => 'text',
        );
        // location
        $form['location'] = array(
            'name'   => 'location',
            'label'  => __('Location code (ex GB)'),
            'type'   => 'text',
        );
        // custom
        $form['custom'] = array(
            'name'   => 'custom',
            'label'  => __('Custom attribute'),
            'type'   => 'text',
        );
        // test_mode
        $form['test_mode'] = array(
            'name'   => 'test_mode',
            'label'  => __('Test mode'),
            'type'   => 'checkbox',
        );
        $this->gatewaySettingForm = $form;
        return $this;
    }

    public function setPayForm()
    {
        $form = array();
        // form cmd
        $form['cmd'] = array(
            'name'   => 'cmd',
            'type'   => 'hidden',
        );
        // form upload
        $form['upload'] = array(
            'name'   => 'upload',
            'type'   => 'hidden',
        );
        // form return
        $form['return'] = array(
            'name'   => 'return',
            'type'   => 'hidden',
        );
        // form cancel_return
        $form['cancel_return'] = array(
            'name'   => 'cancel_return',
            'type'   => 'hidden',
        );
        // form notify_url
        $form['notify_url'] = array(
            'name'   => 'notify_url',
            'type'   => 'hidden',
        );
        // form business
        $form['business'] = array(
            'name'   => 'business',
            'type'   => 'hidden',
        );
        // form currency_code
        $form['currency_code'] = array(
            'name'   => 'currency_code',
            'type'   => 'hidden',
        );
        // form invoice
        $form['invoice'] = array(
            'name'   => 'invoice',
            'type'   => 'hidden',
        );
        // form item_name_1
        $form['item_name_1'] = array(
            'name'   => 'item_name_1',
            'type'   => 'hidden',
        );
        // form item_number_1
        $form['item_number_1'] = array(
            'name'   => 'item_number_1',
            'type'   => 'hidden',
        );
        // form quantity_1
        $form['quantity_1'] = array(
            'name'   => 'quantity_1',
            'type'   => 'hidden',
        );
        // form amount_1
        $form['amount_1'] = array(
            'name'   => 'amount_1',
            'type'   => 'hidden',
        );
        // first_name
        $form['first_name'] = array(
            'name'   => 'first_name',
            'type'   => 'hidden',
        );
        // last_name
        $form['last_name'] = array(
            'name'   => 'last_name',
            'type'   => 'hidden',
        );
        // address1
        $form['address1'] = array(
            'name'   => 'address1',
            'type'   => 'hidden',
        );
        // city
        $form['city'] = array(
            'name'   => 'city',
            'type'   => 'hidden',
        );
        // state
        $form['state'] = array(
            'name'   => 'state',
            'type'   => 'hidden',
        );
        // country
        $form['country'] = array(
            'name'   => 'country',
            'type'   => 'hidden',
        );
        // zip
        $form['zip'] = array(
            'name'   => 'zip',
            'type'   => 'hidden',
        );
        // email
        $form['email'] = array(
            'name'   => 'email',
            'type'   => 'hidden',
        );
        /*
        // form no_note
        $form['no_note'] = array(
            'name'   => 'no_note',
            'type'   => 'hidden',
        );
        // form bn
        $form['bn'] = array(
            'name'   => 'bn',
            'type'   => 'hidden',
        );
        // form tax
        $form['tax'] = array(
            'name'   => 'tax',
            'type'   => 'hidden',
        );
        // form rm
        $form['rm'] = array(
            'name'   => 'rm',
            'type'   => 'hidden',
        );
        // form handling_cart
        $form['handling_cart'] = array(
            'name'   => 'handling_cart',
            'type'   => 'hidden',
        );
        // form lc
        $form['lc'] = array(
            'name'   => 'lc',
            'type'   => 'hidden',
        );
        // form cbt
        $form['cbt'] = array(
            'name'   => 'cbt',
            'type'   => 'hidden',
        );
        // form custom
        $form['custom'] = array(
            'name'   => 'custom',
            'type'   => 'hidden',
        );
        // form charset
        $form['charset'] = array(
            'name'   => 'charset',
            'type'   => 'hidden',
        );
        // form shipping_1
        $form['shipping_1'] = array(
            'name'   => 'shipping_1',
            'type'   => 'hidden',
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
        $this->gatewayPayInformation['cmd'] = '_cart';
        $this->gatewayPayInformation['upload'] = 1;
        $this->gatewayPayInformation['return'] = $this->gatewayFinishUrl;
        $this->gatewayPayInformation['cancel_return'] = $this->gatewayCancelUrl;
        $this->gatewayPayInformation['notify_url'] = $this->gatewayNotifyUrl;
        $this->gatewayPayInformation['invoice'] = intval($this->gatewayInvoice['random_id']);
        $this->gatewayPayInformation['item_name_1'] = 'Test name';
        $this->gatewayPayInformation['item_number_1'] = 1;
        $this->gatewayPayInformation['quantity_1'] = 1;
        $this->gatewayPayInformation['amount_1'] = intval($this->gatewayInvoice['amount']);
        $this->gatewayPayInformation['business'] = $this->gatewayOption['business'];
        $this->gatewayPayInformation['currency_code'] = $this->gatewayOption['currency'];
        $this->gatewayPayInformation['first_name'] = '';
        $this->gatewayPayInformation['last_name'] = '';
        $this->gatewayPayInformation['address1'] = '';
        $this->gatewayPayInformation['city'] = '';
        $this->gatewayPayInformation['state'] = '';
        $this->gatewayPayInformation['country'] = '';
        $this->gatewayPayInformation['zip'] = '';
        $this->gatewayPayInformation['email'] = '';
        /* 
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
    
    public function verifyPayment($request, $processing)
    {
        // Some good example for verify
        // https://developer.paypal.com/docs/classic/ipn/ht_ipn/
        // https://stackoverflow.com/questions/4848227/validate-that-ipn-call-is-from-paypal
        // http://www.emanueleferonato.com/2011/09/28/using-php-with-paypals-ipn-instant-paypal-notification-to-automate-your-digital-delivery/
        // https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNIntro/
        // Source : https://developer.paypal.com/docs/classic/ipn/ht_ipn/

        // STEP 1: read POST data

        // Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.
        // Instead, read raw POST data from the input stream.
        $myPost = array();
        foreach ($request as $keyval) {
            $keyval = explode ('=', $keyval);
            if (count($keyval) == 2) {
                $myPost[$keyval[0]] = urldecode($keyval[1]);
            }
            
        }
        
        // read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
        $req = 'cmd=_notify-validate';
        if(function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }

        foreach ($myPost as $key => $value) {
            if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }
 
        // Step 2: POST IPN data back to PayPal to validate
        if ($this->gatewayOption['test_mode']) {
            $url_parsed = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            $url_parsed = 'https://www.paypal.com/cgi-bin/webscr';
        }

        $ch = curl_init($url_parsed);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
 
        // In wamp-like environments that do not come bundled with root authority certificates,
        // please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set
        // the directory path of the certificate as shown below:
        // curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
        if (!($res = curl_exec($ch)) ) {
            // error_log("Got " . curl_error($ch) . " when processing IPN data");
            curl_close($ch);
            exit;
        }
        curl_close($ch);

        // STEP 3: Inspect IPN validation result and act accordingly
        if (strcmp ($res, "VERIFIED") == 0) {
            // The IPN is verified, process it:
            // check whether the payment_status is Completed
            // check that txn_id has not been previously processed
            // check that receiver_email is your Primary PayPal email
            // check that payment_amount/payment_currency are correct
            // process the notification
 
            // assign posted variables to local variables
            //$item_name = $_POST['item_name'];
            //$item_number = $_POST['item_number'];
            //$payment_status = $_POST['payment_status'];
            //$payment_amount = $_POST['mc_gross'];
            //$payment_currency = $_POST['mc_currency'];
            //$txn_id = $_POST['txn_id'];
            //$receiver_email = $_POST['receiver_email'];
            //$payer_email = $_POST['payer_email'];
 
            // IPN message values depend upon the type of notification sent.
            // To loop through the &_POST array and print the NV pairs to the screen:
            //foreach($_POST as $key => $value) {
                //echo $key." = ". $value."<br>";
            //}

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

        } elseif (strcmp ($res, "INVALID") == 0) {
            // IPN invalid, log for manual investigation
            // echo "The response from IPN was: <b>" .$res ."</b>";

            $invoice = Pi::api('invoice', 'payment')->getInvoice($processing['invoice']);
            $result['status'] = 0;
            $message = __('Error');
        }
        // Set result
        $result['adapter'] = $this->gatewayAdapter;
        $result['invoice'] = $invoice['id'];
        return $result;
        
        /* 
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
        return $result; */

        // generate the post string from the _POST vars aswell as load the
        // _POST vars into an arry so we can play with them from the calling
        // script.
        /* $post_string = '';    
        foreach ($request as $field => $value) { 
            //$ipn_data[$field] = $value;
            $post_string .= $field.'='.urlencode(stripslashes($value)).'&'; 
        }
        $post_string.="cmd=_notify-validate"; // append ipn command
        
        // open the connection to paypal
        if (isset($request['test_ipn']) ) {
            $fp = fsockopen ( 'ssl://www.sandbox.paypal.com', "443", $err_num, $err_str, 60 );
        } else {
            $fp = fsockopen ( 'ssl://www.paypal.com', "443", $err_num, $err_str, 60 );
        }
        if(!$fp) {
            return false;
        } else { 
            // Post the data back to paypal
            fputs($fp, "POST $url_parsed[path] HTTP/1.1\r\n"); 
            fputs($fp, "Host: $url_parsed[host]\r\n"); 
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
            fputs($fp, "Content-length: ".strlen($post_string)."\r\n"); 
            fputs($fp, "Connection: close\r\n\r\n"); 
            fputs($fp, $post_string . "\r\n\r\n"); 
            $ipn_response = '';
            while(!feof($fp)) { 
                $ipn_response .= fgets($fp, 1024); 
            } 
            fclose($fp); // close connection
        }
        if (! eregi("VERIFIED",$ipn_response)) {
            return false;
        } else {
            $invoice = Pi::api('invoice', 'payment')->updateInvoice($request['invoice']);
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
            return true;
        } */
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