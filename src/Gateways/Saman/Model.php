<?php
/**
 * Created by PhpStorm.
 * User: ashrafimanesh@gmail.com
 * Date: 3/23/17
 * Time: 11:36 PM
 */

namespace Ashrafi\PaymentGateways\Gateways\Saman;


use Ashrafi\PaymentGateways\Requests\BalanceRequest;
use Ashrafi\PaymentGateways\Requests\TransferRequest;
use Ashrafi\PaymentGateways\Responses\BalanceResponse;
use Ashrafi\PaymentGateways\Requests\CallbackRequest;
use Ashrafi\PaymentGateways\Responses\CallbackResponse;
use Ashrafi\PaymentGateways\Requests\ConfirmRequest;
use Ashrafi\PaymentGateways\Responses\ConfirmResponse;
use Ashrafi\PaymentGateways\Model as PaymentGatewayModel;
use Ashrafi\PaymentGateways\Requests\PayRequest;
use Ashrafi\PaymentGateways\Responses\Response;
use Ashrafi\PaymentGateways\Responses\TransferResponse;
use Ashrafi\PhpConnectors\AbstractConnectors;
use Ashrafi\PhpConnectors\CurlConnector;
use Ashrafi\PhpConnectors\SoapConnector;

class Model extends PaymentGatewayModel
{
    private $MID = '';

    public function __construct(){

    }

    protected function _initConfigs()
    {
        if(!isset($this->config['MID']) || !$this->config['MID']){
            throw new \Exception('Please set saman MID in config.');
        }
        $this->MID=$this->config['MID'];
    }

    /**
     * call pay webservice
     * @param PayRequest $payRequest
     * @param Response $payResponse
     */
    protected function _pay(PayRequest $payRequest,Response $payResponse)
    {
        try{
            //get token from saman bank
            $token=$this->_token($payRequest->getAmount(),$payRequest->getOrderId());
            $payResponse->setGatewayResponses($token);
            if(strlen($token)>10)
            {
                $payResponse->setGatewayOrderId($token)->setStatus(true);

                $payResponse->setFormData(['action'=>'https://sep.shaparak.ir/Payment.aspx','token'=>$token,'callbackUrl'=>$payRequest->getCallbackUrl()]);
                $payResponse->setHtml($this->createPayHtmlForm($token,$payRequest->getCallbackUrl()));
            }
            else{
                $payResponse->setStatus(false)->setMessage('خطا در اتصال به بانک سامان');
            }

        }catch (\Exception $ex){
            $payResponse->setStatus(false)->setMessage($ex->getMessage())->setCode($ex->getCode());
        }
        return $payResponse;
    }

    /**
     * @param CallbackRequest $callbackRequest
     * @param CallbackResponse $callbackResponse
     * @throws \Exception
     */
    protected function _callback(CallbackRequest $callbackRequest,CallbackResponse $callbackResponse)
    {
        

        $inputs=$callbackRequest->getGatewayResponses();
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


    /**
     * @param ConfirmRequest $confirmRequest
     * @param ConfirmResponse $confirmResponse
     * @return ConfirmResponse
     * @throws \Exception
     */
    protected function _confirm(ConfirmRequest $confirmRequest,ConfirmResponse $confirmResponse)
    {

        $result=null;
        if($confirmRequest->getGatewayOrderId()) {
            $url='https://sep.shaparak.ir/payments/referencepayment.asmx?WSDL';
            $client=$this->_getConnector($url,SoapConnector::class);
            $result = $client->run('verifyTransaction',[$confirmRequest->getGatewayOrderId(), $this->MID]);
        }
        $confirmResponse->setGatewayResponses($result);
        if($result>0){
            $confirmResponse->setStatus(true)->setAmount($result);
        }
        else{
            $confirmResponse->setMessage($result)->setStatus(false);
        }
        return $confirmResponse;
    }

    /**
     * @param BalanceRequest $balanceRequest
     * @param BalanceResponse $balanceResponse
     * @return BalanceResponse
     */
    protected function _getBalance(BalanceRequest $balanceRequest=null,BalanceResponse $balanceResponse)
    {

//        $balanceResponse=new BalanceResponse(($balanceRequest instanceof BalanceRequest) ? $balanceRequest->getUsername() : '');
        $balanceResponse->setStatus(false)->setMessage('Method '.__FUNCTION__.' does not exist in saman gateway');
        return $balanceResponse;
    }

    /**
     * @param TransferRequest $transferRequest
     * @param TransferResponse $transferResponse
     * @return TransferResponse
     */
    protected function _transfer(TransferRequest $transferRequest,TransferResponse $transferResponse)
    {
        $transferResponse->setStatus(false)->setMessage('Method '.__FUNCTION__.' does not exist in saman gateway');
        return $transferResponse;
    }


    public function getServerAddress()
    {
        // TODO: Implement getServerAddress() method.
    }

    protected function createPayHtmlForm($token,$callbackurl){
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


    /**
     * @param $amount
     * @param $orderId
     * @return mixed|null
     * @throws \Exception
     */
    private function _token($amount,$orderId){
        $result=null;
        if(isset($amount) && isset($orderId)) {
            $request = [
                'TermID' => $this->MID,
                'TotalAmount' => ($amount),
                'ResNum' => $orderId,
            ];
            $url='https://sep.shaparak.ir/Payments/InitPayment.asmx?WSDL';
            $client = $this->_getConnector($url,SoapConnector::class);
            $i = 0;
            while ($i < 2) {
                $i++;
                $this->increaseTryCount();
                $result = $client->run('RequestToken',[$request['TermID'], $request['ResNum'], $request['TotalAmount']]);
                if ($result) {
                    $i = 10;
                    break;
                }
            }
        }
        return $result;
    }
}