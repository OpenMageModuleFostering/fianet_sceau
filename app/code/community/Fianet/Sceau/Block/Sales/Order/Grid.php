<?php


class Fianet_Sceau_Block_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        
        $params = array('grid' => $this);
        
        
        Mage::dispatchEvent('fianet_prepare_sales_order_grid_columns', $params);
        $this->sortColumnsByOrder();
        return $this;
    }
    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();
        $params = array('grid' => $this);
       
        Mage::dispatchEvent('fianet_prepare_sales_order_grid_massaction', $params);
        
        return $this;
    }
}
