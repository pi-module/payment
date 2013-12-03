<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

namespace Module\Payment\Gateway\Paypal;

use Pi;
use Module\Payment\Gateway\AbstractGateway;
use Zend\Soap\Client;
/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
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
        // use_ipn
        $form['use_ipn'] = array(
                'name' => 'use_ipn',
                'type' => 'hidden',
            );
        // paypal_email
        $form['paypal_email'] = array(
                'name' => 'paypal_email',
                'label' => __('Email'),
                'type' => 'text',
            );
        // paypal_test
        $form['paypal_test'] = array(
                'name' => 'paypal_test',
                'label' => __('Test'),
                'type' => 'text',
            );
        // form paypal_money
        $form['paypal_money'] = array(
                'name' => 'paypal_money',
                'label' => __('Money'),
                'type' => 'text',
            );
        $this->gatewaySettingForm = $form;
        return $this;
    }

    public function setPayForm()
    {
        $form = array();
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
        if ($this->gatewayOption['paypal_test'] == 1) {
            return 'www.sandbox.paypal.com';
        } else {
            return 'www.paypal.com';
        }
    }

    public function setRedirectUrl()
    {
        if ($this->gatewayOption['paypal_test'] == 1) {
            return 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            return 'https://www.paypal.com/cgi-bin/webscr';
        }
    }

    public function getCheckout()
    {
        $parameters = array();
        $parameters['cmd'] = '_xclick';
        $parameters['upload'] = '1';
        $parameters['currency_code'] = $this->gatewayOption['paypal_money'];
        $parameters['business'] = $this->gatewayOption['paypal_email'];
        $parameters['return'] = $this->gatewayBackUrl;
        $parameters['image_url'] = '';
        $parameters['cpp_header_image'] = '';
        $parameters['invoice'] = $this->gatewayInvoice['id'];
        $parameters['item_name'] = 'Test';
        $parameters['item_number'] = $this->gatewayInvoice['id'];
        $parameters['tax'] = 0;
        $parameters['amount'] = $this->gatewayInvoice['amount'];
        $parameters['custom'] = $this->gatewayInvoice['id'];
        $parameters['email'] = '';
        return $parameters;
    }

    public function verifyPayment($value)
    {
        $result = array();
        return $result;
    }
}