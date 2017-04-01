<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 4/1/17
 * Time: 8:51 AM
 */

namespace Ashrafi\PaymentGateways;


class AccountBalance
{
    protected $accountId,$balance=0,$currency=null;

    public function __construct($accountId,$balance=0,$currency=null){
        $this->setAccountId($accountId)->setBalance($balance)->setCurrency($currency);
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
     * @return AccountBalance
     */
    protected function setAccountId($accountId)
    {
        $this->accountId = $accountId;
        return $this;
    }

    /**
     * @return int
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param int $balance
     * @return AccountBalance
     */
    protected function setBalance($balance)
    {
        $this->balance = $balance;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     * @return AccountBalance
     */
    protected function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }
}