<?php
/**
 * Created by PhpStorm.
 * User: ashrafimanesh@gmail.com
 * Date: 4/1/17
 * Time: 10:04 AM
 */

require_once __DIR__.'/loader.php';

$wsdl_url='https://perfectmoney.is/acct/verify.asp';
$connector=\Ashrafi\PhpConnectors\ConnectorFactory::create(\Ashrafi\PhpConnectors\CurlConnector::class
    ,$wsdl_url
    ,'http://ashrafimanesh.ir/proxy/curl.php'
    ,''
    ,\Ashrafi\PhpConnectors\AbstractConnectors::ProxyTypeUrl);


$transferRequest=new \Ashrafi\PaymentGateways\Requests\TransferRequest('4389262','ziiziegv@2','a'.uniqid());



var_dump(htmlspecialchars($connector->run('',$request)));