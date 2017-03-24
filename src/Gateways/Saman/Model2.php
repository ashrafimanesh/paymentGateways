<?php

namespace Ashrafi\PaymentGateways\Gateways;

use Ashrafi\PaymentGateways\iModel;

/**
 * Description of Model
 *
 * @author ramin ashrafimanesh <ashrafimanesh@gmail.com>
 */
class Model implements iModel{
    private $MID = '10560528', $terminalId = '670831', $userPassword = '50437', $orderId;
    private $server_url='http://fioff.ir/';
    
    private function _call2($url,$params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        // in real life you should use something like:
        // curl_setopt($ch, CURLOPT_POSTFIELDS, 
        //          http_build_query(array('postvar1' => 'value1')));

        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $exec=curl_exec ($ch);
//        $exec = json_decode($exec,true);
        curl_close ($ch);
        return $exec;
    }
    /**
     * @return \SoapClient
     */
    private function getClient($url)
    {
        $context = stream_context_create(array('ssl' => array('verify_peer' => false, 'allow_self_signed' => true)));

        return new \SoapClient($url, ['stream_context' => $context,'trace' => true, 'exceptions' => true]);
    }

    private function _call($url,$params){

    }

    private function _token($inputs){
        $result=null;
        if(isset($inputs['amount']) && isset($inputs['orderId'])) {
            $request = [
                'TermID' => $this->MID,
                'TotalAmount' => ($inputs['amount']),
                'ResNum' => $inputs['orderId'],
            ];
            $client = $this->getClient('https://sep.shaparak.ir/Payments/InitPayment.asmx?WSDL');
            $i = 0;
            while ($i < 3) {
                $i++;
                $result = $client->RequestToken($request['TermID'], $request['ResNum'], $request['TotalAmount']);
                if (!strpos(" " . $result, '/')) {
                    $i = 10;
                    break;
                }
            }
        }
        return $result;
    }

    private function _pay($inputs){
        $html='<form action="https://sep.shaparak.ir/Payment.aspx" method="POST" id="myForm">';
        $html.= '<input type="hidden" name="Token" value="'.urlencode($inputs['token']).'"/>';
        $html.= '<input type="hidden" name="RedirectURL" value="'.$inputs['callbackUrl'].'"/>';
        $html.= '</form>';
        $html.= '<script>document.getElementById("myForm").submit();</script>';
        return $html;
    }

    private function _verify($inputs){
        $result=null;
        if(isset($inputs['RefNum'])) {
            $client = $this->getClient('https://sep.shaparak.ir/payments/referencepayment.asmx?WSDL');
            $result = $client->verifyTransaction($inputs['RefNum'], $this->MID);
        }
        return $result;
    }


    /**
     * call pay webservice
     */
    function pay($inputs=array()){
        $result=$this->_token($inputs);
//        $result=  $this->_call($this->server_url.'saman/token.php', ['orderID'=>$inputs['orderId'],'amount'=>$inputs['amount']]);
        if(strlen($result)>10)
        {
            $response['refId']=$result;
            $result=$this->_pay(['token'=>$result,'callbackUrl'=>$inputs['callbackUrl']]);
//            $result=  $this->_call($this->server_url.'saman/pay.php', ['token'=>$result,'callbackUrl'=>$inputs['callbackUrl']]);
            $response['status'] = true;
            $response['redirect_form']=$result;
            return $response;
        }
        $response['status'] = false;
        //-- نمایش خطا
        $response['message'] = $result;
        return $response;
    }
    
    /**
     * call confirm webservice
     */
    function confirm($inputs=[]){
        $response=['status'=>false,'message'=>''];
        if(isset($inputs['StateCode']) && $inputs['StateCode']==0)
        {
//            $result= $this->_call($this->server_url.'saman/verify.php', ['RefNum'=>$inputs['RefNum']]);
//            $result= (int)(json_decode($result, true));
            $result=(int)($this->_verify(['RefNum'=>$inputs['RefNum']]));
            $response['data']=$result;
            if($result>0)
            {
                $response['message']=$result;
                $response['status']=true;
            }
                
        }
        
        return $response;
    }
}
