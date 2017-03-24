<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 3/24/17
 * Time: 9:30 AM
 */

namespace Ashrafi\PaymentGateways;


class CallbackResponse extends Response
{
    const Success=1,CancelByUser=-1,Unknown=-1000;

    protected $statusCode,$cardNumber;

    public function __construct(Request $request){
        parent::__construct($request);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     * @return CallbackResponse
     */
    public function setStatusCode($statusCode)
    {
        if(!in_array($statusCode,$this->_allStatusCodes())){
            throw new \Exception('invalid status set in '.get_called_class());
        }
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * @param mixed $cardNumber
     * @return CallbackResponse
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    private function _allStatusCodes()
    {
        return [self::CancelByUser,self::Success,self::Unknown];
    }


}