<?php

namespace Ashrafi\PaymentGateways\Gateways\Mellat;

use Ashrafi\PaymentGateways\CallbackRequest;
use Ashrafi\PaymentGateways\CallbackResponse;
use Ashrafi\PaymentGateways\ConfirmRequest;
use Ashrafi\PaymentGateways\ConfirmResponse;
use Ashrafi\PaymentGateways\Model as PaymentGatewayModel;
use Ashrafi\PaymentGateways\PayRequest;
use Ashrafi\PaymentGateways\Response;
use Ashrafi\PhpConnectors\AbstractConnectors;
use Ashrafi\PhpConnectors\NusoapConnector;
use Ashrafi\PhpConnectors\SoapConnector;
class Model extends PaymentGatewayModel
{
    private $userName, $terminalId, $userPassword ;
    public function __construct(){
        parent::__construct();
        $this->userName=$this->config['gateways']['mellat']['userName'];
        $this->terminalId=$this->config['gateways']['mellat']['terminalId'];
        $this->userPassword=$this->config['gateways']['mellat']['userPassword'];
        if(!$this->userName){
            throw new \Exception('Please set mellat userName value in config. Read config/app.php for more detail');
        }
    }

    /**
     * @param PayRequest $payRequest
     * @return Response
     */
    protected function _pay(PayRequest $payRequest)
    {
        $payResponse=new Response($payRequest);
//        return $this->_fakeResponce();
        //-- تبدیل اطلاعات به آرایه برای ارسال به بانک
        $parameters = $this->_getPayParams($payRequest);
        /**
         * get bank connection object
         */
        try{
            if($this->config['proxy']['enable'] && $this->config['proxy']['nusoapProxyAddress']){
                $client = NusoapConnector::getInstance('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl',$this->config['proxy']['nusoapProxyAddress'],null,AbstractConnectors::ProxyTypeUrl);
            }
            else{
                $client = NusoapConnector::getInstance('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
            }
            $namespace = 'http://interfaces.core.sw.bps.com/';
            $result = $client->run('bpPayRequest', [$parameters, $namespace]);
            $payResponse->setGatewayResponses($result);
        } catch (\Exception $ex) {
            $payResponse->setStatus(false);
            $payResponse->setMessage("There was a problem connecting to Bank");
            return $payResponse;
        }

        if ($client->fault) {
            $payResponse->setStatus(false);
            $payResponse->setMessage("There was a problem connecting to Bank");
        } else {
            $err = $client->getError();
            if ($err) {
                $payResponse->setStatus(false);
                $payResponse->setMessage($err);
            } else {
//                $response['data']=$result;
                $res = explode(',', $result);
                $ResCode = $res[0];
                if ($ResCode == "0") {
                    $payResponse->setStatus(true);
                    $payResponse->setGatewayOrderId($res[1]);
                    //-- انتقال به درگاه پرداخت
                    $payResponse->setHtml('<form name="myform" action="https://bpm.shaparak.ir/pgwchannel/startpay.mellat" method="POST">
                                                <input type="hidden" id="RefId" name="RefId" value="' . $res[1] . '">
                                        </form>
                                        <script type="text/javascript">window.onload = formSubmit; function formSubmit() { document.forms[0].submit(); }</script>');
                } else {
                    $payResponse->setStatus(false)->setMessage(-1*$result);
                }
            }
        }

        /**
         * call bank payment method
         */
        return $payResponse;
    }

    /**
     * @param CallbackRequest $callbackRequest
     * @return CallbackResponse
     */
    protected function _callback(CallbackRequest $callbackRequest)
    {
        $inputs=$callbackRequest->getGatewayResponses();
        $callbackResponse=new CallbackResponse($callbackRequest);
        if(!isset($inputs['SaleOrderId']) || !isset($inputs['SaleReferenceId']) || !isset($inputs['orderId'])){
            $callbackResponse->setStatus(false);
            $callbackResponse->setMessage('Invalid Response');
        }
        switch(true){
            case !$inputs['SaleOrderId'] || !$inputs['SaleReferenceId'] || !$inputs['orderId']:
                $callbackResponse->setStatus(false);
                $callbackResponse->setMessage('Invalid Response');
                break;
            case $inputs['SaleOrderId'] && $inputs['SaleReferenceId']:
                $callbackResponse->setStatusCode(CallbackResponse::Success)->setStatus(true)->setGatewayOrderId($inputs['SaleReferenceId']);
                break;

        }
        return $callbackResponse;
    }

    /**
     * @param ConfirmRequest $confirmRequest
     * @return ConfirmResponse
     */
    protected function _confirm(ConfirmRequest $confirmRequest)
    {
        $result=null;
        if($confirmRequest->getGatewayOrderId()) {
            if($this->config['proxy']['enable'] && $this->config['proxy']['nusoapProxyAddress']){
                $client = NusoapConnector::getInstance('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl',$this->config['proxy']['nusoapProxyAddress'],null,AbstractConnectors::ProxyTypeUrl);
            }
            else {
                $client = NusoapConnector::getInstance('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
            }
            $namespace = 'http://interfaces.core.sw.bps.com/';
            $parameters = array(
                'terminalId' => $this->terminalId,
                'userName' => $this->userName,
                'userPassword' => $this->userPassword,
                'orderId' => $confirmRequest->getOrderId(),
                'saleOrderId' => '',
                'saleReferenceId' => $confirmRequest->getGatewayOrderId());

            $result = $client->run('bpVerifyRequest',[$parameters, $namespace]);
        }
        $confirmResponse=new ConfirmResponse($confirmRequest);
        $confirmResponse->setGatewayResponses($result);

        if($result == 0) {
            //-- وریفای به درستی انجام شد٬ درخواست واریز وجه
            // Call the SOAP method
            $result = $client->run('bpSettleRequest', [$parameters, $namespace]);

            $confirmResponse->setGatewayResponses($result);

            if($result == 0) {
                //-- تمام مراحل پرداخت به درستی انجام شد.
                //-- آماده کردن خروجی
                $confirmResponse->setStatus(true)->setMessage('The transaction was successful');
            } else {
                //-- در درخواست واریز وجه مشکل به وجود آمد. درخواست بازگشت وجه داده شود.
                $client->run('bpReversalRequest',[$parameters, $namespace]);
                $confirmResponse->setStatus(false)->setMessage((-1*$result));
            }
        } else {
            //-- وریفای به مشکل خورد٬ نمایش پیغام خطا و بازگشت زدن مبلغ
            $client->run('bpReversalRequest', [$parameters, $namespace]);
            $confirmResponse->setStatus(false)->setMessage((-1*$result));
        }
        return $confirmResponse;
    }

    /**
     * @param PayRequest $payRequest
     * @return array
     */
    private function _getPayParams(PayRequest $payRequest)
    {
        $parameters = array(
            'terminalId' => $this->terminalId,
            'userName' => $this->userName,
            'userPassword' => $this->userPassword,
            'orderId' => (int)$payRequest->getOrderId(),
            'amount' => $payRequest->getAmount(), // Price / Rial
            'localDate' => date('Ymd'),
            'localTime' => date('Gis'),
            'additionalData' => '',
            'callBackUrl' => $payRequest->getCallbackUrl(),
            'payerId' => 0);
        return $parameters;
    }

}