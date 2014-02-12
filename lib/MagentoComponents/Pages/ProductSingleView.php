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

    /**
     * Get status xpath
     *
     * @param $status
     * @return string
     */
    public function getStatusXpath($status)
    {
        return '//*[@id="product_addtocart_form"]//p['. Menta_Util_Div::contains($status, 'class') .']/span';
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
        $helper = Menta_ComponentManager::get('MagentoComponents_Helper');
        $this->getTest()->assertEquals(
            $helper->normalize($expected),
            $helper->normalize($this->getHelperCommon()->getText($this->getRegularPricePath())),
            'Different prices'
        );
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

    /**
     * Check if button Add to Cart is visible
     *
     * @return bool
     */
    public function isButtonAddVisible()
    {
        return $this->getHelperCommon()->isVisible($this->getAddToCartButtonPath());
    }


    /**
     *
     */
    public function assertButtonAddText()
    {
        $this->getTest()->assertEquals(
            $this->__("Add to Cart"),
            $this->getHelperCommon()->getText($this->getAddToCartButtonPath())
        );
    }

    /**
     * Select attribute option
     *
     * @param $optionId
     * @param $attributeId
     *
     */
    public function selectDropDownOption($optionId, $attributeId)
    {
        $this->getHelperCommon()->select("//select[@id='attribute" . $attributeId . "']", "value=" . $optionId);
    }

    /**
     * Assert dropdown option label with price
     *
     * @param $optionId
     * @param $label
     * @param $priceDifference
     * @param $attributeId
     */
    public function assertDropdownExistOptionLabelWithPrice($optionId, $label, $priceDifference, $attributeId)
    {
        $this->getTest()->assertTrue($this->getHelperCommon()->isElementPresent("//select[@id='attribute".$attributeId."']"));
        $this->getTest()->assertTrue($this->getHelperCommon()->isElementPresent("//select[@id='attribute".$attributeId."']/option[@value='".$optionId."']"), "Option with ID: ".$optionId." not found");
        $this->getTest()->assertTrue($this->getHelperCommon()->isElementPresent("//select[@id='attribute".$attributeId."']/option[@value='".$optionId."'][@price='".$priceDifference."']"),
            "Option with ID: ".$optionId." and price: ".$priceDifference." not found");
        $this->getTest()->assertEquals(
            $this->getHelper()->normalize($label),
            $this->getHelper()->normalize($this->getHelperCommon()->getText("//select[@id='attribute".$attributeId."']/option[@value='".$optionId."'][@price='".$priceDifference."']"))
        );
    }


    /**
     * Assert label of selected option
     *
     * @param $label
     * @param $attributeId
     */
    public function assertSelectedLabel($label, $attributeId)
    {
        $this->getTest()->assertEquals(
            $this->getHelper()->normalize($label),
            $this->getHelper()->normalize($this->getHelperCommon()->getSelectedLabel("//select[@id=\"attribute" . $attributeId. "\"]"))
        );
    }

    /**
     * Assert value of selected option
     *
     * @param $optionId
     * @param $attributeId
     */
    public function assertDropdownSelectedValue($optionId, $attributeId){
        $this->getTest()->assertEquals(
            $optionId,
            $this->getHelperCommon()->getSelectedValue("//select[@id=\"attribute".$attributeId."\"]")
        );
    }


    /**
     * Get Magento Helper
     *
     * @return Menta_Interface_Component
     */
    public function getHelper()
    {
        return Menta_ComponentManager::get('MagentoComponents_Helper');
    }


    /**
     * Assert status
     *
     * @param $statusCode
     */
    public function assertStatus($statusCode)
    {
        if($statusCode == 'inStock'){
            $this->assertInStock();
        } elseif ($statusCode == 'outOfStock'){
            $this->assertOutOfStock();
        }
    }

    /**
     * Assert status In Stock
     */
    public function assertInStock()
    {
        $this->getTest()->assertEquals($this->__("In stock"),
            $this->getHelperCommon()->getText($this->getStatusXpath('in-stock')));
    }

    /**
     * Assert status Out Of Stock
     */
    public function assertOutOfStock()
    {
        $this->getTest()->assertEquals($this->__("Out of stock"),
            $this->getHelperCommon()->getText($this->getStatusXpath('in-stock')));
    }


    /**
     * Assert add to cart button
     *
     * @param $status
     */
    public function assertAddToCartButton($status)
    {
        if ($status != 'outOfStock') {
            $this->getTest()->assertTrue($this->isButtonAddVisible());
            $this->assertButtonAddText();
        } else {
            $this->getTest()->assertFalse($this->isButtonAddVisible());
        }
    }

    /**
     * Assert quantity
     *
     * @param $quantity
     */
    public function assertQuantity($quantity)
    {
        $this->getTest()->assertEquals($quantity, $this->getTest()->getValue("id=qty"));
    }
}