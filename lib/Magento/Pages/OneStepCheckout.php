<?php

class AoeComponents_Magento_Pages_OneStepCheckout extends AoeComponents_Magento_Pages_OnePageCheckout {

    public function selectShipping($name) {
        // $this->getTest()->assertElementPresent("//td[@class='shipping-name']/label[text()='$name']");
        $this->getTest()->click("//td[@class='shipping-name']/label[text()='$name']");
        //$this->getTest()->click("//dl[@class='shipping-methods']/label[text()='$name']");
        $this->waitForSummary();
    }

    public function enterValidCreditCardDataVisa() {
        //	$this->getTest()->click("//dl[@id='checkout-payment-method-load']/dt//label");

        $this->getTest()->waitForAjaxCompletedJquery();
        $this->getTest()->waitForAjaxCompletedPrototype();

        $this->getTest()->select('id=braintree_cc_type', 'label=Visa');
        $this->getTest()->typeAndLeave('id=braintree_cc_number', '4111111111111111');
        $this->getTest()->fireEvent('id=braintree_cc_number', 'blur');
        $this->getTest()->select('id=braintree_expiration', 'label=03 - March');
        $this->getTest()->select('id=braintree_expiration_yr', 'label=2020');
        $this->getTest()->typeAndLeave('id=braintree_cc_cid', '123');
        $this->getTest()->fireEvent('id=braintree_cc_cid', 'blur');
    }

    public function saveAccountForLaterUse() {
        $this->getTest()->click($this->getSaveAccountCheckPath());
        $this->getTest()->typeAndLeave("id=billing:customer_password", "test1234");
        $this->getTest()->typeAndLeave("id=billing:confirm_password", "test1234");
    }

    /**
     * Add shipping address
     *
     * @param string $country
     * @return array complete address data that was used
     */
    public function addShippingAddress($country='us') {

        $addressProvider = new AoeComponents_Magento_Provider_Address();
        $address = $addressProvider->getAddressField('shipping', $country);

        $this->getTest()->typeAndLeave("id=shipping:firstname", $address['firstname']);
        $this->getTest()->typeAndLeave("id=shipping:lastname", $address['lastname']);
        $this->getTest()->typeAndLeave("id=shipping:street1", $address['street1']);
        $this->getTest()->typeAndLeave("id=shipping:street2", $address['street2']);
        $this->getTest()->typeAndLeave("id=shipping:city", $address['city']);
        $this->getTest()->typeAndLeave("id=shipping:telephone", $address['phone']);
        $this->getTest()->typeAndLeave("id=shipping:postcode", $address['postcode']);
        $this->getTest()->typeAndLeave("id=shipping:company", $address['company']);
        $this->getTest()->select("id=shipping:country_id", "label=".$address['country']);
        if (isset($address['region'])&& $address['region']!='') {
            $this->getTest()->select("id=shipping:region_id", "label=".$address['region']);
        }

        sleep(1);   // to keep order of ajax requests
        $this->waitForSummary();

        return $address;
    }

    /**
     * Add billing address
     *
     * @param string $country
     * @return array complete address data that was used
     */
    public function addBillingAddress($country='us') {
        $addressProvider = new AoeComponents_Magento_Provider_Address();
        $address = $addressProvider->getAddressField('billing', $country);

        $address['email'] = Menta_ComponentManager::get('AoeComponents_Magento_Pages_CustomerAccount')->createNewMailAddress('oscbillling');

        $this->getTest()->typeAndLeave("id=billing:firstname", $address['firstname']);
        $this->getTest()->typeAndLeave("id=billing:lastname", $address['lastname']);
        $this->getTest()->typeAndLeave("id=billing:email", $address['email']);
        $this->getTest()->typeAndLeave("id=billing:confirm_email", $address['email']);
        $this->getTest()->typeAndLeave("id=billing:telephone", $address['phone']);
        $this->getTest()->typeAndLeave("id=billing:street1", $address['street1']);
        $this->getTest()->typeAndLeave("id=billing:street2", $address['street2']);
        $this->getTest()->typeAndLeave("id=billing:city", $address['city']);
        $this->getTest()->typeAndLeave("id=billing:postcode", $address['postcode']);
        $this->getTest()->typeAndLeave("id=billing:company", $address['company']);
        $this->getTest()->select("id=billing:country_id", "label=".$address['country']);
        if (isset($address['region']) && $address['region']) {
            $this->getTest()->select("id=billing:region_id", "label=".$address['region']);
        }
        //$this->getSession()->keys(\WebDriver\Key::TAB);
        sleep(1);   // to keep order of ajax requests
        $this->waitForSummary();

        return $address;
    }

    /**
     * Get order id from order success page
     *
     * @author Joerg Winkler <joerg.winkler@aoemedia.de>
     * @author Fabrizio Branca <fabrizio.branca@aoemedia.de>
     * @return integer Order ID
     */
    public function getOrderIdFromSuccessPage() {
        $waitHelper = Menta_ComponentManager::get('Menta_Component_Helper_Wait'); /* @var $waitHelper Menta_Component_Helper_Wait */
        $this->getTest()->assertTrue(
            $waitHelper->waitForElementPresent("//h1[contains(text(),'Sending your order')]"),
            'Waiting for headline "Sending your order" timed out'
        );
        $waitHelper->waitForElementPresent('id=order-id');
        $orderId = $this->getTest()->getText('id=order-id');
        $this->getTest()->assertNotEmpty($orderId, 'No order id found!');
        return $orderId;
    }

    public function assertTotal($expectedPrice, $type, $message='') {
        $price = $this->getTest()->getText('//table[@id="onestepcheckout-totals-summary"]//tr['.AoeComponents_Div::contains($type).']//td[@class="value"]');
        $price = strip_tags($price);
        $this->getTest()->assertEquals($expectedPrice, $price, $message);
    }

    public function assertPriceInSummary($productId, $expectedPrice) {
        $price = $this->getTest()->getText('//tr[@id="product_'.$productId.'"]/td[@class="total"]');
        $price = strip_tags($price);
        $this->getTest()->assertEquals($expectedPrice, $price, 'Prices in summary is not as expected');
    }

    public function submitForm($checkValidationPassed=TRUE) {
        $this->getTest()->assertElementPresent("onestepcheckout-place-order");
        $this->getTest()->click("onestepcheckout-place-order");
        sleep(1);
        if ($checkValidationPassed) {
            $this->getTest()->assertElementNotPresent("css=.validation-failed");
            $this->getHelperAssert()->assertTextNotPresent("Please check red fields below and try again");
            $this->getHelperWait()->waitForTextPresent('Please wait, processing your order', 2);
        }
    }

    /**
     * Get (one step) checkout url
     *
     * @return string
     */
    public function getCheckoutUrl() {
        return '/onestepcheckout/';
    }

    /**
     * Open (one step) checkout
     *
     * @return void
     */
    public function open() {
        $this->getTest()->open($this->getCheckoutUrl());
        $this->getTest()->waitForElementPresent('//form[@id="onestepcheckout-form"]');
        $this->getTest()->assertTitle('One Step Checkout');
    }

    public function doSplitShipping() {

        $session = $this->getSession(); /* @var $session \WebDriver\Session */
        $link = $session->element(\WebDriver\LocatorStrategy::XPATH, "//input[@id='dosplitshipping']");
        $session->moveto(array('element' => $link->getID()));
        $session->click();
        //$this->getTest()->click("//input[@id='dosplitshipping']");
        $this->waitForSummary();
        $this->getTest()->assertTrue($this->getHelperCommon()->isSelected("//input[@id='dosplitshipping']"));
    }

    public function noSplitShipping() {

        $session = $this->getSession(); /* @var $session \WebDriver\Session */
        $link = $session->element(\WebDriver\LocatorStrategy::XPATH, "//input[@id='nosplitshipping']");
        $session->moveto(array('element' => $link->getID()));
        $session->click();
        //$this->getTest()->click("//input[@id='nosplitshipping']");
        $this->waitForSummary();
        $this->getTest()->assertTrue($this->getHelperCommon()->isSelected("//input[@id='nosplitshipping']"));
    }

}