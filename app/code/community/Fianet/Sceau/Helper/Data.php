<?php

class Fianet_Sceau_Helper_Data extends Mage_Core_Helper_Abstract {
    const ORDER_ATTR_SCEAU_SENT_PROD = 'fianet_sceau_order_sent_prod';
    const ORDER_ATTR_SCEAU_SENT_PPROD = 'fianet_sceau_order_sent_preprod';
    const ORDER_ATTR_SCEAU_SENT_ERROR= 'fianet_sceau_order_sent_error';

    static function Generate_Sceau_xml(Mage_Sales_Model_Order $order) {
        //récupération des informations
        $email = $order->customer_email;
        $timestamp = $order->created_at;
        $refid = $order->increment_id;
        $privatekey = Mage::getStoreConfig('sceau/sceauconfg/private_key', $order->getStoreId());
        $crypt = md5($privatekey . "_" . $refid . "+" . $timestamp . "=" . $email);
        $siteid = self::getSiteID($order);
		
		//Si l'IP de l'internaute n'est pas présente dans Magento (en cas de création de commande depuis le BO) alors on récupère l'IP de la boutique
		$ip = (!$order->getRemoteIp()) ? $_SERVER['REMOTE_ADDR'] : $order->getRemoteIp();
        //Zend_Debug::dump($order);die;

        return("<?xml version='1.0' encoding='UTF-8' ?><control><utilisateur><nom titre='$order->customer_prefix'><![CDATA[$order->customer_lastname]]></nom><prenom><![CDATA[$order->customer_firstname]]></prenom><email><![CDATA[$email]]></email></utilisateur><infocommande><siteid><![CDATA[$siteid]]></siteid><refid><![CDATA[$refid]]></refid><montant devise='$order->base_currency_code'><![CDATA[$order->base_grand_total]]></montant><ip timestamp='$timestamp'><![CDATA[$ip]]></ip></infocommande><crypt><![CDATA[$crypt]]></crypt></control>");
    }

    static public function clean_xml($xml) {
        $xml = str_replace("\\'", "'", $xml);
        $xml = str_replace("\\\"", "\"", $xml);
        $xml = str_replace("\\\\", "\\", $xml);
        $xml = str_replace("\t", "", $xml);
        $xml = str_replace("\n", "", $xml);
        $xml = str_replace("\r", "", $xml);
        $xml = trim($xml);
        return ($xml);
    }

    static public function clean_invalid_char($var) {
        //supprimes les balises html
        $var = strip_tags($var);
        //$var = str_replace("&", "&&amp;", $var);
        $var = str_replace('&', '', $var);
        $var = str_replace("<", "&lt;", $var);
        $var = str_replace(">", "&gt;", $var);
        $var = trim($var);
        return ($var);
    }

    static function processOrderToFianet(Mage_Sales_Model_Order $order) {
        if (self::sendOrderToFianet($order)) {

            $attribut_sceau = self::ORDER_ATTR_SCEAU_SENT_PPROD;

            if (self::sendingMode($order) == Fianet_Sceau_Model_Source_Mode::MODE_PROD) {
                $attribut_sceau = self::ORDER_ATTR_SCEAU_SENT_PROD;
            }
            $order->setData($attribut_sceau, '1');

            //Mage::getSingleton('adminhtml/session')->addError('processOrderToFianet() : ' . $attribut_sceau . ' = ' . $order->getData($attribut_sceau));

            return true;
        }
        return false;
    }

    static function getStatusesConfig() {
        return explode(',', Mage::getStoreConfig('sceau/sceauconfg/orderstatuses'));
    }

    static function checkCurrentOrderStatus(Mage_Sales_Model_Order $order) {
        return in_array($order->getData('status'), self::getStatusesConfig());
    }

    static function sendOrderToFianet(Mage_Sales_Model_Order $order) {


        $flux = self::Generate_Sceau_xml($order);
        //Zend_Debug::dump($flux, 'flux');

        $url = self::getFianetUrl($order);
        $config = array('maxredirects' => 0,
            'timeout' => 5);

        $params = array('SiteID' => self::getSiteID($order),
            'CheckSum' => md5($flux),
            'XMLInfo' => $flux);

        Mage::dispatchEvent('Fianet_Sceau_Before_Send_Order', array('order' => $order, 'url' => $url, 'flux' => $flux));
        //Zend_Debug::dump($config, 'config');
        //Zend_Debug::dump($params, 'params');

        try {
            //Zend_Debug::dump($url, 'flux');
            $client = new Zend_Http_Client($url, $config);
            $client->setMethod(Zend_Http_Client::POST);
            $client->setParameterPost($params);
            //Zend_Debug::dump($client, 'client');
            $response = $client->request();

            //Mage::getSingleton('adminhtml/session')->addError('sendOrderToFianet() : '.htmlentities($flux));
            //Mage::getSingleton('adminhtml/session')->addError('sendOrderToFianet() : '.$response->getBody());

            Mage::dispatchEvent('Fianet_Sceau_After_Send_Order', array('order' => $order, 'response' => $response));
            //Zend_Debug::dump($response);

            return self::parseFianetResponse($response, $order);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError('FIA-NET Sceau sending error : ' . $e->getMessage());
        }
    }

    static function parseFianetResponse($response, Mage_Sales_Model_Order $order) {
        $attribut_sceau_error = self::ORDER_ATTR_SCEAU_SENT_ERROR;
        if ($response->isSuccessful()) {
            $xml = $response->getBody();

            $simplexml = new Varien_Simplexml_Config($xml);
            if ($simplexml->getNode()->getAttribute('type') == 'OK') {
                //Zend_Debug::dump('OK');
                $order->setData($attribut_sceau_error, '0');
                return true;
            }
        }
        if ($simplexml->getNode()->detail == '')
        {
            $order->setData($attribut_sceau_error, '1');
            $ret = "erreur de flux";
    }
        else
            $ret = $simplexml->getNode()->detail;
        Mage::getSingleton('adminhtml/session')->addError('FIA-NET Sceau sending error : ' . $ret);
        
        return false;
    }

    static function getFianetUrl(Mage_Sales_Model_Order $order) {
        $url = Mage::getStoreConfig('sceau/sceaulinks/test_send_url');

        if (self::sendingMode($order) == Fianet_Sceau_Model_Source_Mode::MODE_PROD) {
            $url = Mage::getStoreConfig('sceau/sceaulinks/prod_send_url');
        }

        return Mage::getStoreConfig('sceau/sceaulinks/fianet_url') . '/' . $url;
    }

    static function getSiteID(Mage_Sales_Model_Order $order=null) {
        if (isset($order)) {
            return Mage::getStoreConfig('sceau/sceauconfg/siteid', $order->getStoreId());
        }
        return Mage::getStoreConfig('sceau/sceauconfg/siteid');
    }
	
	static function getLogin() {
        return Mage::getStoreConfig('sceau/sceauconfg/login');
    }

    static function activateWidgetComments() {
        return Mage::getStoreConfig('sceau/widgetconf/commentaires');
    }

    static function getWidgetTransparent() {
        if (Mage::getStoreConfig('sceau/widgetconf/widgettransparent') == TRUE) {
            return "transparent";
        }
        return "blanc";
    }

    static public function isOrderAlreadySent(Mage_Sales_Model_Order $order) {
        $attribute_name = self::ORDER_ATTR_SCEAU_SENT_PPROD;
        if (self::sendingMode($order) == Fianet_Sceau_Model_Source_Mode::MODE_PROD) {
            $attribute_name = self::ORDER_ATTR_SCEAU_SENT_PROD;
        }

        if ($order->getData($attribute_name) == '1') {
            return true;
        }
        return false;
    }

    static public function getMagentoVersion() {
        $version = Mage::getVersion();
        $version = substr($version, 0, 5);
        $version = str_replace('.', '', $version);
        while (strlen($version) < 3) {
            $version .= "0";
        }
        return (int) $version;
    }

    static function isModuleActive($order) {
        if (Mage::getStoreConfig('sceau/sceauconfg/active', $order->getStoreId()) == '1') {
            return true;
        }
        return false;
    }

    static function sendingMode(Mage_Sales_Model_Order $order) {
        return Mage::getStoreConfig('sceau/sceauconfg/mode', $order->getStoreId());
    }

}