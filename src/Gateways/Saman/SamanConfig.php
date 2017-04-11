<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 4/11/17
 * Time: 11:18 AM
 */

namespace Ashrafi\PaymentGateways\Gateways\Saman;


use Ashrafi\PaymentGateways\AbstractConfig;

class SamanConfig extends AbstractConfig
{

    function setHandler($handlerClass)
    {
        $this->handler=$handlerClass?:Model::class;
        return $this;
    }
}