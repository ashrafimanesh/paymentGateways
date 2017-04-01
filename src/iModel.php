<?php

namespace Ashrafi\PaymentGateways;
use Ashrafi\PaymentGateways\Requests\BalanceRequest;
use Ashrafi\PaymentGateways\Requests\CallbackRequest;
use Ashrafi\PaymentGateways\Requests\ConfirmRequest;
use Ashrafi\PaymentGateways\Requests\PayRequest;
use Ashrafi\PaymentGateways\Requests\TransferRequest;

/**
 *
 * @author ramin ashrafimanesh <ashrafimanesh@gmail.com>
 */
interface iModel {

    /**
     * call pay webservice
     * @param PayRequest $payRequest
     * @return Responses\Response
     */
    function pay(PayRequest $payRequest);


    /**
     * @param CallbackRequest $callbackRequest
     * @return Responses\CallbackResponse
     */
    function callback(CallbackRequest $callbackRequest);
    
    /**
     * call confirm webservice
     * @param ConfirmRequest $confirmRequest
     * @return Responses\ConfirmResponse
     */
    function confirm(ConfirmRequest $confirmRequest);


    /**
     * @param BalanceRequest $balanceRequest
     * @return Responses\BalanceResponse
     */
    function getBalance(BalanceRequest $balanceRequest=null);

    /**
     * @param TransferRequest $transferRequest
     * @return TransferResponse
     */
    function transfer(TransferRequest $transferRequest);
}
