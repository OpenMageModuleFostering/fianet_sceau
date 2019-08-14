<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Order
 *
 * @author ahoury
 */
class Fianet_Sceau_Model_Order
{
    static function processOrderToFianet($order)
    {
        if (Fianet_Sceau_Helper_Data::sendOrderToFianet($this))
        {

            $attribut_sceau = Fianet_Sceau_Helper_Data::ORDER_ATTR_SCEAU_SENT_PPROD;

            if (Fianet_Sceau_Helper_Data::sendingMode($this) == Fianet_Sceau_Model_Source_Mode::MODE_PROD) {
                $attribut_sceau = Fianet_Sceau_Helper_Data::ORDER_ATTR_SCEAU_SENT_PROD;
            }
            $this->setData($attribut_sceau, '1');
            
            return true;
        }
        return false;
    }
}
