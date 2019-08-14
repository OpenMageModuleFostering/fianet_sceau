<?php

class Fianet_Sceau_Model_Observer extends Varien_Event_Observer {
    CONST GRID_NAME_IN_LAYOUT = 'sales_order.grid';
    CONST MASSACTION_BLOCK_NAME_IN_LAYOUT = 'sales_order.grid.child';
    CONST MASSACTION_BLOCK_CLASS = 'Mage_Adminhtml_Block_Widget_Grid_Massaction';

    public function updateSalesOrderGrid($observer) {
        
        $event = $observer->getEvent();
        $block = $event->getData('block');
        
       

        if ($block->getNameInLayout() == self::GRID_NAME_IN_LAYOUT) {
            $block->addColumnAfter('fianet_sceau', array(
                'header' => 'FIA-NET SCEAU',
                'sortable' => false,
                'type' => 'fianet',
                'align' => 'center',
                'width' => '20',
                'renderer' => 'Fianet_Sceau_Block_Widget_grid_column_renderer_fianet',
                'filter' => 'Fianet_Sceau_Block_Widget_grid_column_filter_fianet'
                    ), 'action');
        }


        if (preg_match('/' . self::MASSACTION_BLOCK_NAME_IN_LAYOUT . '[0-9]+/', $block->getNameInLayout())
                && (get_class($block) == self::MASSACTION_BLOCK_CLASS || get_parent_class($block) == self::MASSACTION_BLOCK_CLASS)) {
            $block->addItem('fianet_sceau', array(
                'label' => Mage::helper('fianet_sceau')->__('Envoyer à FIA-NET sceau'),
                'url' => Mage::getUrl('sceau/index/mass')
            ));
        }
    }

    
    public function sendToFianet($observer) {
        //gestion de l'évènement magento  
        $event = $observer->getEvent();
        $order = $event->getData('order');
      
        //$myorder = (Fianet_Sceau_Model_Order)$order;
        //Zend_Debug::dump($myorder);
        //die;
        if ($this->_canSendOrder($order))
        {
            Mage::Helper('fianet_sceau/Data')->processOrderToFianet($order);
        }
    }
    
    protected function _canSendOrder(Mage_Sales_Model_Order $order)
    {
        if (!Mage::Helper('fianet_sceau/Data')->isModuleActive($order))
        {//si le module est désactivé
            return false;
        }
        else if (Mage::Helper('fianet_sceau/Data')->isOrderAlreadySent($order))
        {//Si la commande a déjà été envoyée
            return false;
        }
        elseif (Mage::Helper('fianet_sceau/Data')->checkCurrentOrderStatus($order)) 
        {//si la status de la commande est l'un des status pour lequel l'envoi est requis
            return true;
        }
        elseif ($order->getState() == Mage_Sales_Model_Order::STATE_NEW 
                && $order->getIsInProcess())
        {//si la commande a été payée
            return true;

        }
        return false;
    }

}