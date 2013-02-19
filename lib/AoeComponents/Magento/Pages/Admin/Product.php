<?php
/**
 * @author David Robinson <david.robinson@aoemedia.com>
 * @since 19/2/2013
 */
class AoeComponents_Magento_Pages_Admin_Product extends AoeComponents_Magento_Pages_Admin
{
	/**
	 * Open product admin view
	 *
	 * @param int $productId
	 * @return void
	 */
	public function openProduct($productId)
	{
		$this->getTest()->open($this->getProductUrl($productId));
		$this->getTest()->assertTextNotPresent('This product no longer exists.');
	}

	/**
	 * Get product admin url
	 *
	 * @param int $productId
	 * @return string
	 */
	public function getProductUrl($productId)
	{
		return $this->getAdminUrl().'/catalog_product/edit/id/'.$productId;
	}
}
