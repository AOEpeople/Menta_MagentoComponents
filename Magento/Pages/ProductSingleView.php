<?php

class AoeComponents_Magento_Pages_ProductSingleView extends Menta_Component_AbstractTest {

	public function getAddToCartButtonSelector() {
		return "link=Add to Cart";
	}

	public function getRegularPricePath() {
		return "//form[@id='product_addtocart_form']//*[@class='price-box']//span[@class='regular-price']";
	}
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
		$this->getTest()->click($this->getAddToCartButtonSelector());
		if ($waitForAjax) {
			$cart = Menta_ComponentManager::get('AoeComponents_Magento_Pages_Cart'); /* @var $cart AoeComponents_Magento_Pages_Cart */
			$cart->waitForAjax();
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

	/**
	 * Check if product has proper price
	 *
	 * @param string $expected expected price including currency sign
	 */
	public function assertRegularPrice($expected) {
		$this->getTest()->assertEquals($expected, $this->getText($this->getRegularPricePath()));
	}
}