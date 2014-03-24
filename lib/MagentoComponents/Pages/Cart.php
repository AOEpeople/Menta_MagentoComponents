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

}