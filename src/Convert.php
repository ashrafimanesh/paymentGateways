<?php
/**
 * Created by PhpStorm.
 * User: sonaa
 * Date: 3/24/17
 * Time: 1:14 PM
 */

namespace Ashrafi\PaymentGateways;


trait Convert
{
    public function toArray(){
        return get_object_vars($this);
    }

}