<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 4/1/17
 * Time: 8:30 AM
 */

namespace Ashrafi\PaymentGateways\Requests;


use Ashrafi\PaymentGateways\AccountTrait;

class BalanceRequest
{
    use AccountTrait;

    protected $accountId,$currency=null;

    public function __construct($accountId=null,$currency=null){
        $this->setAccountId($accountId)->setCurrency($currency);
    }

    /**
     * @return mixed
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param mixed $accountId
     * @return $this
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
        return $this;
    }

    /**
     * @return null
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

}