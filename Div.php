<?php
/**
 * ${NAME}.
 *
 * @author Fabrizio Branca <fabrizio.branca@aoemedia.de>
 * @since 2011-11-26
 */
class AoeComponents_Div {

	/**
	 * Return the contains statement for xpath
	 *
	 * @param string $needle
	 * @param string $attribute (optional)
	 * @return string
	 */
	public static function contains($needle, $attribute="class") {
		return "contains(concat(' ', @$attribute, ' '), ' $needle ')";
	}

}
