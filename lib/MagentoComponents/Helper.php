<?php

/**
 * Magento helper component
 * All methods can be used in any other magento components/test
 */
class MagentoComponents_Helper extends Menta_Component_AbstractTest
{
    /**
     * Path for login link in header
     * @return string
     */
    public function getLoginLinkPath()
    {
        return $this->getLinkPathFromHeader($this->__('Log In'));
    }

    /**
     * Path for logout link in header
     * @return string
     */
    public function getLogoutLinkPath()
    {
        return $this->getLinkPathFromHeader($this->__('Log Out'));
    }

    /**
     * Path for my account link in header
     * @return string
     */
    public function getAccountLinkPath()
    {
        return $this->getLinkPathFromHeader($this->__('My Account'));
    }

    /*
     * * Get amount of cart items from top links
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
     * Path for any link in header
     * @param $text
     * @return string
     */
    public function getLinkPathFromHeader($text)
    {
        return  $this->getTopMenuIndicatorPath() . '//div[' . Menta_Util_Div::contains('links') . ']//a['
		. Menta_Util_Div::contains($text, 'title') . ' ]';
    }

	/**
	 * Path for container with main customer links
	 *
	 * @return string
	 */
	public function getTopMenuIndicatorPath()
	{
		return '//div[@id="header-account"]';
	}

	/**
	 * Path for 'Account' element
	 *
	 * @return string
	 */
	public function getAccountMenuIndicatorPath()
	{
		return '//div[ ' .  Menta_Util_Div::contains('account-cart-wrapper', 'class') . ']//a[' .
		Menta_Util_Div::contains('#header-account', 'data-target-element') . ' ]';
	}

	/**
	 *
	 */
	public function openLinksMenu()
	{
		/* @var $helper MagentoComponents_Helper */
		$helper = Menta_ComponentManager::get('MagentoComponents_Helper');
		$this->getHelperCommon()->click($helper->getAccountMenuIndicatorPath());
	}

	/**
     * Normalize
     * @param        $text
     * @param string $currency
     *
     * @return mixed|string
     */
    public function normalize($text, $currency = '$US')
    {
        $text = str_replace(' ', '', $text);
        $text = str_replace($currency, '<CURRENCYSYMBOL>', $text);
        $text = trim($text);
        return $text;
    }
}