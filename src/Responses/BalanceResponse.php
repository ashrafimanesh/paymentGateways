<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 4/1/17
 * Time: 8:30 AM
 */

namespace Ashrafi\PaymentGateways\Responses;


use Ashrafi\PaymentGateways\AccountBalance;

class BalanceResponse
{
    use AtomResponse;

    protected $username,$accountsId=[];

    /**
     * @param $username
     * @param array $accountsId [[id=>0,balance=>0,currency=>'']]
     */
    public function __construct($username,$accountsId=[]){
        $this->setUsername($username);
        foreach($accountsId as $accountInfo){
            $this->addAccount($accountInfo['id'],
                isset($accountInfo['balance']) ? $accountInfo['balance'] : 0,
                isset($accountInfo['currency']) ? $accountInfo['currency'] : ''
            );
        }
    }

    /**
     * @param $accountId
     * @param int $balance
     * @param null $currency
     * @return AccountBalance
     */
    protected function addAccount($accountId,$balance=0,$currency=null){
        if(!isset($this->accountsId[$accountId])){
            $this->accountsId[$accountId]=new AccountBalance($accountId,$balance,$currency);
        }
        return $this->accountsId[$accountId];
    }

    /**
     * @param $accountId
     * @return AccountBalance|null
     */
    public function findAccount($accountId){
        return isset($this->accountsId[$accountId]) ? $this->accountsId[$accountId] : null;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param $username
     * @return $this
     */
    protected function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return array
     */
    public function getAccountsId()
    {
        return $this->accountsId;
    }

}
