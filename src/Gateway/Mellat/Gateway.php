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

namespace Module\Payment\Gateway\Mellat;

use Pi;
use Module\Payment\Gateway\AbstractGateway;
use Zend\Json\Json;

class Gateway extends AbstractGateway
{
    public function setAdapter()
    {
        $this->gatewayAdapter = 'Mellat';
    }

    public function setInformation()
    {
        $gateway = array();
        $gateway['title'] = __('Bank Mellat (Iran)');
        $gateway['path'] = 'Mellat';
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
        // form pin
        $form['pin'] = array(
                'name' => 'pin',
                'label' => __('pin'),
                'type' => 'text',
            );
        // form username
        $form['username'] = array(
                'name' => 'username',
                'label' => __('username'),
                'type' => 'text',
            );
        // form password
        $form['password'] = array(
                'name' => 'password',
                'label' => __('password'),
                'type' => 'text',
            );
        $this->gatewaySettingForm = $form;
        return $this;
    }

    public function setPayForm()
    {
        $form = array();
        // form RefId
        $form['RefId'] = array(
                'name' => 'RefId',
                'type' => 'hidden',
            );
        $this->gatewayPayForm = $form;
        return $this;
    }

    public function getDialogUrl()
    {
        return 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl';
    }

    public function getNamespaceUrl()
    {
        return 'http://interfaces.core.sw.bps.com/';
    }

    public function getAuthority()
    {
        $parameters = array();
        $parameters['terminalId'] = $this->gatewayOption['pin'];
        $parameters['userName'] = $this->gatewayOption['username'];
        $parameters['userPassword'] = $this->gatewayOption['password'];
        $parameters['orderId'] = $this->gatewayInvoice['random_id'];
        $parameters['amount'] = intval($this->gatewayInvoice['amount']);
        $parameters['localDate'] = date('Ymd'); 
        $parameters['localTime'] = date('His');
        $parameters['additionalData'] = $this->gatewayOption['additionalData'];
        $parameters['callBackUrl'] = $this->gatewayBackUrl;
        $parameters['payerId'] = 0;
        // Check bank
        $result = $this->call('bpPayRequest', $parameters);
        $result = explode (',', $result);
        if ($result[0] == 0) {
            $this->gatewayPayInformation['RefId'] = $result[1];
        } else {
            $this->setPaymentError($result[0]);
            // set log
            $log = array();
            $log['gateway'] = $this->gatewayAdapter;
            $log['authority'] = $result[0];
            $log['value'] = Json::encode($this->gatewayInvoice);
            $log['invoice'] = $this->gatewayInvoice['id'];
            $log['amount'] = intval($this->gatewayInvoice['amount']);
            $log['status'] = 0;
            $log['message'] = $this->gatewayError;
            Pi::api('log', 'payment')->setLot($log);
        }
    }

    public function setRedirectUrl()
    {
        $this->getAuthority();
        $this->gatewayRedirectUrl = 'https://bpm.shaparak.ir/pgwchannel/startpay.mellat';
    }

    public function verifyPayment($value, $processing)
    {
        // Set result
        $result = array();
        // Set parameters
        $parameters = array();
        $parameters['terminalId'] = $this->gatewayOption['pin'];
        $parameters['userName'] = $this->gatewayOption['username'];
        $parameters['userPassword'] = $this->gatewayOption['password'];
        $parameters['orderId'] = $value['SaleOrderId'];
        $parameters['saleOrderId'] = $value['SaleOrderId'];
        $parameters['saleReferenceId'] = $value['SaleReferenceId'];
        // Check 
        if ($processing['random_id'] == $value['SaleOrderId']) {
            // Check bank
            $call = $this->call('bpVerifyRequest', $parameters);
            if ($call == 0) {
                $invoice = Pi::api('invoice', 'payment')->updateInvoice($value['SaleOrderId']);
                $result['status'] = 1;
                $message = __('Your payment were successfully.');;
            } else {
                $this->setPaymentError($call);
                $invoice = Pi::api('invoice', 'payment')->getInvoice($value['SaleOrderId']);
                $result['status'] = 0;
                $message = $this->gatewayError;
            }
        } else {
            $invoice = Pi::api('invoice', 'payment')->getInvoice($value['SaleOrderId']);
            $result['status'] = 0;
            $message = __('Your order id not true.');
        }
        // Set log
        $log = array();
        $log['gateway'] = $this->gatewayAdapter;
        $log['authority'] = $value['RefId'];
        $log['value'] = Json::encode($value);
        $log['invoice'] = $invoice['id'];
        $log['amount'] = $invoice['amount'];
        $log['status'] = $result['status'];
        $log['message'] = $message;
        Pi::api('log', 'payment')->setLot($log);
        // Set result
        $result['adapter'] = $this->gatewayAdapter;
        $result['invoice'] = $invoice['id'];
        return $result;
    }

    public function call($api, $parameters)
    {
        // Set nusoap client
        require_once Pi::path('module') . '/payment/src/Gateway/Mellat/nusoap.php';
        // Set client
        $client = new \nusoap_client($this->getDialogUrl());
        return $client->call($api, $parameters, $this->getNamespaceUrl());
    }

    public function setPaymentError($id = '')
    {
        switch ($id) {
            case '':
                $error = __('Bank Mellat error 0');
                break;
                
            case '41':
                $error = __('Bank Mellat error 41');
                break;
                
            case '43':
                $error = __('Bank Mellat error 43');
                break;
                
            case '17':
                $error = __('Bank Mellat error 17');
                break;
                
            case '415':
                $error = __('Bank Mellat error 415');
                break;
                
            case '417':
                $error = __('Bank Mellat error 417');
                break;
                
            case '11':
                $error = __('Bank Mellat error 11');
                break;
                
            case '12':
                $error = __('Bank Mellat error 12');
                break;
                
            case '13':
                $error = __('Bank Mellat error 13');
                break;
                
            case '14':
                $error = __('Bank Mellat error 14');
                break;
                
            case '15':
                $error = __('Bank Mellat error 15');
                break;
                
            case '16':
                $error = __('Bank Mellat error 16');
                break;
                
            case '18':
                $error = __('Bank Mellat error 18');
                break;
                
            case '19':
                $error = __('Bank Mellat error 19');
                break;
                
            case '111':
                $error = __('Bank Mellat error 111');
                break;
                
            case '112':
                $error = __('Bank Mellat error 112');
                break;
                
            case '113':
                $error = __('Bank Mellat error 113');
                break;
                
            case '114':
                $error = __('Bank Mellat error 114');
                break;
                
            case '21':
                $error = __('Bank Mellat error 21');
                break;
                
            case '23':
                $error = __('Bank Mellat error 23');
                break;
                
            case '24':
                $error = __('Bank Mellat error 24');
                break;
                
            case '25':
                $error = __('Bank Mellat error 25');
                break;
                
            case '31':
                $error = __('Bank Mellat error 31');
                break;
                
            case '32':
                $error = __('Bank Mellat error 32');
                break;
                
            case '33':
                $error = __('Bank Mellat error 33');
                break;
                
            case '34':
                $error = __('Bank Mellat error 34');
                break;
                
            case '35':
                $error = __('Bank Mellat error 35');
                break;
                
            case '42':
                $error = __('Bank Mellat error 42');
                break;
                
            case '44':
                $error = __('Bank Mellat error 44');
                break;
                
            case '45':
                $error = __('Bank Mellat error 45');
                break;
                
            case '46':
                $error = __('Bank Mellat error 46');
                break;
                
            case '47':
                $error = __('Bank Mellat error 47');
                break;
                
            case '48':
                $error = __('Bank Mellat error 48');
                break;
                
            case '49':
                $error = __('Bank Mellat error 49');
                break;
                
            case '412':
                $error = __('Bank Mellat error 412');
                break;
                
            case '413':
                $error = __('Bank Mellat error 413');
                break;
                
            case '414':
                $error = __('Bank Mellat error 414');
                break;
                
            case '416':
                $error = __('Bank Mellat error 416');
                break;
                
            case '418':
                $error = __('Bank Mellat error 418');
                break;
                
            case '419':
                $error = __('Bank Mellat error 419');
                break;
                
            case '421':
                $error = __('Bank Mellat error 421');
                break;
                
            case '51':
                $error = __('Bank Mellat error 51');
                break;
                
            case '54':
                $error = __('Bank Mellat error 54');
                break;
                
            case '55':
                $error = __('Bank Mellat error 55');
                break;
                
            case '61':
                $error = __('Bank Mellat error 61');
                break;

            default:
                $error = sprintf(__('Bank Mellat error %s'), $id); 
                break;
        }
        // Set error
        $this->gatewayError = $error;
    }
}