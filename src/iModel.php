<?php

namespace Ashrafi\PaymentGateways;
use Ashrafi\PaymentGateways\Requests\BalanceRequest;
use Ashrafi\PaymentGateways\Requests\CallbackRequest;
use Ashrafi\PaymentGateways\Requests\ConfirmRequest;
use Ashrafi\PaymentGateways\Requests\PayRequest;
use Ashrafi\PaymentGateways\Requests\TransferRequest;
use Ashrafi\PaymentGateways\Responses\BalanceResponse;
use Ashrafi\PaymentGateways\Responses\CallbackResponse;
use Ashrafi\PaymentGateways\Responses\ConfirmResponse;
use Ashrafi\PaymentGateways\Responses\Response;
use Ashrafi\PaymentGateways\Responses\TransferResponse;

/**
 *
 * @author ramin ashrafimanesh <ashrafimanesh@gmail.com>
 */
interface iModel {

    /**
     * @param iConfig $config
     * @param array $globalConfigs
     * @return $this
     */
    function setConfig(iConfig $config,$globalConfigs=[]);

    /**
     * call pay webservice
     * @param PayRequest $payRequest
     * @param Response $response
     */
    function pay(PayRequest $payRequest,Response $response);


    /**
     * @param CallbackRequest $callbackRequest
     * @param CallbackResponse $callbackResponse
     */
    function callback(CallbackRequest $callbackRequest,CallbackResponse $callbackResponse);

    /**
     * call confirm webservice
     * @param ConfirmRequest $confirmRequest
     * @param ConfirmResponse $confirmResponse
     */
    function confirm(ConfirmRequest $confirmRequest,ConfirmResponse $confirmResponse);


    /**
     * @param BalanceRequest $balanceRequest
     * @param BalanceResponse $balanceResponse
     */
    function getBalance(BalanceRequest $balanceRequest=null,BalanceResponse $balanceResponse);

    /**
     * @param TransferRequest $transferRequest
     * @param TransferResponse $transferResponse
     */
    function transfer(TransferRequest $transferRequest,TransferResponse $transferResponse);
}
