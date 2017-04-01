<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 3/24/17
 * Time: 9:50 AM
 */

namespace Ashrafi\PaymentGateways;


use Ashrafi\PaymentGateways\Requests\Request;

trait Collection
{
    use Convert;
    protected $orderId,$gatewayOrderId;

    /**
     * @return $this
     */
    public function save(){
        $this->created_at=time();
        $ex=explode('\\',get_class($this));
        $function=end($ex);
        $logPath = self::getLogPath($this->getOrderId());
        file_put_contents($logPath.'/'.$function.'.log',json_encode($this->toArray(),JSON_UNESCAPED_UNICODE).PHP_EOL, LOCK_EX);
        $this->log=['path'=>$logPath,'file'=>$function.'.log'];
        return $this;
    }

    /**
     * @param $status
     * @return $this
     */
    public function saveFinalStatus($status){
        if(isset($this->log) && isset($this->log['path'])){
            file_put_contents($this->log['path'].'/final_status',$status,LOCK_EX);
        }
        return $this;
    }

    public function getFinalStatus(){
        $logPath=self::getLogPath($this->getOrderId());
        return file_exists($logPath.'/final_status') ? file_get_contents($logPath.'/final_status') : null;
    }

    public static function getSavedResponse(Request $request,$class=null){
        $logPath=self::getLogPath($request->getOrderId());
        $ex=explode('\\',$class);
        $function=end($ex);
        if(!file_exists($logPath.'/'.$function.'.log')){
            return null;
        }
        $result=json_decode(file_get_contents($logPath.'/'.$function.'.log'),LOCK_EX);
        if(!$result){
            return null;
        }
        $obj=new $class($request);
        foreach($result as $key=>$value){
            $obj->$key=$value;
        }
        return $obj;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGatewayOrderId()
    {
        return $this->gatewayOrderId;
    }

    /**
     * @param mixed $gatewayOrderId
     * @return $this
     */
    public function setGatewayOrderId($gatewayOrderId)
    {
        $this->gatewayOrderId = $gatewayOrderId;
        return $this;
    }

    /**
     * @return string
     */
    public static function getLogPath($orderId)
    {
        $config = require(__DIR__ . '/config/app.php');
        $logPath = rtrim($config['logPath'], '/') . '/' . $orderId;
        if (!is_dir($logPath)) {
            mkdir($logPath, 0777, true);
            return $logPath;
        }
        return $logPath;
    }
}