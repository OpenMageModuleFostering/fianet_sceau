<?php

class Fianet_Sceau_IndexController extends Mage_Adminhtml_Controller_Action
{
    
    public function massAction()
    {
      $params = Mage::app()->getRequest()->getParams();
      $orderIds = $params[$params['massaction_prepare_key']];
      
      $successMsg = '';
      
      foreach ($orderIds as $orderId)
      {
          $order = Mage::getModel('sales/order')->load($orderId);
          
          //$order->setData('fianet_sceau_order_sent', '0');
          
          if ( $this->_canSendOrder($order)
                  && Mage::Helper('fianet_sceau/Data')->processOrderToFianet($order))
          {
              //Zend_Debug::dump($order->getData('fianet_sceau_order_sent_prod'), 'order->fianet_sceau_order_sent_prod');
              //Zend_Debug::dump($order->getData('fianet_sceau_order_sent_preprod'), 'order->fianet_sceau_order_sent_preprod');
              //die;
              
               
              $successMsg .= "\n<br />- Commande n° " . $order->getIncrementId(); 
          }
      }
        if ($successMsg != '')
          {
              $successMsg = 'Commande envoyées à FIA-NET :' . $successMsg;
              Mage::getSingleton('adminhtml/session')->addSuccess($successMsg);
          }
        $order->save();
        $this->_redirect('adminhtml/sales_order/index');
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
        return true;
    }
    
}