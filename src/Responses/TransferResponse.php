<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 4/1/17
 * Time: 11:09 AM
 */

namespace Ashrafi\PaymentGateways\Responses;


use Ashrafi\PaymentGateways\Requests\Request;

class TransferResponse extends Response
{
    protected $payer,$payee,$payeeInfo=null;
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
     * @return null
     */
    public function getPayeeInfo()
    {
        return $this->payeeInfo;
    }

    /**
     * @param null $payeeInfo
     * @return $this
     */
    public function setPayeeInfo($payeeInfo)
    {
        $this->payeeInfo = $payeeInfo;
        return $this;
    }
    
    


}