<?php

class MagentoComponents_Pages_ProductSingleView extends Menta_Component_AbstractTest
{

    /**
     * Get path for regular price
     *
     * @return string
     */
    public function getRegularPricePath()
    {
        return "//form[@id='product_addtocart_form']//*[" . Menta_Util_Div::contains('price-box') . "]//span[@class='regular-price']";
    }

    /**
     * Get path for add to cart button
     *
     * @return string
     */
    public function getAddToCartButtonPath()
    {
        return "//*[@id='product_addtocart_form']//button[" . Menta_Util_Div::contains($this->__('Add to Cart'), 'title') . "]";
    }

    public function getCheckoutButtonPath()
    {
        return "//*[@id='product_addtocart_form']//button[" . Menta_Util_Div::contains("bu-checkout") . "]";
    }

    public function getOutOfStockButtonPath()
    {
        return "//*[@id='product_addtocart_form']//button[" . Menta_Util_Div::contains($this->__('Out of stock'), 'title') . "]";
    }

    /**
     * Put products into cart
     *
     * @param $products
     * @param bool $waitForAjax
     * @internal param array|int $productId or array of productIds
     * @return void
     */
    public function putProductsIntoCart($products, $waitForAjax = true)
    {
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
    public function clickAddToCart($waitForAjax = true)
    {
        $this->getHelperCommon()->click($this->getAddToCartButtonPath());
        if ($waitForAjax) {
            $cart = Menta_ComponentManager::get('MagentoComponents_Pages_Cart');
            /* @var $cart MagentoComponents_Pages_Cart */
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
    public function openProduct($productId)
    {
        $this->getHelperCommon()->open($this->getProductUrl($productId));
        $this->getHelperAssert()->assertTextNotPresent('We are sorry, but the page you are looking for cannot be found.');
    }

    /**
     * Get product url
     *
     * @param int $productId
     * @return string
     */
    public function getProductUrl($productId)
    {
        return '/catalog/product/view/id/' . $productId;
    }

    /**
     * Check if product has proper price
     *
     * @param string $expected expected price including currency sign
     */
    public function assertPriceInclTax($expected)
    {
        $this->getTest()->assertEquals($expected, $this->getText("//span[@class=\"price-including-tax\"]//span[@class=\"price\"]"));
    }

    /**
     * Check if product has proper price
     *
     * @param string $expected expected price including currency sign
     */
    public function assertRegularPrice($expected)
    {
        $this->getTest()->assertEquals($expected, $this->getRegularPrice());
    }

    /**
     * Check if product has proper price
     *
     * @return regular price
     */
    public function getRegularPrice()
    {
        return $this->getHelperCommon()->getText($this->getRegularPricePath());
    }

    /* BUTTONS */
    public function isButtonAddVisible()
    {
        return $this->getHelperCommon()->isVisible($this->getAddToCartButtonPath());
    }

    public function isButtonCheckoutVisible()
    {
        return $this->getHelperCommon()->isVisible($this->getCheckoutButtonPath());
    }

    public function isButtonOutOfStockVisible()
    {
        return $this->getHelperCommon()->isVisible($this->getOutOfStockButtonPath());
    }

    public function assertButtonAddText()
    {
        $this->getTest()->assertEquals(
            $this->__("Add to Cart"),
            $this->getHelperCommon()->getText($this->getAddToCartButtonPath())
        );
    }

    public function assertButtonOutOfStockText()
    {
        $this->getTest()->assertEquals(
            $this->__("Out of stock"),
            $this->getHelperCommon()->getText($this->getOutOfStockButtonPath())
        );
    }

    public function assertButtons($status, $checkoutAvailable)
    {
        if ($status != 'outOfStock') {
            $this->getTest()->assertFalse($this->isButtonOutOfStockVisible());
            $this->getTest()->assertTrue($this->isButtonAddVisible());
            $this->assertButtonAddText();
        } else {
            $this->getTest()->assertTrue($this->isButtonOutOfStockVisible());
            $this->getTest()->assertFalse($this->isButtonAddVisible());
            $this->assertButtonOutOfStockText();
        }
        if ($checkoutAvailable) {
            $this->getTest()->assertTrue($this->isButtonCheckoutVisible());
        } else {
            $this->getTest()->assertFalse($this->isButtonCheckoutVisible());
        }
    }


    /*
     * Size Selector
     */
    public function selectSize($sizeId, $attributeId)
    {
        $this->getTest()->select("//select[@id='attribute" . $attributeId . "']", "value=" . $sizeId);
    }


    /**
     * @param $optionId
     * @param $attributeId
     *
     * TODO replace selectSize
     */
    public function selectDropDownOption($optionId, $attributeId)
    {
        $this->getTest()->select("//select[@id='attribute" . $attributeId . "']", "value=" . $optionId);
    }

}