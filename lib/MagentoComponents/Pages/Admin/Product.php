<?php

/**
 * Components for product edit page
 */
class MagentoComponents_Pages_Admin_Product extends MagentoComponents_Pages_Admin
{
    /**
     * Open product admin view
     *
     * @param int $productId
     * @return void
     */
    public function openProduct($productId)
    {
        $this->getHelperCommon()->open($this->getProductUrl($productId));
        $this->getHelperAssert()->assertTextNotPresent('This product no longer exists.');
    }

    /**
     * Get product admin url
     *
     * @param int $productId
     * @return string
     */
    public function getProductUrl($productId)
    {
        return $this->getAdminUrl() . '/catalog_product/edit/id/' . $productId;
    }
}
