<?php

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

    /**
     * Path for any link in header
     * @param $text
     * @return string
     */
    public function getLinkPathFromHeader($text)
    {
        return '//div[' . Menta_Util_Div::contains('header') . ']//ul['
        . Menta_Util_Div::contains('links') . ']//a['
        . Menta_Util_Div::contains($text, 'title') . ' ]';
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