<?php

class MagentoComponents_Pages_Message extends Menta_Component_AbstractTest {

	/**
	 * Assert message
	 *
	 * @param $text
	 * @param $type
	 */
	public function assertMessage($text, $type) {
		$xpath = '//ul['.Menta_Util_Div::contains('messages').']';
		$xpath .= '/li['.Menta_Util_Div::contains($type).']';
		$xpath .= '/ul/li/span';
		$this->getHelperAssert()->assertElementEqualsToText($xpath, $text);
	}

	/**
	 * Assert error message
	 *
	 * @param $text
	 */
	public function assertErrorMessage($text) {
		$this->assertMessage($text, 'error-msg');
	}

	/**
	 * Assert success message
	 *
	 * @param $text
	 */
	public function assertSuccessMessage($text) {
		$this->assertMessage($text, 'success-msg');
	}

	public function assertNoErrorMessagePresent() {
		$xpath = '//ul['.Menta_Util_Div::contains('messages').']';
		$xpath .= '/li['.Menta_Util_Div::contains('error-msg').']';
		$this->getHelperAssert()->assertElementNotPresent($xpath, 'Error messsage found!');
	}

}