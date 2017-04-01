<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 3/23/17
 * Time: 11:43 PM
 */

namespace Ashrafi\PaymentGateways\Requests;


class PayRequest extends Request
{

    protected $amount,$callbackUrl,$userCallbackUrl,$inputs = array();

    /**
     * @param int $amount
     * @param null $customerCallbackUrl
     * @param int $orderId
     * @param array $inputs
     */
    public function __construct($amount,$customerCallbackUrl='',$orderId=0,$inputs=[]){
        parent::__construct($orderId);
        $this->setAmount($amount)->setUserCallbackUrl($customerCallbackUrl)
            ->setInputs($inputs);
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     * @return Request
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * @param mixed $callbackUrl
     * @return Request
     */
    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;
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
     * @return Request
     */
    public function setInputs($inputs)
    {
        $this->inputs = $inputs;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserCallbackUrl()
    {
        return $this->userCallbackUrl;
    }

    /**
     * @param mixed $userCallbackUrl
     * @return Request
     */
    public function setUserCallbackUrl($userCallbackUrl)
    {
        $this->userCallbackUrl = $userCallbackUrl;
        return $this;
    }



}