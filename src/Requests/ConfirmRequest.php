<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 3/24/17
 * Time: 1:13 PM
 */

namespace Ashrafi\PaymentGateways\Requests;


class ConfirmRequest extends Request
{
    public $accountId=null,$inputs=[];
    public function __construct($orderId,$gatewayOrderId=null){
        $this->setOrderId($orderId);
        $this->setGatewayOrderId($gatewayOrderId);
    }

    /**
     * @return null
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param null $accountId
     * @return $this
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
        return $this;
    }

    /**
     * @return array
     */
    public function getInputs()
    {
        return $this->inputs;
    }

    /**
     * @param array $inputs
     * @return $this
     */
    public function setInputs($inputs)
    {
        $this->inputs = $inputs;
        return $this;
    }

}