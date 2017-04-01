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
use Ashrafi\PaymentGateways\Responses;
use Ashrafi\PaymentGateways\Responses\TransferResponse;
use Ashrafi\PhpConnectors\AbstractConnectors;
use Ashrafi\PhpConnectors\ConnectorFactory;
use Ashrafi\PhpConnectors\CurlConnector;

class Model extends PaymentGatewayModel
{
    private $accountId = '',$passPhrase='';

    public function __construct(){
        parent::__construct();
        $this->accountId=$this->config['gateways']['perfectMoney']['accountId'];
        $this->passPhrase=$this->config['gateways']['perfectMoney']['passPhrase'];
        if(!$this->accountId){
            throw new \Exception('Please set PerfectMoney accountId in config. Read config/app.php for more detail');
        }
        if(!$this->passPhrase){
            throw new \Exception('Please set PerfectMoney passPhrase in config. Read config/app.php for more detail');
        }
    }

    protected function _pay(PayRequest $payRequest)
    {
        // TODO: Implement _pay() method.
    }

    protected function _callback(CallbackRequest $callbackRequest)
    {
        // TODO: Implement _callback() method.
    }

    protected function _confirm(ConfirmRequest $confirmRequest)
    {
        // TODO: Implement _confirm() method.
    }

    protected function _getBalance(BalanceRequest $balanceRequest)
    {
        $balanceRequest->setUsername($this->accountId)->setPassword($this->passPhrase);
        $url='https://perfectmoney.is/acct/balance.asp';

        $client=$this->_getConnector($url,CurlConnector::class,AbstractConnectors::ProxyTypeUrl);
        $result=$client->run('',['AccountID'=>$this->accountId,'PassPhrase'=>$this->passPhrase]);

        $accountsInfo=$this->_parseBalanceResult($result);

        $balanceResponse=new Responses\BalanceResponse($balanceRequest->getUsername(),$accountsInfo);

        if($accountsInfo && sizeof($accountsInfo)){
            $balanceResponse->setStatus(true);
        }
        return $balanceResponse;
    }

    protected function _transfer(TransferRequest $transferRequest)
    {
        // TODO: Implement _transfer() method.
    }

    private function _parseBalanceResult($result)
    {
        $pageDom = new \DOMDocument();
        $result=mb_convert_encoding($result, 'HTML-ENTITIES', "UTF-8");
        $pageDom->loadHTML($result);

//        $html=str_get_html($result);
        $trs=$pageDom->getElementsByTagName('tr');
        $accountsInfo=[];
        if($trs->length>0){
            for($i=1;$i<$trs->length;$i++){
                $accountId=$trs[$i]->childNodes[0]->textContent;
                $balance=$trs[$i]->childNodes[1]->textContent;
                $accountsInfo[]=['id'=>$accountId,'balance'=>$balance,'currency'=>($accountId[0]=='U' ? 'USD' : ($accountId[0]=='E' ? 'EUR' : '' ))];
            }
        }
        return $accountsInfo;
    }

}