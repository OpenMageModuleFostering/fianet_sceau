<?php


abstract class Fianet_Sceau_Block_Widget_Abstract extends Mage_Core_Block_Template
{
   
    abstract public function canDisplay();
    abstract public function isActive();
    
    public function isModuleActive()
    {
        if (Mage::getStoreConfig('sceau/sceauconfg/active') == '1')
        {
            return true;
        }
        return false;
    }
  
   
    
    public function getSiteId()
    {
        return Fianet_Sceau_Helper_Data::getSiteID();
    }
}