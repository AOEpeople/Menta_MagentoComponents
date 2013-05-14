<?php

class AoeComponents_Magento_WebServiceApiV2 extends Menta_Component_Abstract {

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
	public function getSoapSessionId() {
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
	 * @author Fabrizio Branca <fabrizio.branca@aoemedia.de>
	 * @since 09.11.2011
	 */
	public function getSoapClient() {
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
	public function createProduct($type='simple', $set='4', $sku='simple_sku', array $productData=array()) {
		$result = $this->getSoapClient()->catalogProductCreate(
			$this->getSoapSessionId(),
			$type,
			$set,
			$sku,
			$productData
		);
		return $result;
	}

	public function getProductInfo($idOrSku, $storeView='default', array $attributes=array()) {
		$result = $this->getSoapClient()->catalogProductInfo(
			$this->getSoapSessionId(),
			$idOrSku,
			'default',
			$attributes

		);
		return $result;
	}

	public function deleteProduct($idOrSku) {
		$result = $this->getSoapClient()->catalogProductDelete(
			$this->getSoapSessionId(),
			$idOrSku
		);
		return $result;
	}


}