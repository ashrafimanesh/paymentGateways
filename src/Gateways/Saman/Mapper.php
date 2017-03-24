<?php

namespace Ashrafi\PaymentGateways\Gateways\Saman;

use Ashrafi\PaymentGateways\iMapper;

/**
 * Description of Mapper
 *
 * @author ramin ashrafimanesh <ashrafimanesh@gmail.com>
 */
class Mapper implements iMapper{
    /**
     * convert standard pay inputs to bank special pay inputs
     */
    public function payRequest(PayRequest $payRequest){
        return array('amount'=>$payRequest->getAmount(),'callbackUrl'=>$payRequest->getCallbackurl(),'orderId'=>$payRequest->getOrderID());
    }
    
    /**
     * convert bank special pay outputs to standard pay outputs
     * @return object PayResponce
     */
    public function payResponce($inputs=[]){
        $payResponce=new PayResponce();
        $payResponce->setStatus($inputs['status']);
        if($inputs['status'])
        {
            $payResponce->setRefId($inputs['refId']);
        }
        else
        {
            $payResponce->setRefId(0);
            $payResponce->setErrorMessage($inputs['message']);
        }
        return $payResponce;
    }
    
    /**
     * convert standard confirm inputs to bank special confirm inputs
     */
    public function confirmReqeust($inputs=[]){
        
    }
    
    /**
     * convert bank special confirm outputs to standard confirm outputs
     */
    public function confirmResponce($inputs=[]){
        
    }
    
    public function bankResponse($inputs=array())
    {
        $result=['status'=>(!isset($inputs['State']) || $inputs['State']!="OK" || strlen($inputs['RefNum'])<1 ? false : true)
            ,'code'=>$inputs['StateCode']
            ,'bank_order_id'=>isset($inputs['RefNum']) ? $inputs['RefNum']: ''];
        return $result;
    }
}
