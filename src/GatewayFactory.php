<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 4/11/17
 * Time: 10:52 AM
 */

namespace Ashrafi\PaymentGateways;


use Ashrafi\PaymentGateways\Requests\PayRequest;
use Ashrafi\PaymentGateways\Responses\Response;

class GatewayFactory
{
    protected static $object=null;

    protected $using=null,$current=null,$globalConfigs=[];

    /**
     * @var GatewayCollection
     */
    protected $collection;

    protected function __construct($gatewaysConfig,$globalConfigs=[]){
        $this->globalConfigs=$globalConfigs;
    }

    /**
     * @param $name
     * @return iModel
     */
    public function __get($name){
        $this->using=$name;
        return $this->_getGateway();
    }

    /**
     * is triggered when invoking inaccessible methods in an object context.
     *
     * @param $name string
     * @param $arguments array
     * @return mixed
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
     */
    function __call($name, $arguments)
    {
        $gateway=$this->_getGateway();
        if(!method_exists($gateway,$name)){
            throw new \InvalidArgumentException('Method does not exist in '.get_class($gateway));
        }
        return call_user_func_array([$gateway,$name],$arguments);
    }


    /**
     *
     * @param $gatewaysConfig ['gatewayUniqueName1'=> iConfig configs ,...]
     * @return GatewayFactory|null
     */
    public static function getInstance($gatewaysConfig,$globalConfigs=[]){
        if(!self::$object){
            $factory=new GatewayFactory($gatewaysConfig,$globalConfigs);
            self::$object=$factory;

            self::$object->setCollection(new GatewayCollection());
            array_walk($gatewaysConfig,[$factory,'registerGateway']);
        }
        return self::$object;
    }

    /**
     * @param iConfig $configs
     * @param $gatewayUniqueName
     */
    protected function registerGateway(iConfig $configs,$gatewayUniqueName){
        $gateway=$configs->getHandler();
        if(is_string($gateway)){
            $gateway=new $gateway();
        }
        if(!($gateway instanceof iModel)){
            throw new \InvalidArgumentException('Invalid Argument');
        }
        $gateway->setName($gatewayUniqueName);
        $gateway->setConfig($configs,$this->globalConfigs);
        $this->collection->add($gatewayUniqueName,$gateway);
    }

    public function using($gatewayUniqueName){
        $this->using=$gatewayUniqueName;
        return $this;
    }

    /**
     * @return GatewayCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param GatewayCollection $collection
     * @return $this
     */
    public function setCollection(GatewayCollection $collection)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @return iModel
     */
    protected function _getGateway()
    {
        $collection = $this->getCollection();

        $this->current = $this->using ? $this->using : $collection->key();

        return $collection[$this->current];
    }

}