<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 4/11/17
 * Time: 1:15 PM
 */

namespace Ashrafi\PaymentGateways\Gateways\Mellat;


use Ashrafi\PaymentGateways\AbstractConfig;

class MellatConfig extends AbstractConfig
{

    function setHandler($handlerClass)
    {
        $this->handler=$handlerClass?:Model::class;
        return $this;
    }
}