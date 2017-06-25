<?php

/**
 * Created by PhpStorm.
 * User: ashrafimanesh@gmail.com
 * Date: 6/25/17
 * Time: 9:48 PM
 */
namespace Ashrafi\PaymentGateways\Gateways\Dalanpay;

use Ashrafi\PaymentGateways\Model as PaymentGatewayModel;
use Ashrafi\PaymentGateways\Requests\BalanceRequest;
use Ashrafi\PaymentGateways\Requests\CallbackRequest;
use Ashrafi\PaymentGateways\Requests\ConfirmRequest;
use Ashrafi\PaymentGateways\Requests\PayRequest;
use Ashrafi\PaymentGateways\Requests\TransferRequest;
use Ashrafi\PaymentGateways\Responses\BalanceResponse;
use Ashrafi\PaymentGateways\Responses\CallbackResponse;
use Ashrafi\PaymentGateways\Responses\ConfirmResponse;
use Ashrafi\PaymentGateways\Responses\Response as PayResponse;
use Ashrafi\PaymentGateways\Responses\TransferResponse;
use Ashrafi\PhpConnectors\ConnectorFactory;
use Ashrafi\PhpConnectors\CurlConnector;

class Model extends PaymentGatewayModel
{

    const DirectAccount = 1;
    const InDirectAccount = 2;
    protected $accountType;
    private $apiKey;
    /**
     * set private configs
     * @return mixed
     */
    protected function _initConfigs()
    {
        if(!isset($this->config['apiKey']) || !$this->config['apiKey']){
            throw new \Exception('Please set dalanpay apiKey in config.');
        }
        $this->apiKey=$this->config['apiKey'];
        $this->accountType=isset($this->config['accountType']) ? $this->config['accountType'] : self::DirectAccount;
    }

    /**
     * @param PayRequest $payRequest
     * @param PayResponse $payResponse
     */
    protected function _pay(PayRequest $payRequest, PayResponse $payResponse)
    {
        $params = [
            'apikey' => $this->apiKey,
            'amount' => $payRequest->getAmount(),
            'bankID' => 1,
            'callbackurl' => $payRequest->getCallbackUrl()
        ];
        $url='http://dalanpay.ir/gateway/create';
        $client = $this->_getConnector($url,CurlConnector::class);

        $result = json_decode($client->run(null, $params));
        $payResponse->setGatewayResponses($result);
        if($result && $result->status>0){
            $payResponse->setStatus(true)->setGatewayOrderId($result->refId);
            $payResponse->setFormData(['action'=>$result->goto,'token'=>$result->refId,'callbackUrl'=>$payRequest->getCallbackUrl()]);
            if($this->accountType==self::DirectAccount){
                $connector=ConnectorFactory::create(CurlConnector::class,$result->goto);
                $result=json_decode($connector->run(null,[]));
                $payResponse->setHtml($result->form);
            }
        }
        else{
            $payResponse->setStatus(false)->setMessage($result->errorMessage);
        }
        return $payResponse;
    }

    /**
     * @param CallbackRequest $callbackRequest
     * @param CallbackResponse $callbackResponse
     */
    protected function _callback(CallbackRequest $callbackRequest, CallbackResponse $callbackResponse)
    {
        $inputs=$callbackRequest->getGatewayResponses();
        if($inputs['status']>0 && isset($inputs['refId'])){
            $callbackResponse->setStatus(true)->setStatusCode(CallbackResponse::Success)->setGatewayOrderId($inputs['refId']);
        }
        else{
            $callbackResponse->setStatus(false)->setMessage(isset($inputs['errorMessage']) ? $inputs['errorMessage'] : '');
        }
    }

    /**
     * @param ConfirmRequest $confirmRequest
     * @param ConfirmResponse $confirmResponse
     */
    protected function _confirm(ConfirmRequest $confirmRequest, ConfirmResponse $confirmResponse)
    {
        $result=null;
        if($confirmRequest->getGatewayOrderId()) {
            $url='http://dalanpay.ir/gateway/verify';
            $client=$this->_getConnector($url,CurlConnector::class);
            $params=['apikey'=>$this->apiKey,'refId'=>$confirmRequest->getGatewayOrderId()];
            $result = json_decode($client->run(null,$params));
        }
        $confirmResponse->setGatewayResponses($result);
        if($result && isset($result->status) && $result->status>0){
            $confirmResponse->setStatus(true);
        }
        else{
            $confirmResponse->setMessage($result && isset($result->errorMessage) ? $result->errorMessage : '')->setStatus(false);
        }
        return $confirmResponse;
    }

    /**
     * @param BalanceRequest $balanceRequest
     * @param BalanceResponse $BalanceResponse
     */
    protected function _getBalance(BalanceRequest $balanceRequest = null, BalanceResponse $BalanceResponse)
    {
        // TODO: Implement _getBalance() method.
    }

    /**
     * @param TransferRequest $transferRequest
     * @param TransferResponse $transferResponse
     */
    protected function _transfer(TransferRequest $transferRequest, TransferResponse $transferResponse)
    {
        // TODO: Implement _transfer() method.
    }
}