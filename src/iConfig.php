<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 4/11/17
 * Time: 10:59 AM
 */

namespace Ashrafi\PaymentGateways;


interface iConfig
{
    function setHandler($handlerClass);

    /**
     * @return string|iModel
     */
    function getHandler();

    function setConfigs($configs);

    function getConfigs();

}