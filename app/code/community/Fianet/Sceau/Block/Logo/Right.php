<?php


class Fianet_Sceau_Block_Logo_Right extends Fianet_Sceau_Block_Logo_Abstract
{
    CONST BLOCK_CACHE_KEY_PREFIX = 'fianet_sceau_logo_block_frontend_right_';
    CONST CONFIG_PATH_BLOCK_ACTIVE = 'sceau/logoconf/logo_right_position';
    
    function _construct()
    {
        parent::_construct();
        
        $this->setCacheKey(self::BLOCK_CACHE_KEY_PREFIX . Mage::app()->getStore()->getCode());
        $this->setCacheTags(array(Mage_Core_Block_Abstract::CACHE_GROUP));
        $this->setCacheLifetime(60*60*24*30);
        
    }
    
    
    public function canDisplay()
    {
        
        return ($this->isModuleActive() && $this->isActive());
    }
    
    
    
    public function isActive()
    {
        if (Mage::getStoreConfig(self::CONFIG_PATH_BLOCK_ACTIVE) == '1')
        {
            return true;
        }
        return false;
    }
   
    
}