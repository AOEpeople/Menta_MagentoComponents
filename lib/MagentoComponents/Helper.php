<?php

class MagentoComponents_Helper extends Menta_Component_AbstractTest
{

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