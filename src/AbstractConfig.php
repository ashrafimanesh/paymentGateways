<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 4/11/17
 * Time: 11:20 AM
 */

namespace Ashrafi\PaymentGateways;

abstract class AbstractConfig implements iConfig
{
    protected $handler=null;
    protected $configs=null;

    public function __construct($handler=null,$configs=null){
        $this->setHandler($handler)->setConfigs($configs);
    }

    /**
     * @return string|iModel
     */
    function getHandler()
    {
        return $this->handler;
    }

    function setConfigs($configs)
    {
        $this->configs=$configs;
        return $this;
    }

    function getConfigs()
    {
        return $this->configs;
    }

}