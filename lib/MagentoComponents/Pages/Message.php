<?php

/**
 * Components for system messages
 */
class MagentoComponents_Pages_Message extends Menta_Component_AbstractTest
{
    /**
     * Get message xpath depends on type (error, success, notice)
     *
     * @param $type
     * @return string
     */
    public function getMessageXpath($type)
    {
        $xpath = '//ul[' . Menta_Util_Div::contains('messages') . ']';
        $xpath .= '/li[' . Menta_Util_Div::contains($type) . ']';
        $xpath .= '/ul/li/span';
        return $xpath;
    }

    /**
     * Assert message
     *
     * @param $text
     * @param $type
     */
    public function assertMessage($text, $type)
    {
        $xpath = $this->getMessageXpath($type);
        $this->getHelperAssert()->assertElementEqualsToText($xpath, $text);
    }

    /**
     * Assert error message
     *
     * @param $text
     */
    public function assertErrorMessage($text)
    {
        $this->assertMessage($text, 'error-msg');
    }

    /**
     * Assert success message
     *
     * @param $text
     */
    public function assertSuccessMessage($text)
    {
        $this->assertMessage($text, 'success-msg');
    }

    /**
     * Assert no error message present
     */
    public function assertNoErrorMessagePresent()
    {
        $xpath = '//ul[' . Menta_Util_Div::contains('messages') . ']';
        $xpath .= '/li[' . Menta_Util_Div::contains('error-msg') . ']';
        $this->getHelperAssert()->assertElementNotPresent($xpath, 'Error messsage found!');
    }

    /**
     * Wait for success message present
     * @param $text
     */
    public function waitForSuccessMessagePresent($text)
    {
        $this->waitForMessagePresent($text, 'success-msg');
    }

    /**
     * Wait for message
     *
     * @param $text
     * @param $type
     */
    public function waitForMessagePresent($text, $type)
    {
        $xpath = $this->getMessageXpath($type);
        $this->getHelperWait()->waitForTextPresent($xpath, $text);
    }

}