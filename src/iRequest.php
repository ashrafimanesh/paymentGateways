<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 4/11/17
 * Time: 11:49 AM
 */

namespace Ashrafi\PaymentGateways;


interface iRequest
{
    /**
     * @param $orderId
     * @return $this
     */
    function setOrderId($orderId);

    /**
     * @return mixed
     */
    function getOrderId();

    /**
     * @param $gatewayOrderId
     * @return $this
     */
    function setGatewayOrderId($gatewayOrderId);

    /**
     * @return mixed
     */
    function getGatewayOrderId();
}