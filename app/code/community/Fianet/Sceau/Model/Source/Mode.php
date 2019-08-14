<?php

/**
 * 2000-2012 FIA-NET
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is available
 * through the world-wide-web at this URL: http://www.opensource.org/licenses/OSL-3.0
 * If you are unable to obtain it through the world-wide-web, please contact us
 * via http://www.fia-net-group.com/formulaire.php so we can send you a copy immediately.
 *
 *  @author Quadra Informatique <ecommerce@quadra-informatique.fr>
 *  @copyright 2000-2012 FIA-NET
 *  @version Release: $Revision: 0.2.0 $
 *  @license http://www.opensource.org/licenses/OSL-3.0  Open Software License (OSL 3.0)
 */
class Fianet_Sceau_Model_Source_Mode {

    const MODE_PROD = 'prod';
    const MODE_PREPROD = 'test';
    
    public function toOptionArray() {
        return array(
            array('value' => self::MODE_PREPROD, 'label' => Mage::helper('adminhtml')->__('Test')),
            array('value' => self::MODE_PROD, 'label' => Mage::helper('adminhtml')->__('Production')),
        );
    }

}
