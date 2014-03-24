<?php

/**
 * Components for WebService Api v2
 */
class MagentoComponents_WebServiceApiV2 extends Menta_Component_Abstract
{

    /**
     * @var string
     */
    protected $soapSessionId;

    /**
     * @var SoapClient
     */
    protected $soapClient;

    /**
     * Get session (login)
     * and initialize SoapClient
     *
     * @return string sessionKey
     * @throws Exception
     */
    public function getSoapSessionId()
    {
        if (is_null($this->soapSessionId)) {
            $user = $this->getConfiguration()->getValue('testing.webserviceapi.user');
            $password = $this->getConfiguration()->getValue('testing.webserviceapi.password');
            if (empty($user) || empty($password)) {
                throw new Exception('No valid webservice user and/or password found in configuration');
            }
            $this->soapSessionId = $this->getSoapClient()->login($user, $password);
        }
        return $this->soapSessionId;
    }

    /**
     * Get soapclient
     *
     * @return SoapClient
     */
    public function getSoapClient()
    {
        if (is_null($this->soapClient)) {
            // TODO: maybe we should have a custom wsdl location setting...
            $url = $this->getConfiguration()->getValue('testing.maindomain');
            $this->soapClient = new SoapClient($url . '/index.php/api/v2_soap/?wsdl');
        }
        return $this->soapClient;
    }

    /**
     * Create product
     *
     * @param string $type
     * @param string $set
     * @param string $sku
     * @param array $productData
     * @return mixed
     */
    public function createProduct($type = 'simple', $set = '4', $sku = 'simple_sku', array $productData = array())
    {
        $result = $this->getSoapClient()->catalogProductCreate(
            $this->getSoapSessionId(),
            $type,
            $set,
            $sku,
            $productData
        );
        return $result;
    }

    /**
     * Get info about product
     *
     * @param $idOrSku
     * @param string $storeView
     * @param array $attributes
     * @return mixed
     */
    public function getProductInfo($idOrSku, $storeView = 'default', array $attributes = array())
    {
        $result = $this->getSoapClient()->catalogProductInfo(
            $this->getSoapSessionId(),
            $idOrSku,
            $storeView,
            $attributes

        );
        return $result;
    }

    /**
     * Delete product
     *
     * @param $idOrSku
     * @return mixed
     */
    public function deleteProduct($idOrSku)
    {
        $result = $this->getSoapClient()->catalogProductDelete(
            $this->getSoapSessionId(),
            $idOrSku
        );
        return $result;
    }

    /**
     * Get info about order
     *
     * @param $incrementId
     * @return mixed
     */
    public function getOrderInfo($incrementId)
    {
        $result = $this->getSoapClient()->salesOrderInfo(
            $this->getSoapSessionId(),
            $incrementId
        );
        return $result;
    }

    /**
     * Create a shipment for an order
     *
     * @param        $orderIncrementId
     * @param array $itemsQty - associative array in the form of order_item_id => qty_to_ship
     * @param string $comment
     * @param bool $sendEmail
     * @param bool $includeComment
     * @return string Shipment Increment ID
     */
    public function createShipment($orderIncrementId, $itemsQty = array(), $comment = '', $sendEmail = false, $includeComment = false)
    {
        $result = $this->getSoapClient()->salesOrderShipmentCreate(
            $this->getSoapSessionId(),
            $orderIncrementId,
            $this->getApiItemQtyFromSimpleItemQty($itemsQty),
            $comment,
            $sendEmail,
            $includeComment
        );
        return $result;
    }

    /**
     * Creates an API compatible item qty array from a simplified item qty array
     *
     * @param $itemsQty
     * @return array
     */
    public function getApiItemQtyFromSimpleItemQty($itemsQty)
    {
        $apiItemQty = array();
        foreach ($itemsQty as $itemId => $qty) {
            $apiItemQty[] = array(
                'order_item_id' => $itemId,
                'qty' => $qty
            );
        }

        return $apiItemQty;
    }

    /**
     * Add a tracking number to a shipment
     *
     * @param $shipmentIncrementId
     * @param $carrierCode
     * @param $trackingTitle
     * @param $trackingNumber
     * @return int Tracking Number ID
     */
    public function addTrackingCode($shipmentIncrementId, $carrierCode, $trackingTitle, $trackingNumber)
    {
        $result = $this->getSoapClient()->salesOrderShipmentAddTrack(
            $this->getSoapSessionId(),
            $shipmentIncrementId,
            $carrierCode,
            $trackingTitle,
            $trackingNumber
        );
        return $result;
    }

    /**
     * Get info about a shipment
     *
     * @param $shipmentIncrementId
     * @return array
     */
    public function getShipmentInfo($shipmentIncrementId)
    {
        $result = $this->getSoapClient()->salesOrderShipmentCreate(
            $this->getSoapSessionId(),
            $shipmentIncrementId
        );
        return $result;
    }
}
