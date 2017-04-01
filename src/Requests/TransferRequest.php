<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 4/1/17
 * Time: 11:04 AM
 */

namespace Ashrafi\PaymentGateways\Requests;


use Ashrafi\PaymentGateways\AccountTrait;

class TransferRequest extends Request
{
    use AccountTrait;

    protected $payer,$payee,$amount,$description='',$inputs=[];

    public function __construct($username=null,$password=null,$orderId=0,$gatewayOrderId=null){
        parent::__construct($orderId,$gatewayOrderId);
        $this->setUsername($username)->setPassword($password);
    }

    /**
     * @return mixed
     */
    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * @param mixed $payer
     * @return $this
     */
    public function setPayer($payer)
    {
        $this->payer = $payer;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPayee()
    {
        return $this->payee;
    }

    /**
     * @param mixed $payee
     * @return $this
     */
    public function setPayee($payee)
    {
        $this->payee = $payee;
        return $this;
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
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }


    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
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