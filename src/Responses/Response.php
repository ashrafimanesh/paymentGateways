<?php
/**
 * Created by PhpStorm.
 * User: ashrafimanesh
 * Date: 3/23/17
 * Time: 11:43 PM
 */

namespace Ashrafi\PaymentGateways\Responses;


use Ashrafi\PaymentGateways\Collection;
use Ashrafi\PaymentGateways\iRequest;
use Ashrafi\PaymentGateways\iResponse;
use Ashrafi\PaymentGateways\Requests\Request;

class Response implements iResponse
{
    use AtomResponse;
    use Collection;

    const TypeHtml=1;
    const TypeFormData=2;
    const SuccessPayRequest='SuccessPayRequest';
    const SuccessPayResponse='SuccessPayResponse';
    const SuccessPaid='SuccessPaid';
    const FailedPay='FailedPay';
    const SuccessConfirmRequest='SuccessConfirmRequest';
    const SuccessConfirm='SuccessConfirm';
    const FailedConfirm='FailedConfirm';

    protected $request,$code;
    public $html,$formData;

    /**
     * @param Request $request
     * @param bool|false $status
     * @param string $message
     * @param int $code
     */
    public function __construct(Request $request=null,$status=false,$message='',$code=1){
        $this->setRequest($request)->setCode($code)->setStatus($status)->setMessage($message);
        if($request instanceof Request){
            $this->setOrderId($request->getOrderId())->setGatewayOrderId($request->getGatewayOrderId());
        }
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param $code
     * @return Response
     */
    public function setCode($code)
    {
        $this->code = $code;
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
     * @return iRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param iRequest|null $request
     * @return $this
     */
    public function setRequest(iRequest $request=null)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormData()
    {
        return $this->formData;
    }

    /**
     * @param mixed $formData
     * @return Response
     */
    public function setFormData($formData)
    {
        $this->formData = $formData;
        return $this;
    }


    /**
     * copy Response class attributes to called class attributes
     * @param Response $response
     * @return mixed
     */
    public static function copy(Response $response){
        $class=get_called_class();
        $instance=new $class($response->getRequest());
        foreach($response as $key=>$value){
            $instance->$key=$value;
        }
        return $instance;
    }

}