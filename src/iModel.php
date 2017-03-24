<?php

namespace Ashrafi\PaymentGateways;

/**
 *
 * @author ramin ashrafimanesh <ashrafimanesh@gmail.com>
 */
interface iModel {

    /**
     * call pay webservice
     * @param PayRequest $payRequest
     * @return Response
     */
    function pay(PayRequest $payRequest);


    /**
     * @param CallbackRequest $callbackRequest
     * @return CallbackResponse
     */
    function callback(CallbackRequest $callbackRequest);
    
    /**
     * call confirm webservice
     */
    function confirm(ConfirmRequest $confirmRequest);
}
