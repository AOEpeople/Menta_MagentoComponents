<?php

class MagentoComponents_Pages_CategoryView extends Menta_Component_AbstractTest
{
    /**
     * Get path for regular price
     *
     * @return string
     */
    public function getRegularPricePath($productId)
    {
        return '//span[@id="product-price-' .$productId. '"]/span';
    }

    /**
     * Get xpath to "add to cart"
     *
     * @param int $productId
     * @return string
     */
    public function getAddToCartLinkXpath($productId)
    {
        $xpath = "//li//button[" .
            Menta_Util_Div::contains($this->__('Add to Cart'), 'title') .
            " and contains(@onclick," . $productId . ")] ";

        return $xpath;
    }

    /**
     * Put products into cart
     *
     * @param $products
     * @param boolean $waitForAjax should wait for end of request before adding next product to cart
     * @param int $sleep time to sleep between requests
     * @internal param array|int $productId or array of productIds
     */
    public function putProductsIntoCart($products, $waitForAjax = true, $sleep = 0)
    {
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
     */
    public function putProductIntoCart($productId, $waitForAjax = true, $sleep = 0)
    {
        $this->getHelperCommon()->click($this->getAddToCartLinkXpath($productId));
        // Hover on parent element first (Needed in Selenium 2)

        if ($waitForAjax) {
            $cart = Menta_ComponentManager::get('MagentoComponents_Pages_Cart');
            /* @var $cart MagentoComponents_Pages_Cart */
            $cart->waitForAjax();
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
    public function moveToProductAddToCartButton($productId)
    {
        // Hover on parent element first (Needed in Selenium 2)
        $session = $this->getSession();
        /* @var $session \WebDriver\Session */

        $link = $session
            ->element(\WebDriver\LocatorStrategy::XPATH, $this->getAddToCartLinkXpath($productId));

        $session->moveto(array('element' => $link->getID()));
    }

    /**
     * Open category page (product listing)
     *
     * @param int $categoryId
     * @param string $additionalParams
     * @return void
     */
    public function open($categoryId, $additionalParams = '?limit=all')
    {
        $this->getHelperCommon()->open($this->getCategoryUrl($categoryId) . $additionalParams);
    }

    /**
     * Check if product has proper price
     *
     * @param string $expected expected price including currency sign
     */
    public function assertRegularPrice($productId, $expected)
    {
        $this->getTest()->assertEquals(
            $this->getHelper()->normalize($expected),
            $this->getHelper()->normalize($this->getHelperCommon()->getText($this->getRegularPricePath($productId))),
            'Different prices'
        );
    }

    /**
     * Checks if category page is currently open
     */
    public function assertIsOnCategoryPage()
    {
        $this->getHelperAssert()->assertBodyClass('catalog-category-view');
    }

    /**
     * Get category url
     *
     * @param int $categoryId
     * @return string
     */
    public function getCategoryUrl($categoryId)
    {
        return '/catalog/category/view/id/' . $categoryId;
    }

    /**
     * Get Magento Helper
     *
     * @return MagentoComponents_Helper
     */
    public function getHelper()
    {
        return Menta_ComponentManager::get('MagentoComponents_Helper');
    }

}