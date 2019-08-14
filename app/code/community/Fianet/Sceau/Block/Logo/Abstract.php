<?php


abstract class Fianet_Sceau_Block_Logo_Abstract extends Mage_Core_Block_Template
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
  
    public function getLoginSceau()
    {
       return Mage::getStoreConfig('sceau/sceauconfg/login');
    }

}