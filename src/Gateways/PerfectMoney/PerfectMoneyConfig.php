<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 4/11/17
 * Time: 3:00 PM
 */

namespace Ashrafi\PaymentGateways\Gateways\PerfectMoney;


use Ashrafi\PaymentGateways\AbstractConfig;

class PerfectMoneyConfig extends AbstractConfig
{

    function setHandler($handlerClass)
    {
        $this->handler=$handlerClass?:Model::class;
        return $this;
    }
}