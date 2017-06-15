<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 3/24/17
 * Time: 12:38 PM
 */

namespace Ashrafi\PaymentGateways;


use Ashrafi\PaymentGateways\Requests\BalanceRequest;
use Ashrafi\PaymentGateways\Requests\CallbackRequest;
use Ashrafi\PaymentGateways\Requests\ConfirmRequest;
use Ashrafi\PaymentGateways\Requests\PayRequest;
use Ashrafi\PaymentGateways\Requests\Request;
use Ashrafi\PaymentGateways\Requests\TransferRequest;
use Ashrafi\PaymentGateways\Responses\BalanceResponse;
use Ashrafi\PaymentGateways\Responses\TransferResponse;
use Ashrafi\PaymentGateways\Responses\CallbackResponse;
use Ashrafi\PaymentGateways\Responses\ConfirmResponse;
use Ashrafi\PaymentGateways\Responses\Response as PayResponse;
use Ashrafi\PhpConnectors\AbstractConnectors;
use Ashrafi\PhpConnectors\CurlConnector;
use Ashrafi\PhpConnectors\NusoapConnector;
use Ashrafi\PhpConnectors\SoapConnector;

abstract class Model implements iModel
{
    protected $calledClass;
    /**
     * @var iConfig
     */
    protected $config;
    protected $checkFinalStatus=false;
    protected $innerCalledClass=null;

    /**
     * set private configs
     * @return mixed
     */
    abstract protected function _initConfigs();

    /**
     * @param PayRequest $payRequest
     * @param PayResponse $payResponse
     */
    abstract protected function _pay(PayRequest $payRequest,PayResponse $payResponse);

    /**
     * @param CallbackRequest $callbackRequest
     * @param CallbackResponse $callbackResponse
     */
    abstract protected function _callback(CallbackRequest $callbackRequest,CallbackResponse $callbackResponse);

    /**
     * @param ConfirmRequest $confirmRequest
     * @param ConfirmResponse $confirmResponse
     */
    abstract protected function _confirm(ConfirmRequest $confirmRequest,ConfirmResponse $confirmResponse);

    /**
     * @param BalanceRequest $balanceRequest
     * @param BalanceResponse $BalanceResponse
     */
    abstract protected function _getBalance(BalanceRequest $balanceRequest=null,BalanceResponse $BalanceResponse);

    /**
     * @param TransferRequest $transferRequest
     * @param TransferResponse $transferResponse
     */
    abstract protected function _transfer(TransferRequest $transferRequest,TransferResponse $transferResponse);

    /**
     * @param iConfig $config
     * @param array $globalConfigs
     * @return $this
     */
    function setConfig(iConfig $config,$globalConfigs=[])
    {
        $this->config=$config->getConfigs()+$globalConfigs;
        $this->_initConfigs();
        return $this;
    }

    /**
     * @param PayRequest $payRequest
     * @param PayResponse $payResponse
     * @return PayResponse
     * @throws \Exception
     */
    function pay(PayRequest $payRequest,PayResponse $payResponse)
    {
        $finalStatus=$payRequest->getFinalStatus();
        if($this->isCheckFinalStatus() && $finalStatus){
            throw new \Exception('Invalid order for pay. Current status is '.$finalStatus);
        }
        $this->calledClass=get_called_class();
        $payRequest->save()->saveFinalStatus(PayResponse::SuccessPayRequest);

        $this->_pay($payRequest,$payResponse);

        $payResponse->save();
        if($payResponse->getStatus()){
            $payResponse->saveFinalStatus(PayResponse::SuccessPayResponse);
        }
        else{
            $payResponse->saveFinalStatus(PayResponse::FailedPay);
        }
        return $payResponse;
    }

    /**
     * @param CallbackRequest $callbackRequest
     * @param CallbackResponse $callbackResponse
     * @return CallbackResponse|null
     * @throws \Exception
     */
    function callback(CallbackRequest $callbackRequest,CallbackResponse $callbackResponse)
    {
        $this->calledClass=get_called_class();

        $finalStatus=$callbackRequest->getFinalStatus();
        if($this->isCheckFinalStatus() && !in_array($finalStatus,[PayResponse::SuccessPayResponse])){

            //get last PayResponse
            $finalResponses = $this->_getFinalResponses($callbackRequest);

            if(!$finalResponses){
                throw new \Exception('Invalid order for callback. Current status is '.$finalStatus);
            }
            else{
                return $finalResponses;
            }
        }

        $callbackRequest->save();

        $this->_callback($callbackRequest,$callbackResponse);

        $callbackResponse->setGatewayResponses($callbackRequest->getGatewayResponses());
        $callbackResponse->save();
        if($callbackResponse->getStatus()){
            $callbackResponse->saveFinalStatus(PayResponse::SuccessPaid);
        }
        else{
            $callbackResponse->saveFinalStatus(PayResponse::FailedPay);
        }
        return $callbackResponse;
    }

    /**
     * @param ConfirmRequest $confirmRequest
     * @param ConfirmResponse $confirmResponse
     * @return ConfirmResponse|null
     * @throws \Exception
     */
    function confirm(ConfirmRequest $confirmRequest,ConfirmResponse $confirmResponse)
    {
        $this->calledClass=get_called_class();
        $finalStatus=$confirmRequest->getFinalStatus();
        if($this->isCheckFinalStatus() && !in_array($finalStatus,[PayResponse::FailedConfirm,PayResponse::SuccessPaid])){
            //get last PayResponse
            $finalResponses = $this->_getFinalConfirmResponse($confirmRequest);
            if(!$finalResponses){
                $finalResponses=$this->_getFinalResponses($confirmRequest);
            }
            if(!$finalResponses){
                throw new \Exception('Invalid order for confirm. Current status is '.$finalStatus);
            }
            else{
                return $finalResponses;
            }
        }
        $confirmRequest->save()->saveFinalStatus(PayResponse::SuccessConfirmRequest);

        $this->_confirm($confirmRequest,$confirmResponse);

        $confirmResponse->save();
        if($confirmResponse->getStatus()){
            $confirmResponse->saveFinalStatus(PayResponse::SuccessConfirm);
        }
        else{
            $confirmResponse->saveFinalStatus(PayResponse::FailedConfirm);
        }
        return $confirmResponse;
    }

    /**
     * @param BalanceRequest $balanceRequest
     * @param BalanceResponse $BalanceResponse
     */
    function getBalance(BalanceRequest $balanceRequest=null,BalanceResponse $BalanceResponse=null)
    {
        $this->calledClass=get_called_class();

        $this->_getBalance($balanceRequest,$BalanceResponse);

        return $BalanceResponse;
    }

    /**
     * @param TransferRequest $transferRequest
     * @param TransferResponse $transferResponse
     * @return TransferResponse
     */
    function transfer(TransferRequest $transferRequest,TransferResponse $transferResponse){

        $this->calledClass=get_called_class();

        $this->_transfer($transferRequest,$transferResponse);

        return $transferResponse;
    }


    /**
     * @param Request $request
     * @return null
     */
    protected function _getFinalResponses(Request $request)
    {
        $finalResponses = PayResponse::getSavedResponse($request, CallbackResponse::class);
        if (!$finalResponses) {
            $finalResponses = PayResponse::getSavedResponse($request, CallbackRequest::class);
            return $finalResponses;
        }
        return $finalResponses;
    }

    /**
     * @param Request $request
     * @return null
     */
    protected function _getFinalConfirmResponse(Request $request)
    {
        $finalResponses = PayResponse::getSavedResponse($request, ConfirmResponse::class);
        if (!$finalResponses) {
            $finalResponses = PayResponse::getSavedResponse($request, ConfirmResponse::class);
            return $finalResponses;
        }
        return $finalResponses;
    }


    /**
     * @param $url
     * @return AbstractConnectors
     */
    protected function _getConnector($url, $class=SoapConnector::class)
    {
        switch($class){
            case CurlConnector::class:
                $proxyAddressIndex='curlProxyAddress';
                break;
            case NusoapConnector::class:
                $proxyAddressIndex='nusoapProxyAddress';
                break;
            case SoapConnector::class:
            default:
                $proxyAddressIndex='soapProxyAddress';
                break;

        }
        if (isset($this->config['proxy']) && $this->config['proxy']['enable'] &&  $this->config['proxy'][$proxyAddressIndex]) {
            if(!in_array($this->config['proxy']['type'],[AbstractConnectors::ProxyTypeUrl,AbstractConnectors::ProxyTypeHttp])){
                throw new \Exception('Invalid proxy type set in config/app.php or env file');
            }
            $client = $class::getInstance($url, $this->config['proxy'][$proxyAddressIndex], null, $this->config['proxy']['type']);
        } else {
            $client = $class::getInstance($url);
        }
        return $client;
    }

    /**
     * @return boolean
     */
    public function isCheckFinalStatus()
    {
        return $this->checkFinalStatus;
    }

    /**
     * @param boolean $checkFinalStatus
     * @return $this
     */
    public function setCheckFinalStatus($checkFinalStatus)
    {
        $this->checkFinalStatus = $checkFinalStatus;
        return $this;
    }

    /**
     * @return null
     */
    public function getInnerCalledClass()
    {
        return $this->innerCalledClass;
    }

    /**
     * @param Model $innerCalledClass
     * @return $this
     */
    public function setInnerCalledClass(Model $innerCalledClass)
    {
        $this->innerCalledClass = $innerCalledClass;
        return $this;
    }
}