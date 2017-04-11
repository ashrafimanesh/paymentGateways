<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 4/1/17
 * Time: 11:48 AM
 */

namespace Ashrafi\PaymentGateways\Gateways\PerfectMoney;

use Ashrafi\PaymentGateways\Model as PaymentGatewayModel;
use Ashrafi\PaymentGateways\Requests\BalanceRequest;
use Ashrafi\PaymentGateways\Requests\CallbackRequest;
use Ashrafi\PaymentGateways\Requests\ConfirmRequest;
use Ashrafi\PaymentGateways\Requests\PayRequest;
use Ashrafi\PaymentGateways\Requests\TransferRequest;
use Ashrafi\PaymentGateways\Responses\BalanceResponse;
use Ashrafi\PaymentGateways\Responses\CallbackResponse;
use Ashrafi\PaymentGateways\Responses\ConfirmResponse;
use Ashrafi\PaymentGateways\Responses\Response;
use Ashrafi\PaymentGateways\Responses\TransferResponse;
use Ashrafi\PhpConnectors\AbstractConnectors;
use Ashrafi\PhpConnectors\ConnectorFactory;
use Ashrafi\PhpConnectors\CurlConnector;

class Model extends PaymentGatewayModel
{
    protected $accountId = '',$passPhrase='';

    /**
     * set private configs
     * @return mixed
     */
    protected function _initConfigs()
    {
        $this->setAccountId($this->config['accountId']);
        $this->setPassPhrase($this->config['passPhrase']);
        if(!$this->getAccountId()){
            throw new \Exception('Please set PerfectMoney accountId in config. Read config/app.php for more detail');
        }
        if(!$this->getPassPhrase()){
            throw new \Exception('Please set PerfectMoney passPhrase in config. Read config/app.php for more detail');
        }
    }

    protected function _pay(PayRequest $payRequest,Response $payResponse)
    {
        $url='https://perfectmoney.is/acct/ev_create.asp';
        $authParams=$this->_getAuthParams();
        $client=$this->_getConnector($url,CurlConnector::class);
        $inputs=$payRequest->getInputs();
        if(!isset($inputs['payerAccountId']) || !$inputs['payerAccountId']){
            throw new \Exception('Please set payerAccountId with call $payRequest->setInputs([\'payerAccountId\'=>\'\'])');
        }
        $out=$client->run('',array_merge($authParams,['Amount'=>$payRequest->getAmount(),'Payer_Account'=>$inputs['payerAccountId']]));

        $ar = $this->_parseResponse($out);

        $payResponse->setGatewayResponses($ar);

        if(isset($ar['ERROR'])){
            $payResponse->setStatus(false)->setMessage($ar['ERROR']);
        }
        else{
            $payResponse->setStatus(true)->setGatewayOrderId($ar['VOUCHER_NUM']);
        }
        return $payResponse;
    }

    protected function _callback(CallbackRequest $callbackRequest,CallbackResponse $callbackResponse)
    {
        $callbackResponse->setStatus(true);
        return $callbackResponse;
    }

    protected function _confirm(ConfirmRequest $confirmRequest,ConfirmResponse $confirmResponse)
    {
        $url='https://perfectmoney.is/acct/ev_activate.asp';
        $client=$this->_getConnector($url,CurlConnector::class);

        $inputs=$confirmRequest->getInputs();
        if(!isset($inputs['VOUCHER_CODE']) || !$inputs['VOUCHER_CODE']){
            throw new \Exception('Invalid VOUCHER_CODE. Please Set $confirmRequest->setInputs([\'VOUCHER_CODE\'=>\'_code_\'])');
        }
        $authParams=$this->_getAuthParams();
        $out=$client->run('',array_merge($authParams,['Payee_Account'=>$confirmRequest->getAccountId(),'ev_number'=>$confirmRequest->getGatewayOrderId(),'ev_code'=>$inputs['VOUCHER_CODE']]));

        $ar = $this->_parseResponse($out);

        $confirmResponse->setGatewayResponses($ar);

        if(isset($ar['ERROR'])){
            $confirmResponse->setStatus(false)->setMessage($ar['ERROR']);
        }
        else{
            $confirmResponse->setStatus(true)->setGatewayOrderId($ar['VOUCHER_NUM'])->setAccountId($ar['Payee_Account'])->setAmount($ar['VOUCHER_AMOUNT']);
        }

        return $confirmResponse;
    }

    protected function _getBalance(BalanceRequest $balanceRequest=null,BalanceResponse $balanceResponse)
    {
        $url='https://perfectmoney.is/acct/balance.asp';

        $client=$this->_getConnector($url,CurlConnector::class);
        $out=$client->run('', $this->_getAuthParams());
        // searching for hidden fields
        $ar = $this->_parseResponse($out);
        $balanceResponse->setGatewayResponses($ar);
        if(isset($ar['ERROR'])){
            $balanceResponse->setStatus(false)->setMessage($ar['ERROR']);
            return $balanceResponse;
        }

        foreach($ar as $accountId=>$balance){
            $balanceResponse->addAccount($accountId,$balance,($accountId[0]=='U' ? 'USD' : ($accountId[0]=='E' ? 'EUR' : ($accountId[0]=='B' ? 'BTC' : '' ) )));
        }

        return $balanceResponse;
    }

    protected function _transfer(TransferRequest $transferRequest,TransferResponse $transferResponse)
    {
        $transferRequest->setUsername($this->getAccountId())->setPassword($this->getPassPhrase());

        $inputs=$transferRequest->getInputs();

        $url='https://perfectmoney.is/acct/confirm.asp';
        $client=$this->_getConnector($url,CurlConnector::class);

        $authParams=$this->_getAuthParams();
        $params=array_merge($authParams,[
                'Payer_Account'=>$transferRequest->getPayer(),
            'Payee_Account'=>$transferRequest->getPayee(),
            'Amount'=>$transferRequest->getAmount(),
            'PAYMENT_ID'=>$transferRequest->getOrderId()
        ]);
        $out=$client->run('',$params+$inputs);

        $ar = $this->_parseResponse($out);

        $transferResponse->setGatewayResponses($ar);

        if(isset($ar['ERROR'])){
            $transferResponse->setStatus(false)->setMessage($ar['ERROR']);
        }
        else{
            $transferResponse->setStatus(true);
        }

        return $transferResponse;
    }

    /**
     * @return array
     */
    protected function _getAuthParams()
    {
        return ['AccountID' => $this->getAccountId(), 'PassPhrase' => $this->getPassPhrase()];
    }

    /**
     * @param $out
     * @return string
     * @throws \Exception
     */
    protected function _parseResponse($out)
    {
// searching for hidden fields
        if (!preg_match_all("/<input name='(.*)' type='hidden' value='(.*)'>/", $out, $result, PREG_SET_ORDER)) {
            throw new \Exception('Invalid output');
        }
        $ar = "";
        foreach ($result as $item) {
            $key = $item[1];
            $ar[$key] = $item[2];
        }
        return $ar;
    }

    /**
     * @return string
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param string $accountId
     * @return $this
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassPhrase()
    {
        return $this->passPhrase;
    }

    /**
     * @param string $passPhrase
     * @return $this
     */
    public function setPassPhrase($passPhrase)
    {
        $this->passPhrase = $passPhrase;
        return $this;
    }
}