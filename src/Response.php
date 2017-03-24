<?php
/**
 * Created by PhpStorm.
 * User: ashrafimanesh
 * Date: 3/23/17
 * Time: 11:43 PM
 */

namespace Ashrafi\PaymentGateways;


class Response
{
    use Collection;

    const SuccessPayRequest='SuccessPayRequest';
    const SuccessPayResponse='SuccessPayResponse';
    const SuccessPaid='SuccessPaid';
    const FailedPay='FailedPay';
    const SuccessConfirmRequest='SuccessConfirmRequest';
    const SuccessConfirm='SuccessConfirm';
    const FailedConfirm='FailedConfirm';

    protected $request,$code,$message,$status,$gatewayResponses,$html;

    /**
     * @param bool|false $status
     * @param string $message
     * @param int $code
     */
    public function __construct(Request $request=null,$status=false,$message='',$code=1){
        $this->setRequest($request)->setStatus($status)->setCode($code)->setMessage($message)->setOrderId($request->getOrderId())->setGatewayOrderId($request->getGatewayOrderId());
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @param mixed $html
     * @return Response
     */
    public function setHtml($html)
    {
        $this->html = $html;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     * @return Response
     */
    public function setRequest(Request $request=null)
    {
        $this->request = $request;
        return $this;
    }


    /**
     * @param mixed $gatewayResponses
     * @return CallbackResponse
     */
    public function setGatewayResponses($gatewayResponses)
    {
        $this->gatewayResponses = $gatewayResponses;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGatewayResponses()
    {
        return $this->gatewayResponses;
    }


}