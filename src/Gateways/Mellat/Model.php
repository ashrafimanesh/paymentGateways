<?php

namespace Ashrafi\PaymentGateways\Gateways\Mellat;

use Ashrafi\PaymentGateways\Requests\BalanceRequest;
use Ashrafi\PaymentGateways\Requests\TransferRequest;
use Ashrafi\PaymentGateways\Responses\BalanceResponse;
use Ashrafi\PaymentGateways\Requests\CallbackRequest;
use Ashrafi\PaymentGateways\Responses\CallbackResponse;
use Ashrafi\PaymentGateways\Requests\ConfirmRequest;
use Ashrafi\PaymentGateways\Responses\ConfirmResponse;
use Ashrafi\PaymentGateways\Model as PaymentGatewayModel;
use Ashrafi\PaymentGateways\Requests\PayRequest;
use Ashrafi\PaymentGateways\Responses\Response;
use Ashrafi\PaymentGateways\Responses\TransferResponse;
use Ashrafi\PhpConnectors\AbstractConnectors;
use Ashrafi\PhpConnectors\NusoapConnector;
use Ashrafi\PhpConnectors\SoapConnector;
class Model extends PaymentGatewayModel
{
    private $userName, $terminalId, $userPassword ;

    protected function _initConfigs(){
        $this->userName=$this->config['userName'];
        $this->terminalId=$this->config['terminalId'];
        $this->userPassword=$this->config['userPassword'];
        if(!$this->userName){
            throw new \Exception('Please set mellat userName value in config. Read config/app.php for more detail');
        }
    }

    /**
     * @param PayRequest $payRequest
     * @param Response $payResponse
     * @return Response
     */
    protected function _pay(PayRequest $payRequest,Response $payResponse)
    {
        //-- تبدیل اطلاعات به آرایه برای ارسال به بانک
        $parameters = $this->_getPayParams($payRequest);
        /**
         * get bank connection object
         */
        try{
            $url='https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl';

            $client=$this->_getConnector($url,NusoapConnector::class);

            $namespace = 'http://interfaces.core.sw.bps.com/';
            $result = $client->run('bpPayRequest', [$parameters, $namespace]);
            $payResponse->setGatewayResponses($result);
        } catch (\Exception $ex) {
            $payResponse->setStatus(false);
            $payResponse->setMessage("There was a problem connecting to Bank");
            return $payResponse;
        }
        $res = explode(',', $result);
        $ResCode = $res[0];
        if ($ResCode == "0") {
            $payResponse->setStatus(true);
            $payResponse->setGatewayOrderId($res[1]);
            //-- انتقال به درگاه پرداخت
            $payResponse->setHtml('<form name="myform" action="https://bpm.shaparak.ir/pgwchannel/startpay.mellat" method="POST">
                                        <input type="hidden" id="RefId" name="RefId" value="' . $res[1] . '">
                                </form>
                                <script type="text/javascript">window.onload = formSubmit; function formSubmit() { document.forms[0].submit(); }</script>');
        } else {
            $payResponse->setStatus(false)->setMessage(-1*$result);
        }

        /**
         * call bank payment method
         */
        return $payResponse;
    }

    /**
     * @param CallbackRequest $callbackRequest
     * @param CallbackResponse $callbackResponse
     * @return CallbackResponse
     * @throws \Exception
     */
    protected function _callback(CallbackRequest $callbackRequest,CallbackResponse $callbackResponse)
    {
        $inputs=$callbackRequest->getGatewayResponses();
        if(!isset($inputs['SaleOrderId']) || !isset($inputs['SaleReferenceId']) || !isset($inputs['orderId'])){
            $callbackResponse->setStatus(false);
            $callbackResponse->setMessage('Invalid Response');
        }
        switch(true){
            case !$inputs['SaleOrderId'] || !$inputs['SaleReferenceId'] || !$inputs['orderId']:
                $callbackResponse->setStatus(false);
                $callbackResponse->setMessage('Invalid Response');
                break;
            case $inputs['SaleOrderId'] && $inputs['SaleReferenceId']:
                $callbackResponse->setStatusCode(CallbackResponse::Success)->setStatus(true)->setGatewayOrderId($inputs['SaleReferenceId']);
                break;

        }
        return $callbackResponse;
    }

    /**
     * @param ConfirmRequest $confirmRequest
     * @param ConfirmResponse $confirmResponse
     * @return ConfirmResponse
     */
    protected function _confirm(ConfirmRequest $confirmRequest,ConfirmResponse $confirmResponse)
    {
        $result=-1;
        if($confirmRequest->getGatewayOrderId()) {

            try{
                $url = 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl';
                $client=$this->_getConnector($url,NusoapConnector::class);

                $namespace = 'http://interfaces.core.sw.bps.com/';
                $parameters = array(
                    'terminalId' => $this->terminalId,
                    'userName' => $this->userName,
                    'userPassword' => $this->userPassword,
                    'orderId' => $confirmRequest->getOrderId(),
                    'saleOrderId' => '',
                    'saleReferenceId' => $confirmRequest->getGatewayOrderId());

                $result = $client->run('bpVerifyRequest',[$parameters, $namespace]);
            }catch (\Exception $ex){
                $confirmResponse->setStatus(false);
                $confirmResponse->setMessage("There was a problem connecting to Bank");
                return $confirmResponse;
            }
        }
        $confirmResponse->setGatewayResponses($result);


        if($result == 0) {
            //-- وریفای به درستی انجام شد٬ درخواست واریز وجه
            // Call the SOAP method
            $result = $client->run('bpSettleRequest', [$parameters, $namespace]);

            $confirmResponse->setGatewayResponses($result);

            if($result == 0) {
                //-- تمام مراحل پرداخت به درستی انجام شد.
                //-- آماده کردن خروجی
                $confirmResponse->setStatus(true)->setMessage('The transaction was successful');
            } else {
                //-- در درخواست واریز وجه مشکل به وجود آمد. درخواست بازگشت وجه داده شود.
                $client->run('bpReversalRequest',[$parameters, $namespace]);
                $confirmResponse->setStatus(false)->setMessage((-1*$result));
            }
        } else {
            //-- وریفای به مشکل خورد٬ نمایش پیغام خطا و بازگشت زدن مبلغ
            $client->run('bpReversalRequest', [$parameters, $namespace]);
            $confirmResponse->setStatus(false)->setMessage((-1*$result));
        }
        return $confirmResponse;
    }

    /**
     * @param BalanceRequest $balanceRequest
     * @param BalanceResponse $balanceResponse
     * @return BalanceResponse
     */
    protected function _getBalance(BalanceRequest $balanceRequest=null,BalanceResponse $balanceResponse)
    {
        $balanceResponse->setStatus(false)->setMessage('Method '.__FUNCTION__.' does not exist in mellat gateway');
        return $balanceResponse;
    }

    /**
     * @param TransferRequest $transferRequest
     * @param TransferResponse $transferResponse
     * @return TransferResponse
     */
    protected function _transfer(TransferRequest $transferRequest,TransferResponse $transferResponse)
    {
        $transferResponse->setStatus(false)->setMessage('Method '.__FUNCTION__.' does not exist in mellat gateway');
        return $transferResponse;
    }

    /**
     * @param PayRequest $payRequest
     * @return array
     */
    private function _getPayParams(PayRequest $payRequest)
    {
        $parameters = array(
            'terminalId' => $this->terminalId,
            'userName' => $this->userName,
            'userPassword' => $this->userPassword,
            'orderId' => (int)$payRequest->getOrderId(),
            'amount' => $payRequest->getAmount(), // Price / Rial
            'localDate' => date('Ymd'),
            'localTime' => date('Gis'),
            'additionalData' => '',
            'callBackUrl' => $payRequest->getCallbackUrl(),
            'payerId' => 0);
        return $parameters;
    }

}