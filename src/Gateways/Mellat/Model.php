<?php

namespace App\Modules\Payments\Mellat;

use App\Modules\Payments\iModel;
use SoapClient;
/**
 * Description of samanModel
 *
 * @author ramin ashrafimanesh <ashrafimanesh@gmail.com>
 */

class Model implements iModel {

    private $apikey = '***', $callbackurl = 'http://gateway domain';
    private $userName = 'esharjh', $terminalId = '670831', $userPassword = '50437', $orderId;

    public function confirm($inputs = array()) {
        $response=['status'=>true,'message'=>''];
	$parameters = array(
		'terminalId' => $this->terminalId,
                'userName' => $this->userName,
                'userPassword' => $this->userPassword,
		'orderId' => $inputs['orderID'],
		'saleOrderId' => $inputs['SaleOrderId'],
		'saleReferenceId' => $inputs['SaleReferenceId']);
        
        $client = \App\Libs\NusoapClient::nusoap('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
	// Call the SOAP method
        $namespace = 'http://interfaces.core.sw.bps.com/';
	$result = $client->call('bpVerifyRequest', $parameters, $namespace);
	if($result == 0) {
		//-- وریفای به درستی انجام شد٬ درخواست واریز وجه
		// Call the SOAP method
		$result = $client->call('bpSettleRequest', $parameters, $namespace);
		if($result == 0) {
			//-- تمام مراحل پرداخت به درستی انجام شد.
			//-- آماده کردن خروجی
                        $response['status'] = true;
                        $response['message'] = 'The transaction was successful';
		} else {
			//-- در درخواست واریز وجه مشکل به وجود آمد. درخواست بازگشت وجه داده شود.
			$client->call('bpReversalRequest', $parameters, $namespace);			
                        $response['status'] = false;
                        $response['message'] = -1*$result;
		}
	} else {
		//-- وریفای به مشکل خورد٬ نمایش پیغام خطا و بازگشت زدن مبلغ
		$client->call('bpReversalRequest', $parameters, $namespace);
                $response['status'] = false;
                $response['message'] = -1*$result;
        }
        return $response;
    }

    public function pay($inputs = array()) {
//        return $this->_fakeResponce();
        //-- تبدیل اطلاعات به آرایه برای ارسال به بانک
        $parameters = array(
            'terminalId' => $this->terminalId,
            'userName' => $this->userName,
            'userPassword' => $this->userPassword,
            'orderId' => $inputs['orderId'],
            'amount' => $inputs['amount'], // Price / Rial
            'localDate' => date('Ymd'),
            'localTime' => date('Gis'),
            'additionalData' => '',
            'callBackUrl' => $inputs['callbackUrl'],
            'payerId' => 0);
        /**
         * get bank connection object
         */
        try{
            $client = \App\Libs\NusoapClient::nusoap('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
            $namespace = 'http://interfaces.core.sw.bps.com/';
            $result = $client->call('bpPayRequest', $parameters, $namespace);
        } catch (\Exception $ex) {
            return ['status'=>false,'message'=>"There was a problem connecting to Bank"];
        }
        $response=['status'=>true,'message'=>''];

        if ($client->fault) {
            $response['status'] = false;
            //-- نمایش خطا
            $response['message']="There was a problem connecting to Bank";
        } else {
            $err = $client->getError();
            if ($err) {
                $response['status'] = false;
                //-- نمایش خطا
                $response['message'] = $err;
            } else {
                $response['data']=$result;
                $res = explode(',', $result);
                $ResCode = $res[0];
                if ($ResCode == "0") {
                $response['status'] = true;
                $response['refId']=$res[1];
                    //-- انتقال به درگاه پرداخت
                    $response['redirect_form']='<form name="myform" action="https://bpm.shaparak.ir/pgwchannel/startpay.mellat" method="POST">
                                                <input type="hidden" id="RefId" name="RefId" value="' . $res[1] . '">
                                        </form>
                                        <script type="text/javascript">window.onload = formSubmit; function formSubmit() { document.forms[0].submit(); }</script>';
                } else {
                    $response['status'] = false;
                    //-- نمایش خطا
                    $response['message'] = $result;
                }
            }
        }

        /**
         * call bank payment method
         */
        return $response;
    }

    private function _fakeResponce() {
        $responce['status'] = rand(0, 1);
        if (!$responce['status']) {
            $responce['errorCode'] = rand(-10, -1);
        } else {
            $responce['refId'] = rand(10000000, 99999999);
        }
        return $responce;
    }

//put your code here
}
