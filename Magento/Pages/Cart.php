<?php

class AoeComponents_Magento_Pages_Cart extends Menta_Component_AbstractTest {

	/**
	 * Open cart
	 *
	 * @return void
	 */
	public function open() {
		$this->getTest()->open($this->getCartUrl());
		$this->getTest()->assertTitle("Shopping Cart");
	}

	/**
	 * Get cart url
	 *
	 * @return string
	 */
	public function getCartUrl() {
		return '/checkout/cart/';
	}

	/**
	 * Assert that the cart is empty
	 *
	 * @return void
	 */
	public function assertEmptyCart() {
		$this->open();
		$this->getTest()->assertTextPresent('Shopping Cart is Empty');
	}

	/**
	 * @return Menta_Component_Helper_Common
	 */
	public function getHelperCommon() {
		return Menta_ComponentManager::get('Menta_Component_Helper_Common');
	}

}