<?php

class MagentoComponents_Pages_Cart extends Menta_Component_AbstractTest
{

    /**
     * Check if we're on the cart page.
     * Useful if e.g. the add to cart button should have redirected to this page
     */
    public function assertOnCartPage()
    {
        $this->getHelperAssert()->assertBodyClass('checkout-cart-index');
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
     * Get cart url
     *
     * @return string
     */
    public function getCartUrl()
    {
        return '/checkout/cart/';
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

    public function clearCart()
    {
        $this->getHelperCommon()->open($this->getCartUrl());
        if ($this->getHelperCommon()->isElementPresent($this->getEmptyCartButtonPath())) {
            $this->getHelperCommon()->click($this->getEmptyCartButtonPath());
        }
    }

    /*
     *
     * Get empty cart button path
     *
     * @return string
     * */
    public function getEmptyCartButtonPath()
    {
        return "//button[@value='empty_cart']";
    }

    /*
     * Get table cart path
     *
     * @return string
     */
    public function getCartTablePath()
    {
        return "//table[@id='shopping-cart-table']";
    }


    /*
     * Get number items in cart from top links
     *
     * @return int
     */

    public function getCartItemsFromHeader()
    {
        $text = $this->getHelperCommon()->getText("//*[@class='top-link-cart']");
        preg_match('!\d+!', $text, $count);

        //if cart quantity is 0 then counter is hidden
        if (!$count) {
            return 0;
        }
        return $count[0];
    }


    /**
     * Placeholder for ajax implementation of cartheader
     */
    public function waitForAjax()
    {
    }

}