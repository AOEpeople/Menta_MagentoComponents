<?php

class AoeComponents_Magento_Pages_OneStepCheckout extends Menta_Component_AbstractTest {

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

	/**
	 * Get (one step) checkout url
	 *
	 * @return string
	 */
	public function getCheckoutUrl() {
		return '/onestepcheckout/';
	}

	/**
	 * Path for checkbox "Save account for later use" (or other thing to click on)
	 * @return string
	 */
	public function getSaveAccountCheckPath() {
		return "id=id_create_account";
	}
	/**
	 * @deprecated
	 * @author Joerg Winkler <joerg.winkler@aoemedia.de>
	 */
	public function chooseCheckOutOptionsAndContinue(){

		// enter credit card information
		$this->enterValidCreditCardDataVisa();
		$this->acceptTermsAndConditions();

		$this->waitForSummary();
		$this->submitForm();
	}

	public function waitForSummary() {
		// $this->getTest()->waitForElementPresent("//div[@class='onestepcheckout-summary']");
		$this->getTest()->waitForElementPresent("id=onestepcheckout-totals-summary");
	}

	public function submitForm($checkValidationPassed=TRUE) {
		$this->getTest()->assertElementPresent("onestepcheckout-place-order");
		$this->getTest()->click("onestepcheckout-place-order");
		sleep(1);
		if ($checkValidationPassed) {
			$this->getTest()->assertElementNotPresent("css=.validation-failed");
			$this->getTest()->getHelperAssert()->assertTextNotPresent("Please check red fields below and try again");
			$this->getTest()->getHelperWait()->waitForTextPresent('Please wait, processing your order', 2);
		}
	}

	public function assertPriceInSummary($productId, $expectedPrice) {
		$price = $this->getTest()->getText('//tr[@id="product_'.$productId.'"]/td[@class="total"]');
		$price = strip_tags($price);
		$this->getTest()->assertEquals($expectedPrice, $price, 'Prices in summary is not as expected');
	}

	public function assertShipping($expectedPrice) {
		$this->assertTotal($expectedPrice, 'shipping', 'Shipping price does not match');
	}

	public function assertTax($expectedPrice) {
		$this->assertTotal($expectedPrice, 'tax', 'Tax does not match');
	}

	public function assertNoTaxVisible() {
		$this->getTest()->assertElementNotPresent('//table[@id="onestepcheckout-totals-summary"]//tr['.AoeComponents_Div::contains('tax').']');
	}

	public function assertGrandTotal($expectedPrice) {
		$this->assertTotal($expectedPrice, 'grand-total', 'Grand total does not match');
	}

	public function assertSubtotal($expectedPrice) {
		$this->assertTotal($expectedPrice, 'subtotal', 'Subtotal does not match');
	}

	public function assertTotal($expectedPrice, $type, $message='') {
		$price = $this->getTest()->getText('//table[@id="onestepcheckout-totals-summary"]//tr['.AoeComponents_Div::contains($type).']//span['.AoeComponents_Div::contains('price').']');
		$price = strip_tags($price);
		$this->getTest()->assertEquals($expectedPrice, $price, $message);
	}

	public function assertBillingCountry($countryCode) {
		$commonHelper = Menta_ComponentManager::get('Menta_Component_Helper_Common');
		$selected = $commonHelper->getSelectedValue('//select[@id="billing:country_id"]');
		$this->getTest()->assertEquals($countryCode, $selected);
	}

	/**
	 * Shipping price form middle column
	 * @param $name e.g. standard, priority...
	 * @param $price
	 */
	public function assertShippingPrice($name, $price){
		$commonHelper = Menta_ComponentManager::get('Menta_Component_Helper_Common'); /* @var Menta_Component_Helper_Common */
		$actualPrice = $commonHelper->getText("//tr[@class='type_".$name."']/td[@class='shipping-price']");
		$this->getTest()->assertEquals($price, $actualPrice);
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
			$waitHelper->waitForElementPresent("//h1[contains(text(),'Your order has been received')]"),
			'Waiting for headline "Your order has been received" timed out'
		);
		$waitHelper->waitForElementPresent('id=order-id');
		$orderId = $this->getTest()->getText('id=order-id');
		$this->getTest()->assertNotEmpty($orderId, 'No order id found!');
		return $orderId;
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
		$this->getSession()->keys(WebDriver_Element::Tab);
		$this->waitForSummary();

		return $address;
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

		$this->waitForSummary();

		return $address;
	}

	public function toogleShipToTheSameAddress() {
		$this->getTest()->click("id=billing:use_for_shipping_yes");
	}

	public function prepareShippingAddressFieldsForLoggedInUsers($conditionForOptionToSelect="label=New Address") {
		$this->toogleShipToTheSameAddress();
		$this->getTest()->waitForElementPresent("id=shipping-address-select");
		$this->getTest()->select("id=shipping-address-select", $conditionForOptionToSelect);
	}

	public function prepareShippingAddressFieldsForNewUsers() {
		//$this->getTest()->click("id=billing:use_for_shipping_yes"); //was not working with pretty checkboxes
		$this->toogleShipToTheSameAddress();
	}

	public function selectSavedBillingAddress($conditionForOptionToSelect = "value=") {
		$this->getTest()->waitForElementPresent("id=billing-address-select");
		$this->getTest()->select("id=billing-address-select", $conditionForOptionToSelect);
	}

	public function saveAccountForLaterUse() {
		$this->getTest()->click($this->getSaveAccountCheckPath());
		$this->getTest()->typeAndLeave("id=billing:customer_password", "test1234");
		$this->getTest()->typeAndLeave("id=billing:confirm_password", "test1234");
	}

	public function addNewCreditCard() {
		return;

		// this feature currently is disabled. So every checkout will get a fresh form by default
		$commonHelper = Menta_ComponentManager::get('Menta_Component_Helper_Common'); /* @var $commonHelper Menta_Component_Helper_Common */
		$commonHelper->select('braintree_cc_token','label=Add new card');
	}

	public function enterValidCreditCardDataVisa() {
	//	$this->getTest()->click("//dl[@id='checkout-payment-method-load']/dt//label");

		$this->getTest()->waitForAjaxCompletedJquery();
		$this->getTest()->waitForAjaxCompletedPrototype();

		$this->getTest()->select('id=braintree_cc_type', 'label=Visa');
		$this->getTest()->typeAndLeave('id=braintree_cc_number', '4111111111111111');
		$this->getTest()->fireEvent('id=braintree_cc_number', 'blur');
		$this->getTest()->select('id=braintree_expiration', 'label=03 - March');
		$this->getTest()->select('id=braintree_expiration_yr', 'label=2013');
		$this->getTest()->typeAndLeave('id=braintree_cc_cid', '123');
		$this->getTest()->fireEvent('id=braintree_cc_cid', 'blur');
	}

	public function enterInvalidCreditCardDataVisa() {
	//	$this->getTest()->click("//dl[@id='checkout-payment-method-load']/dt//label");

		$this->getTest()->waitForAjaxCompletedJquery();
		$this->getTest()->waitForAjaxCompletedPrototype();

		$this->getTest()->select('id=braintree_cc_type', 'label=Visa');
		$this->getTest()->typeAndLeave('id=braintree_cc_number', '4111114561111123');
		$this->getTest()->select('id=braintree_expiration', 'label=03 - March');
		$this->getTest()->select('id=braintree_expiration_yr', 'label=2013');
		$this->getTest()->typeAndLeave('id=braintree_cc_cid', '999');
	}

	public function acceptTermsAndConditions() {
		$this->getTest()->assertElementPresent("//label[@for='id_accept_terms']", 'Could not find terms and conditions checkbox');
		// $this->getTest()->click("id=id_accept_terms");
		$this->getTest()->click("//input[@id='id_accept_terms']");
	}

	public function selectShippingMethodStandard() {
		$this->selectShipping('Standard');
	}

	public function selectShippingMethodPriority() {
		$this->selectShipping('Priority');
	}

	public function selectShippingMethodExpress() {
		$this->selectShipping('Express');
	}

	public function selectShipping($name) {
		// $this->getTest()->assertElementPresent("//td[@class='shipping-name']/label[text()='$name']");
		//$this->getTest()->click("//td[@class='shipping-name']/label[text()='$name']");
		$this->getTest()->click("//dl[@class='shipping-methods']/label[text()='$name']");
		$this->waitForSummary();
	}

    public function selectNewsletterCheckbox() {
        $this->getTest()->click("//input[@id='id_subscribe_newsletter']");
    }

	public function doSplitShipping() {

		$session = $this->getSession(); /* @var $session WebDriver_Session */
		$link = $session->element(WebDriver_Container::XPATH, "//input[@id='dosplitshipping']");
		$session->moveto(array('element' => $link->getID()));
		$session->click();
		//$this->getTest()->click("//input[@id='dosplitshipping']");
		$this->waitForSummary();
		$this->getTest()->assertTrue($this->getTest()->getHelperCommon()->isSelected("//input[@id='dosplitshipping']"));
	}

	public function noSplitShipping() {

		$session = $this->getSession(); /* @var $session WebDriver_Session */
		$link = $session->element(WebDriver_Container::XPATH, "//input[@id='nosplitshipping']");
		$session->moveto(array('element' => $link->getID()));
		$session->click();
		//$this->getTest()->click("//input[@id='nosplitshipping']");
		$this->waitForSummary();
		$this->getTest()->assertTrue($this->getTest()->getHelperCommon()->isSelected("//input[@id='nosplitshipping']"));
	}

}