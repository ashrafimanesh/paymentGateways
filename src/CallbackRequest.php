<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 3/24/17
 * Time: 3:30 PM
 */

namespace Ashrafi\PaymentGateways;


class CallbackRequest extends Request
{
    protected $gatewayResponses;

    public function __construct($orderId=0,$gatewayOrderId=null,$gatewayResponses=[]){
        parent::__construct($orderId,$gatewayOrderId);
        $this->setGatewayResponses($gatewayResponses);
    }

    /**
     * @return mixed
     */
    public function getGatewayResponses()
    {
        return $this->gatewayResponses;
    }

    /**
     * @param mixed $gatewayResponses
     * @return CallbackRequest
     */
    public function setGatewayResponses($gatewayResponses)
    {
        $this->gatewayResponses = $gatewayResponses;
        return $this;
    }

}