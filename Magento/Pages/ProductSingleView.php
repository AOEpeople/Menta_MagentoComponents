<?php

class AoeComponents_Magento_Pages_ProductSingleView extends Menta_Component_AbstractTest {

	public function getRegularPricePath() {
		return "//form[@id='product_addtocart_form']//*[@class='price-box']//span[@class='regular-price']";
	}

	public function getAddToCartButtonPath() {
		return "//*[@id='product_addtocart_form']//button[contains(@title,'Add to Basket')]";
	}

	public function getCheckoutButtonPath() {
		return "//*[@id='product_addtocart_form']//button[".AoeComponents_Div::contains("bu-checkout")."]";
	}

	public function getOutOfStockButtonPath() {
		return "//*[@id='product_addtocart_form']//button[contains(@title,'Out of stock')]";
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
		$this->getTest()->click($this->getAddToCartButtonPath());
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

	/* BUTTONS */
	public function isButtonAddVisible() {
		return $this->getTest()->isVisible($this->getAddToCartButtonPath());
	}

	public function isButtonCheckoutVisible() {
		return $this->getTest()->isVisible($this->getCheckoutButtonPath());
	}

	public function isButtonOutOfStockVisible() {
		return $this->getTest()->isVisible($this->getOutOfStockButtonPath());
	}

	public function assertButtonAddText() {
		$this->getTest()->assertEquals(
			$this->__("Add to Cart"),
			$this->getTest()->getText($this->getAddToCartButtonPath())
		);
	}

	public function assertButtonOutOfStockText(){
		$this->getTest()->assertEquals(
			$this->__("Out of stock"),
			$this->getTest()->getText($this->getOutOfStockButtonPath())
		);
	}

	public function assertButtons($status, $checkoutAvailable){
		if($status != 'outOfStock'){
			$this->getTest()->assertFalse($this->isButtonOutOfStockVisible());
			$this->getTest()->assertTrue($this->isButtonAddVisible());
			$this->assertButtonAddText();
		} else {
			$this->getTest()->assertTrue($this->isButtonOutOfStockVisible());
			$this->getTest()->assertFalse($this->isButtonAddVisible());
			$this->assertButtonOutOfStockText();
		}
		if($checkoutAvailable) {
			$this->getTest()->assertTrue($this->isButtonCheckoutVisible());
		} else {
			$this->getTest()->assertFalse($this->isButtonCheckoutVisible());
		}
	}

}