<?php

/**
 * Components for cart view page
 */
class MagentoComponents_Pages_Cart extends Menta_Component_AbstractTest
{
    /**
     * Get items shopping cart table xpath
     *
     * @return string
     */
    public function getCartTablePath()
    {
        return "//table[@id='shopping-cart-table']";
    }

    /**
     * Get empty cart button xpath
     *
     * @return string
     */
    public function getEmptyCartButtonPath()
    {
        return "//button[@value='empty_cart']";
    }

    /**
     * Removes all items from cart
     */
    public function clearCart()
    {
        $this->getHelperCommon()->open($this->getCartUrl());
        if ($this->getHelperCommon()->isElementPresent($this->getEmptyCartButtonPath())) {
            $this->getHelperCommon()->click($this->getEmptyCartButtonPath());
        }
    }

    /**
     * @return array
     */
    public function getCartData() {
        $data = array();
        foreach ($this->getHelperCommon()->getElements('css=#shopping-cart-table tbody tr') as $row) { /* @var $row \WebDriver\Element */
            $sku = preg_replace('/(.*:\s*)/','',$this->getHelperCommon()->getElement('css=.product-cart-sku', $row)->text());
            $data[$sku] = array(
                'name' => $this->getHelperCommon()->getElement('css=.product-name a', $row)->text(),
                'price' => $this->getHelperCommon()->getElement('css=.product-cart-price .cart-price .price', $row)->text(),
                'qty' => $this->getHelperCommon()->getElement('css=input.qty', $row)->attribute('value'),
                'subtotal' => $this->getHelperCommon()->getElement('css=.product-cart-total .cart-price .price', $row)->text(),
                'row' => $row
            );
        }
        return $data;
    }

    /**
     * Remove sku
     *
     * @param $sku
     */
    public function removeSku($sku) {
        $data = $this->getCartData();
        try {
            $this->getHelperCommon()->getElement('css=.product-cart-remove a.btn-remove', $data[$sku]['row'])->click();
        } catch (WebDriver\Exception\ElementNotVisible $e) {
            $this->getHelperCommon()->getElement('css=.product-cart-info a.btn-remove', $data[$sku]['row'])->click();
        }
    }

    /**
     * Assert sku in cart
     *
     * @param $sku
     * @param null $qty
     * @param array $cartData
     */
    public function assertSkuInCart($sku, $qty=null, array $cartData=null) {
        if (is_null($cartData)) {
            $cartData = $this->getCartData();
        }
        $this->getTest()->assertArrayHasKey($sku, $cartData);
        if (!is_null($qty)) {
            $this->getTest()->assertEquals($qty, $cartData[$sku]['qty']);
        }
    }

    /**
     * Assert sku not in cart
     *
     * @param $sku
     * @param array $cartData
     */
    public function assertSkuNotInCart($sku, array $cartData=null) {
        if (is_null($cartData)) {
            $cartData = $this->getCartData();
        }
        $this->getTest()->assertArrayNotHasKey($sku, $cartData);
    }

    /**
     * Assert that the cart is empty
     *
     * @return void
     */
    public function assertEmptyCart()
    {
        $this->open();
        $this->getHelperAssert()->assertTextPresent($this->__('Shopping Cart is Empty'));
    }

    /**
     * Get number of line items
     *
     * @return int
     */
    public function getNumberOfLineItems() {
        return $this->getHelperCommon()->getElementCount('css=#shopping-cart-table tbody tr');
    }

    /**
     * Assert number of line items
     *
     * @param $expectedNumberOfItemsInCart
     */
    public function assertNumberOfLineItems($expectedNumberOfItemsInCart) {
        $this->getTest()->assertEquals($expectedNumberOfItemsInCart, $this->getNumberOfLineItems());
    }

    /**
     * Open cart
     *
     * @return void
     */
    public function open()
    {
        $this->getHelperCommon()->open($this->getCartUrl());
        $this->getHelperAssert()->assertTitle($this->__("Shopping Cart"));
        $this->assertOnCartPage();
    }

    /**
     * Check if we're on the cart page.
     * Useful if e.g. the add to cart button should have redirected to this page
     */
    public function assertOnCartPage()
    {
        $this->getHelperAssert()->assertBodyClass('checkout-cart-index');
    }

    /**
     * Get cart url
     *
     * @return string
     */
    public function getCartUrl()
    {
        return '/checkout/cart/';
    }

    /**
     * Placeholder for ajax implementation add to cart action
     */
    public function waitForAjax()
    {
    }

    public function clickProceedToCheckoutButton() {
        $this->getHelperCommon()->getElement('css=button.btn-proceed-checkout')->click();
    }

}