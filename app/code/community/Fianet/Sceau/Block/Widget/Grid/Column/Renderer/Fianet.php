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
 *  @version Release: $Revision: 0.0.13 $
 *  @license http://www.opensource.org/licenses/OSL-3.0  Open Software License (OSL 3.0)
 */
class Fianet_Sceau_Block_Widget_grid_column_renderer_fianet extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    const PICTO_KO = "ko.PNG";
    const PICTO_OK = "ok.PNG";
        
    public function render(Varien_Object $row) {
        $sent_prod = $row->getData('fianet_sceau_order_sent_prod');
        $sent_preprod = $row->getData('fianet_sceau_order_sent_preprod');   
        $sent_error = $row->getData('fianet_sceau_order_sent_error');
        if (!Mage::Helper('fianet_sceau/Data')->isModuleActive($row))
        {//si le module est désactivé sur le front de la commande
            return 'Module désactivé';
        }        
        
        
        
        $text_pprod = '';
        
        $icon = self::PICTO_KO;
       /*if($sent_preprod == 0 && $sent_prod==0){}*/
        if($sent_preprod == 1 && $sent_prod==0)
        {
            $icon = self::PICTO_OK;
            $text_pprod = " [Test]";
        }
        elseif($sent_preprod == 0 && $sent_prod==1)
        {
             $icon = self::PICTO_OK;        
        }
        elseif($sent_preprod == 1 && $sent_prod==1)
        {
             $icon = self::PICTO_OK;         
        }
        $html = "<img src=" . $this->getSkinUrl('images/sceau/' . $icon)." WIDTH=20 >".$text_pprod;
        //$html .= $row->getData('fianet_sceau_order_sent');

        $order= Mage::getModel('sales/order')->load($row->getId());
        
        /*
        Zend_Debug::dump($row->getData('fianet_sceau_order_sent_prod'));
        Zend_Debug::dump($row->getData('fianet_sceau_order_sent_preprod'));
        
        Zend_Debug::dump($order->getData('fianet_sceau_order_sent_prod'), 'order->fianet_sceau_order_sent_prod');
        Zend_Debug::dump($order->getData('fianet_sceau_order_sent_preprod'), 'order->fianet_sceau_order_sent_preprod');
        */
        if($sent_error==1)
        $html .= "<img src=" . $this->getSkinUrl('images/sceau/warning.gif')." WIDTH=20 >";
        return ($html);
    }
}