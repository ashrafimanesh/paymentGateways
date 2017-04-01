<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 4/1/17
 * Time: 11:28 AM
 */

namespace Ashrafi\PaymentGateways\Requests;


use Ashrafi\PaymentGateways\AccountTrait;

class HistoryRequest
{
    use AccountTrait;
    protected $startDate=null,$endDate=null,$accountId=null;

    public function __construct($username,$password,$accountId=null){
        $this->setUsername($username)->setPassword($password)->setAccountId($accountId);
    }

    /**
     * @return null
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param null $startDate
     * @return HistoryRequest
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return null
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param null $endDate
     * @return HistoryRequest
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
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
     * @return HistoryRequest
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
        return $this;
    }
}