<?php

class MagentoComponents_WebServiceApi extends Menta_Component_Abstract {

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
			$this->soapClient = new SoapClient($url . '/api/?wsdl');
		}
		return $this->soapClient;
	}

	/**
	 * Get order info
	 *
	 * @param int $orderId
	 * @return array
	 */
	public function getOrderInfo($orderId) {
		return $this->getSoapClient()->call($this->getSoapSessionId(), 'sales_order.info', $orderId);
	}

	/**
	 * Run scheduler task
	 * (Requires Aoe_Scheduler to be available on the Magento server)
	 *
	 * @param string $code
	 * @return mixed
	 */
	public function runSchedulerTask($code) {
		return $this->getSoapClient()->call($this->getSoapSessionId(), 'aoe_scheduler.runNow', array($code));
	}

}