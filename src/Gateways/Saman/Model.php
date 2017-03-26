<?php
/**
 * Created by PhpStorm.
 * User: ashrafimanesh@gmail.com
 * Date: 3/23/17
 * Time: 11:36 PM
 */

namespace Ashrafi\PaymentGateways\Gateways\Saman;


use Ashrafi\PaymentGateways\CallbackRequest;
use Ashrafi\PaymentGateways\CallbackResponse;
use Ashrafi\PaymentGateways\ConfirmRequest;
use Ashrafi\PaymentGateways\ConfirmResponse;
use Ashrafi\PaymentGateways\iModel;
use Ashrafi\PaymentGateways\PayRequest;
use Ashrafi\PaymentGateways\Request;
use Ashrafi\PaymentGateways\Response;
use Ashrafi\PhpConnectors\AbstractConnectors;
use Ashrafi\PhpConnectors\SoapConnector;

class Model extends \Ashrafi\PaymentGateways\Model
{
    private $MID = '10560528';

    /**
     * call pay webservice
     * @param PayRequest $payRequest
     * @return Response
     */
    protected function _pay(PayRequest $payRequest)
    {
        $payResponse=new Response($payRequest);
        try{
            $token=$this->_token($payRequest->getAmount(),$payRequest->getOrderId());
            $payResponse->setGatewayResponses($token);
            if(strlen($token)>10)
            {
                $payResponse->setGatewayOrderId($token);


                $payResponse->setHtml($this->payHtmlForm($token,$payRequest->getCallbackUrl()))->setStatus(true);
            }
            else{
                $payResponse->setStatus(false)->setMessage($token);
            }

        }catch (\Exception $ex){
            $payResponse->setStatus(false)->setMessage($ex->getMessage())->setCode($ex->getCode());
        }
        return $payResponse;
    }

    protected function _callback(CallbackRequest $callbackRequest)
    {
        $inputs=$callbackRequest->getGatewayResponses();
        $callbackResponse=new CallbackResponse($callbackRequest);
        $callbackResponse->setMessage($inputs['State']);
        switch(true){
            case $inputs['StateCode']==-1:
                $callbackResponse->setStatusCode(CallbackResponse::CancelByUser);
                break;
            case $inputs['StateCode']=="0" && $inputs['RefNum'] && $inputs['SecurePan']:
            case $inputs['StateCode']==0 && $inputs['RefNum'] && $inputs['SecurePan']:
                $callbackResponse->setStatusCode(CallbackResponse::Success)->setCardNumber($inputs['SecurePan'])->setStatus(true)->setGatewayOrderId($inputs['RefNum']);
                break;

        }
        return $callbackResponse;
    }


    protected function _confirm(ConfirmRequest $confirmRequest)
    {
        $result=null;
        if($confirmRequest->getGatewayOrderId()) {
            $config=require __DIR__.'/../../config/app.php';
            if($config['proxy']['enable'] && $config['proxy']['soapProxyAddress']){
                $client = SoapConnector::getInstance('https://sep.shaparak.ir/payments/referencepayment.asmx?WSDL',$config['proxy']['soapProxyAddress'],null,AbstractConnectors::ProxyTypeUrl);
            }
            else {
                $client = SoapConnector::getInstance('https://sep.shaparak.ir/payments/referencepayment.asmx?WSDL');
            }
            $result = $client->run('verifyTransaction',[$confirmRequest->getGatewayOrderId(), $this->MID]);
        }
        $confirmResponse=new ConfirmResponse($confirmRequest);
        $confirmResponse->setGatewayResponses($result);
        if($result>0){
            $confirmResponse->setStatus(true)->setAmount($result);
        }
        return $confirmResponse;
    }

    public function getServerAddress()
    {
        // TODO: Implement getServerAddress() method.
    }

    protected function payHtmlForm($token,$callbackurl){
        $html='<form action="https://sep.shaparak.ir/Payment.aspx" method="POST" id="myForm">';
        $html.= '<input type="hidden" name="Token" value="'.$token.'"/>';
        $html.= '<input type="hidden" name="RedirectURL" value="'.$callbackurl.'"/>';
        $html.= '</form>';
        $html.= '<script>document.getElementById("myForm").submit();</script>';
        return $html;
    }


    protected function increaseTryCount()
    {

    }


    private function _token($amount,$orderId){
        $result=null;
        if(isset($amount) && isset($orderId)) {
            $request = [
                'TermID' => $this->MID,
                'TotalAmount' => ($amount),
                'ResNum' => $orderId,
            ];
            $config=require __DIR__.'/../../config/app.php';
            if($config['proxy']['enable'] && $config['proxy']['soapProxyAddress']){
                $client = SoapConnector::getInstance('https://sep.shaparak.ir/Payments/InitPayment.asmx?WSDL',$config['proxy']['soapProxyAddress'],null,AbstractConnectors::ProxyTypeUrl);
            }
            else{
                $client = SoapConnector::getInstance('https://sep.shaparak.ir/Payments/InitPayment.asmx?WSDL');
            }
            $i = 0;
            while ($i < 2) {
                $i++;
                $this->increaseTryCount();
                $result = $client->run('RequestToken',[$request['TermID'], $request['ResNum'], $request['TotalAmount']]);
                if (!strpos(" " . $result, '/')) {
                    $i = 10;
                    break;
                }
            }
        }
        return $result;
    }
}