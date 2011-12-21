<?php

class AoeComponents_Magento_Pages_CategoryView extends Menta_Component_AbstractTest {

	/**
	 * Open category page (product listing)
	 *
	 * @param int $categoryId
	 * @return void
	 */
	public function open($categoryId) {
		$this->getTest()->open($this->getCategoryUrl($categoryId));
	}

	/**
	 * Get category url
	 *
	 * @param int $categoryId
	 * @return string
	 */
	public function getCategoryUrl($categoryId) {
		return '/catalog/category/view/id/' . $categoryId;
	}

	/**
	 * Get xpath to "add to cart"
	 *
	 * @param int $productId
	 * @return string
	 */
	public function getAddToCartLinkXpath($productId) {
		return "//li[@id='product_$productId']//a[@class='add-to-basket']";
	}

	/**
	 * Put products into cart
	 *
	 * @param int|array $productId or array of productIds
	 * @param boolean $waitForAjax should wait for end of request before adding next product to cart
	 * @param int time to sleep between requests
	 * @return void
	 */
	public function putProductsIntoCart($products, $waitForAjax = true, $sleep=0) {
		if (!is_array($products)) {
			$products = array($products);
		}

		foreach ($products as $productId) {
			$this->putProductIntoCart($productId, $waitForAjax, $sleep);
		}
	}

	/**
	 * Put product into cart
	 *
	 * @param int $productId
	 * @param bool $waitForAjax
	 * @param int $sleep
	 * @return void
	 */
	public function putProductIntoCart($productId, $waitForAjax = true, $sleep=0) {

		// Hover on parent element first (Needed in Selenium 2)
		$this->moveToProductAddToCartButton($productId);

		$session = $this->getSession(); /* @var $session WebDriver_Session */
		$session->click();

		if ($waitForAjax) {
			$waitHelper = Menta_ComponentManager::get('Menta_Component_Helper_Wait'); /* @var $waitHelper Menta_Component_Helper_Wait */
			$this->getTest()->assertTrue($waitHelper->waitForElementVisible('//*[@id="cartHeader"]/*[@class="number"]'), 'Ajax response for putting item into cart timed out');
		}
		if ($sleep) {
			sleep($sleep);
		}
	}

	/**
	 * Move to a product's add to cart button
	 *
	 * @param $productId
	 * @return void
	 */
	public function moveToProductAddToCartButton($productId) {
		// Hover on parent element first (Needed in Selenium 2)
		$session = $this->getSession(); /* @var $session WebDriver_Session */

		$itemDiv = $session->element(WebDriver_Container::XPATH, "//li[@id='product_$productId']/div");
		$link = $session->element(WebDriver_Container::XPATH, "//li[@id='product_$productId']//a[@class='add-to-basket']");

		$session->moveto(array('element' => $itemDiv->getID()));
		$session->moveto(array('element' => $link->getID()));
	}

	/**
	 * @return Menta_Component_Helper_Common
	 */
	public function getHelperCommon() {
		return Menta_ComponentManager::get('Menta_Component_Helper_Common');
	}
}