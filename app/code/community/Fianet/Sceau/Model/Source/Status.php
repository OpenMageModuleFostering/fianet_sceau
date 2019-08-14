<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Fianet_Sceau_Model_Source_Status
{
    public static function toOptionArray() {
        $statuses = Mage::getModel('sales/order_status')->getCollection()->load();
        
        $data = array();
        
        foreach ($statuses as $status)
        {
            $data[] = array('label' => $status->getLabel(), 'value' => $status->getStatus());
        }
        
        
        
        return $data;
    }
}