<?php
namespace App\Modules\Payments\Mellat;

use App\Modules\Payments\iMapper;
use App\Modules\Payments\PayRequest;
use App\Modules\Payments\PayResponce;
/**
 * Description of samanMapper
 *
 * @author ramin ashrafimanesh <ashrafimanesh@gmail.com>
 */
class Mapper implements iMapper{
    
    public function confirmReqeust($inputs = array()) {
        
    }

    public function confirmResponce($inputs = array()) {
        
    }

    public function payRequest(PayRequest $payRequest) {
        return array('amount'=>$payRequest->getAmount(),'callbackUrl'=>$payRequest->getCallbackurl(),'orderId'=>$payRequest->getOrderID());
    }

    /**
     * 
     * @param type $inputs
     * @return \PayResponce
     */
    public function payResponce($inputs = array()) {
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
    
    public function bankResponse($inputs=array())
    {
        $result=['status'=>(!isset($inputs['ResCode']) || $inputs['ResCode']!=0 ? false : true)
            ,'code'=>-1*$inputs['ResCode']
            ,'bank_order_id'=>isset($inputs['SaleReferenceId']) ? $inputs['SaleReferenceId']: ''];
        return $result;
    }

}
