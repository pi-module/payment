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
        // form additional Data
        /* $form['additionalData'] = array(
                'name' => 'additionalData',
                'label' => __('Additional Data'),
                'type' => 'text',
            ); */
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
        }
    }

    public function setRedirectUrl()
    {
        $this->getAuthority();
        $this->gatewayRedirectUrl = 'https://pgw.bpm.bankmellat.ir/pgwchannel/startpay.mellat';
    }

    public function verifyPayment($value, $processing)
    {
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
            // Set result
            $result = array();
            if ($call == 0) {
                $result['status'] = 1;
                // update invoice
                $invoice = Pi::api('invoice', 'payment')->updateInvoice($value['SaleOrderId']);
                // set log
                $log = array();
                $log['gateway'] = $this->gatewayAdapter;
                $log['authority'] = $value['RefId'];
                $log['value'] = Json::encode($value);
                $log['invoice'] = $invoice['id'];
                $log['amount'] = $invoice['amount'];
                $log['status'] = $invoice['status'];
                Pi::api('log', 'payment')->setLot($log);
            } else {
                $this->setPaymentError($call);
                $result['status'] = 0;
            }
        } else {
            $result['status'] = 0;
        }
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
                $error = 'سرور بانک دچار مشکل می باشد.';
                break;
                
            case '41':
                $error = 'شماره درخواست تکراری است.';
                break;
                
            case '43':
                $error = 'عملیات قبلا انجام شده است.';
                break;
                
            case '17':
                $error = 'لغو عملیات پرداخت توسط کاربر صورت گرفته است.';
                break;
                
            case '415':
                $error = 'زمان شما برای انجام عملیات پرداخت به پایان رسیده است.';
                break;
                
            case '417':
                $error = 'شناسه پرداخت کننده نامعتبر است.';
                break;
                
            case '11':
                $error = 'شماره كارت نامعتبر است.';
                break;
                
            case '12':
                $error = 'موجودي كافي نيست.';
                break;
                
            case '13':
                $error = 'رمز نادرست است.';
                break;
                
            case '14':
                $error = 'تعداد دفعات وارد كردن رمز بيش از حد مجاز است.';
                break;
                
            case '15':
                $error = 'كارت نامعتبر است.';
                break;
                
            case '16':
                $error = 'دفعات برداشت وجه بيش از حد مجاز است.';
                break;
                
            case '18':
                $error = 'تاريخ انقضاي كارت گذشته است.';
                break;
                
            case '19':
                $error = 'مبلغ برداشت وجه بيش از حد مجاز است.';
                break;
                
            case '111':
                $error = 'صادر كننده كارت نامعتبر است.';
                break;
                
            case '112':
                $error = 'خطاي سوييچ صادر كننده كارت.';
                break;
                
            case '113':
                $error = 'پاسخي از صادر كننده كارت دريافت نشد.';
                break;
                
            case '114':
                $error = 'دارنده كارت مجاز به انجام اين تراكنش نيست.';
                break;
                
            case '21':
                $error = 'پذيرنده نامعتبر است.';
                break;
                
            case '23':
                $error = 'خطاي امنيتي رخ داده است.';
                break;
                
            case '24':
                $error = 'اطلاعات كاربري پذيرنده نامعتبر است.';
                break;
                
            case '25':
                $error = 'مبلغ نامعتبر است.';
                break;
                
            case '31':
                $error = 'پاسخ نامعتبر است.';
                break;
                
            case '32':
                $error = 'فرمت اطلاعات وارد شده صحيح نمي باشد.';
                break;
                
            case '33':
                $error = 'حساب نامعتبر است.';
                break;
                
            case '34':
                $error = 'خطاي سيستمي.';
                break;
                
            case '35':
                $error = 'تاريخ نامعتبر است.';
                break;
                
            case '42':
                $error = 'خریدی با این شماره درخواست یافت نشد.';
                break;
                
            case '44':
                $error = 'کسر پول از حساب مشتری صورت نگرفته است.';
                break;
                
            case '45':
                $error = 'واریز پول قبلا انجام شده است.';
                break;
                
            case '46':
                $error = 'واریز پول به حساب پذیرنده انجام نشده است.';
                break;
                
            case '47':
                $error = 'واریز پول به حساب پذیرنده انجام نشده است.';
                break;
                
            case '48':
                $error = 'پول مشتری به حساب او بازگشت داده شده است.';
                break;
                
            case '49':
                $error = 'تراکنش استرداد وجه دلخواه یافت نشد.';
                break;
                
            case '412':
                $error = 'شناسه قبض نادرست است.';
                break;
                
            case '413':
                $error = 'شناسه پرداخت نادرست است.';
                break;
                
            case '414':
                $error = 'سازمان صادر كننده قبض نامعتبر است.';
                break;
                
            case '416':
                $error = 'در ثبت اطلاعات پرداخت شما در بانک ملت خطایی رخ داده است.';
                break;
                
            case '418':
                $error = 'در تعریف اطلاعات شما نزد بانک ملت خطایی پدید آمده است.';
                break;
                
            case '419':
                $error = 'تعداد دفعات ورود اطلاعات از حد مجاز گذشته است.';
                break;
                
            case '421':
                $error = 'IP نامعتبر است.';
                break;
                
            case '51':
                $error = 'تراکنش تکراری است.';
                break;
                
            case '54':
                $error = 'تراکنش مرجع موجود نیست.';
                break;
                
            case '55':
                $error = 'تراکنش نامعتبر است.';
                break;
                
            case '61':
                $error = 'خطا در واریز وجه.';
                break;

            default:
                $error = sprintf('شماره خطای اعلام شده توسط بانک: %s', $id); 
                break;
        }
        // Set error
        $this->gatewayError = $error;
    }
}