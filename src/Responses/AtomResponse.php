<?php
/**
 * Created by PhpStorm.
 * User: ashrafimanesh@gmail.com
 * Date: 4/1/17
 * Time: 9:02 AM
 */

namespace Ashrafi\PaymentGateways\Responses;


trait AtomResponse
{
    protected $status=false,$message='',$gatewayResponses;

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }


    /**
     * @param $gatewayResponses
     * @return Response
     */
    public function setGatewayResponses($gatewayResponses)
    {
        $this->gatewayResponses = $gatewayResponses;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGatewayResponses()
    {
        return $this->gatewayResponses;
    }
}