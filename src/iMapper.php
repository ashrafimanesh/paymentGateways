<?php

namespace Ashrafi\PaymentGateways;

/**
 * Description of MapperAbst
 *
 * @author ramin ashrafimanesh <ashrafimanesh@gmail.com>
 */
interface iMapper {
    /**
     * convert standard pay inputs to bank special pay inputs
     */
    function payRequest($inputs=[]);
    
    /**
     * convert bank special pay outputs to standard pay outputs
     * @return object PayResponce
     */
    function payResponce($inputs=[]);
    
    /**
     * convert standard confirm inputs to bank special confirm inputs
     */
    function confirmReqeust($inputs=[]);
    
    /**
     * convert bank special confirm outputs to standard confirm outputs
     */
    function confirmResponce($inputs=[]);
}
