<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 4/1/17
 * Time: 8:30 AM
 */

namespace Ashrafi\PaymentGateways\Requests;


class BalanceRequest
{
    protected $accountId,$username,$password,$currency=null;

    public function __construct($username,$password,$accountId=null,$currency=null){
        $this->setUsername($username)->setPassword($password)->setAccountId($accountId)->setCurrency($currency);
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     * @return BalanceRequest
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
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
     * @return BalanceRequest
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return BalanceRequest
     */
    public function setPassword($password)
    {
        $this->password = $password;
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
     * @param null $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

}