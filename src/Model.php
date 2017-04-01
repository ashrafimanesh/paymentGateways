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
use Ashrafi\PaymentGateways\Responses\CallbackResponse;
use Ashrafi\PaymentGateways\Responses\ConfirmResponse;
use Ashrafi\PaymentGateways\Responses\Response;

abstract class Model implements iModel
{
    protected $calledClass,$config;

    public function __construct(){
        $config=require __DIR__.'/config/app.php';
        $this->config=$config;
    }

    /**
     * @param PayRequest $payRequest
     * @return Responses\Response
     */
    abstract protected function _pay(PayRequest $payRequest);

    /**
     * @param CallbackRequest $callbackRequest
     * @return Responses\CallbackResponse
     */
    abstract protected function _callback(CallbackRequest $callbackRequest);

    /**
     * @param ConfirmRequest $confirmRequest
     * @return Responses\ConfirmResponse
     */
    abstract protected function _confirm(ConfirmRequest $confirmRequest);

    /**
     * @param BalanceRequest $balanceRequest
     * @return Responses\BalanceResponse
     */
    abstract protected function _getBalance(BalanceRequest $balanceRequest);

    function pay(PayRequest $payRequest)
    {
        $finalStatus=$payRequest->getFinalStatus();
        if($finalStatus){
            throw new \Exception('Invalid order for pay. Current status is '.$finalStatus);
        }
        $this->calledClass=get_called_class();
        $payRequest->save()->saveFinalStatus(Response::SuccessPayRequest);

        $payResponse=$this->_pay($payRequest);

        $payResponse->save();
        if($payResponse->getStatus()){
            $payResponse->saveFinalStatus(Response::SuccessPayResponse);
        }
        else{
            $payResponse->saveFinalStatus(Response::FailedPay);
        }
        return $payResponse;
    }

    function callback(CallbackRequest $callbackRequest)
    {
        $this->calledClass=get_called_class();

        $finalStatus=$callbackRequest->getFinalStatus();
        if(!in_array($finalStatus,[Response::SuccessPayResponse])){

            //get last response
            $finalResponse = $this->_getFinalPayResponse($callbackRequest);

            if(!$finalResponse){
                throw new \Exception('Invalid order for callback. Current status is '.$finalStatus);
            }
            else{
                return $finalResponse;
            }
        }

        $callbackRequest->save();

        $callbackResponse=$this->_callback($callbackRequest);
        $callbackResponse->setGatewayResponses($callbackRequest->getGatewayResponses());
        $callbackResponse->save();
        if($callbackResponse->getStatus()){
            $callbackResponse->saveFinalStatus(Response::SuccessPaid);
        }
        else{
            $callbackResponse->saveFinalStatus(Response::FailedPay);
        }
        return $callbackResponse;
    }

    function confirm(ConfirmRequest $confirmRequest)
    {
        $this->calledClass=get_called_class();
        $finalStatus=$confirmRequest->getFinalStatus();
        if(!in_array($finalStatus,[Response::FailedConfirm,Response::SuccessPaid])){
            //get last response
            $finalResponse = $this->_getFinalConfirmResponse($confirmRequest);
            if(!$finalResponse){
                $finalResponse=$this->_getFinalPayResponse($confirmRequest);
            }
            if(!$finalResponse){
                throw new \Exception('Invalid order for confirm. Current status is '.$finalStatus);
            }
            else{
                return $finalResponse;
            }
        }
        $confirmRequest->save()->saveFinalStatus(Response::SuccessConfirmRequest);
        $confirmResponse=$this->_confirm($confirmRequest);
        $confirmResponse->save();
        if($confirmResponse->getStatus()){
            $confirmResponse->saveFinalStatus(Response::SuccessConfirm);
        }
        else{
            $confirmResponse->saveFinalStatus(Response::FailedConfirm);
        }
        return $confirmResponse;
    }

    function getBalance(BalanceRequest $balanceRequest)
    {
        $this->calledClass=get_called_class();

    }


    /**
     * @param Request $request
     * @return null
     */
    protected function _getFinalPayResponse(Request $request)
    {
        $finalResponse = Response::getSavedResponse($request, CallbackResponse::class);
        if (!$finalResponse) {
            $finalResponse = Response::getSavedResponse($request, CallbackRequest::class);
            return $finalResponse;
        }
        return $finalResponse;
    }

    /**
     * @param Request $request
     * @return null
     */
    protected function _getFinalConfirmResponse(Request $request)
    {
        $finalResponse = Response::getSavedResponse($request, ConfirmResponse::class);
        if (!$finalResponse) {
            $finalResponse = Response::getSavedResponse($request, ConfirmRequest::class);
            return $finalResponse;
        }
        return $finalResponse;
    }


}