<?php

class AoeComponents_Magento_Pages_ProductSingleView extends Menta_Component_AbstractTest {

	/**
	 * Put products into cart
	 *
	 * @param int|array $productId or array of productIds
	 * @return void
	 */
	public function putProductsIntoCart($products, $waitForAjax=true) {
		if (!is_array($products)) {
			$products = array($products);
		}
		foreach ($products as $productId) {
			$this->openProduct($productId);
			$this->clickAddToCart($waitForAjax);
		}
	}

	/**
	 * Click add to cart
	 *
	 * @param bool $waitForAjax
	 * @return null|bool
	 */
	public function clickAddToCart($waitForAjax=true) {
		$this->getTest()->click("link=Add to Cart");
		if ($waitForAjax) {
			$waitHelper = Menta_ComponentManager::get('Menta_Component_Helper_Wait'); /* @var $waitHelper Menta_Component_Helper_Wait */
			$this->getTest()->assertTrue($waitHelper->waitForElementVisible('//*[@id="cartHeader"]/*[@class="number"]'), 'Ajax response for putting item into cart timed out');
		}
		return null;
	}

	/**
	 * Open product single view
	 *
	 * @param int $productId
	 * @return void
	 */
	public function openProduct($productId) {
		$this->getTest()->open($this->getProductUrl($productId));
		$this->getTest()->assertTextNotPresent('We are sorry, but the page you are looking for cannot be found.');
	}

	/**
	 * Get product url
	 *
	 * @param int $productId
	 * @return string
	 */
	public function getProductUrl($productId) {
		return '/catalog/product/view/id/'.$productId;
	}

	/**
	 * Check if product has proper price
	 *
	 * @param string $expected expected price including currency sign
	 */
	public function assertPriceInclTax($expected) {
		$this->getTest()->assertEquals($expected, $this->getText("//span[@class=\"price-including-tax\"]//span[@class=\"price\"]"));
	}


}