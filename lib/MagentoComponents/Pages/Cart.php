<?php

class MagentoComponents_Pages_Cart extends Menta_Component_AbstractTest {

	/**
	 * Check if we're on the cart page.
	 * Useful if e.g. the add to cart button should have redirected to this page
	 *
	 * @author Fabrizio Branca
	 * @since 2012-11-16
	 */
	public function assertOnCartPage() {
		$this->getHelperAssert()->assertBodyClass('checkout-cart-index');
	}

	/**
	 * Open cart
	 *
	 * @return void
	 */
	public function open() {
		$this->getTest()->open($this->getCartUrl());
		$this->getHelperAssert()->assertTitle($this->__("Shopping Cart"));
		$this->assertOnCartPage();
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
		$this->getHelperAssert()->assertTextPresent($this->__('Shopping Cart is Empty'));
	}

    public function clearCart() {
        $this->getTest()->open($this->getCartUrl());
        if($this->getHelperCommon()->isElementPresent($this->getEmptyCartButtonPath())) {
            $this->getHelperCommon()->click($this->getEmptyCartButtonPath());
        }
    }

    public function getEmptyCartButtonPath()
    {
        return "//button[@value='empty_cart']";
    }

	/**
	 * Placeholder for ajax implementation of cartheader
	 */
	public function waitForAjax() {
	}

}