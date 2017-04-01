<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 3/24/17
 * Time: 2:02 PM
 */

namespace Ashrafi\PaymentGateways\Responses;


class ConfirmResponse extends Response
{
    protected $amount,$accountId=null;

    /**
     * @param Request $request
     * @param bool|false $status
     * @param string $message
     * @param int $code
     */
    public function __construct(Request $request=null,$status=false,$message='',$code=1){
        parent::__construct($request,$status,$message,$code);
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
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



}