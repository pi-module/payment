<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

namespace Module\Payment\Gateway\Mellat;

use Pi;
use Module\Payment\Gateway\AbstractGateway;

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
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
        $gateway['description'] = __('Test test test test test test');
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
                'type' => 'hidden',
            );
        // form pin
        $form['pin'] = array(
                'name' => 'pin',
                'label' => __('Pin'),
                'type' => 'text',
            );
        // form username
        $form['username'] = array(
                'name' => 'username',
                'label' => __('Username'),
                'type' => 'text',
            );
        // form password
        $form['password'] = array(
                'name' => 'password',
                'label' => __('Password'),
                'type' => 'text',
            );
        // form password
        $form['additionalData'] = array(
                'name' => 'additionalData',
                'label' => __('Additional Data'),
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

    public function getFormatAmount($amount)
    {
        $amount = number_format($amount, 2, '.', '');
        $amount = intval($amount);
        return $amount;
    }

    public function getDialogUrl()
    {
        return 'https://pgwsf.bpm.bankmellat.ir/pgwchannel/services/pgw?wsdl';
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
        $parameters['orderId'] = $this->gatewayInvoice['id'];
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
        }
    }

    public function setRedirectUrl()
    {
        $this->getAuthority();
        $this->gatewayRedirectUrl = 'https://pgw.bpm.bankmellat.ir/pgwchannel/startpay.mellat';
    }

    public function verifyPayment($value)
    {
        // Set parameters
        $parameters = array();
        $parameters['terminalId'] = $this->gatewayOption['pin'];
        $parameters['userName'] = $this->gatewayOption['username'];
        $parameters['userPassword'] = $this->gatewayOption['password'];
        $parameters['orderId'] = $value['SaleOrderId'];
        $parameters['saleOrderId'] = $value['SaleOrderId'];
        $parameters['saleReferenceId'] = $value['SaleReferenceId'];
        // Check bank
        $call = $this->call('bpVerifyRequest', $parameters);
        // set log
        $log = array();
        $log['gateway'] = $this->gatewayAdapter;
        $log['invoice'] = $value['SaleOrderId'];
        $log['authority'] = $value['RefId'];
        $log['value'] = json_encode($value);
        // Set result
        $result = array();
        if ($call == 0) {
            $result['status'] = 1;
        } else {
            $result['status'] = 0;
        }
        $result['adapter'] = $this->gatewayAdapter;
        $result['invoice'] = $value['SaleOrderId'];
        $result['log'] = $log;
        return $result;
    }

    public function finishPayment($value)
    {
        // update invoice
        $invoice = Pi::api('payment', 'invoice')->updateInvoice($value['invoice']);
        // Set log
        $log = $value['log'];
        $log['amount'] = $invoice['amount'];
        $log['status'] = $invoice['status'];
        Pi::api('payment', 'log')->setLot($log);
        // Set result
        $result = array();
        $result['status'] = 1;
        return $result;
    }

    public function call($api, $parameters)
    {
        // Set nusoap client
        require_once Pi::path('vendor') . '/nusoap/nusoap.php';
        // Set client
        $client = new \nusoap_client($this->getDialogUrl());
        return $client->call($api, $parameters, $this->getNamespaceUrl());
    }
}