<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 4/11/17
 * Time: 11:47 AM
 */

namespace Ashrafi\PaymentGateways;


interface iResponse
{
    /**
     * @param mixed $status
     * @return $this
     */
    function setStatus($status);

    /**
     * @return mixed
     */
    function getStatus();

    /**
     * @param mixed $message
     * @return $this
     */
    function setMessage($message);

    /**
     * @return mixed
     */
    function getMessage();

    /**
     * @param iRequest|null $request
     * @return $this
     */
    function setRequest(iRequest $request=null);

    /**
     * @return iRequest|null
     */
    function getRequest();

    /**
     * @param $gatewayResponses
     * @return $this
     */
    function setGatewayResponses($gatewayResponses);

    /**
     * @return mixed
     */
    function getGatewayResponses();


}