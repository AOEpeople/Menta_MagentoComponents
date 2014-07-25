<?php

/**
 * Components for one page checkout
 */
class MagentoComponents_Pages_OnePageCheckout extends Menta_Component_AbstractTest
{
    /**
     * Path for continue button in checkout
     *
     * @return string
     */
    protected function _getContinueButtonXPath()
    {
        return '//span[' . Menta_Util_Div::containsText($this->__('Continue')) . ']';
    }

    /**
     * Path for login button in checkout
     * @return string
     */
    public function getLoginButtonPath()
    {
        return '//div[@id="checkout-step-login"]//span[contains(text(),"Login")]';
    }

    /**
     * Path for place order button
     *
     * @return string
     */
    public function getPlaceOrderButtonPath()
    {
        return '//div[@id="checkout-review-submit"]//span[' .
        Menta_Util_Div::containsText($this->__('Place Order')) . ']';
    }

    /**
     * Path for checkbox "Save account for later use" (or other thing to click on)
     *
     * @return string
     */
    public function getSaveAccountCheckPath()
    {
        return "id=id_create_account";
    }

    /**
     * Path for subtotal price in summary
     *
     * @return string
     */
    public function getSubtotalXpath()
    {
        return "//tfoot//td[". Menta_Util_Div::containsText('Subtotal') .
        "]/following-sibling::td/span[" . Menta_Util_Div::contains('price') . "]";
    }

    /**
     * Path for grand total price in summary
     *
     * @return string
     */
    public function getGrandTotalXpath()
    {
        return "//table[@id='checkout-review-table']/tfoot/tr[" .
        Menta_Util_Div::contains('last') . "]//td[2]";
    }

    /**
     * Path for flat rate shipping price in summary
     *
     * @return string
     */
    public function getShippingXpath()
    {
        return "//tfoot//td[". Menta_Util_Div::containsText('(Flat Rate - Fixed)') .
        "]/following-sibling::td/span[" . Menta_Util_Div::contains('price') . "]";
    }

    /**
     *
     * @return string
     */
    public function getTaxXpath()
    {
        return "//tfoot//td[". Menta_Util_Div::containsText('Tax') .
        "]/following-sibling::td/span[" . Menta_Util_Div::contains('price') . "]";
    }

    /**
     * Go trough default magento checkout using default configuration and enable methods (shipping, payment)
     *
     * @param bool $newsletter
     */
    public function goThroughCheckout($newsletter = false)
    {
        $this->open();

        $this->finishStep('billingAddress');

        $this->waitForShippingMethod();
        $this->finishStep('shippingMethod');

        $this->waitForPaymentMethod();
        $this->selectPaymentMethodCheckmo();
        $this->finishStep('paymentMethod');

        $this->waitForReview();
        $this->submitForm();
    }

    /**
     * Log in with existing user
     *
     * @param $login
     * @param $password
     */
    public function signInWithExistingAccount($login, $password)
    {
        $this->getHelperCommon()->type('id=login-email', $login, true, false);
        $this->getHelperCommon()->type('id=login-password', $password, true, false);
        $this->getHelperAssert()->assertElementPresent($this->getLoginButtonPath());
        $this->getHelperCommon()->click($this->getLoginButtonPath());
    }

    /**
     * Assert user is log in
     */
    public function assertUserLogged()
    {
        $this->getHelperAssert()->assertElementNotPresent('//div[@class="step-title"]//h2[contains(text(),"' .
            $this->__("Checkout Method") . '")]');
    }

    /**
     * Open (one page) checkout
     *
     * @return void
     */
    public function open()
    {
        $this->getHelperCommon()->open($this->getCheckoutUrl());
        $this->getHelperWait()->waitForElementPresent('//*[@id="checkoutSteps"]', 10);
        $this->getHelperAssert()->assertTitle($this->__('Checkout'));
    }

    /**
     * Get (one step) checkout url
     *
     * @return string
     */
    public function getCheckoutUrl()
    {
        return '/checkout/onepage/';
    }

    /**
     * Set checkout method
     *
     * @param $method register|guest
     */
    public function setCheckoutMethod($method)
    {
        if ($method == 'register' || $method == 'guest') {
            $this->getHelperAssert()->assertElementPresent("//label[@for='login:$method']");
            $this->getHelperCommon()->click("//label[@for='login:$method']");
        }
    }

    /**
     * Finish one of the checkout steps
     *
     * @param $step
     */
    public function finishStep($step)
    {
        if ($step == 'checkoutMethod') {
            $buttonPath = '//*[@id="onepage-guest-register-button"]';
        } else if ($step == 'billingAddress') {
            $buttonPath = '//div[@id="billing-buttons-container"]' . $this->_getContinueButtonXPath();
        } else if ($step == 'shippingAddress') {
            $buttonPath = '//div[@id="shipping-buttons-container"]' . $this->_getContinueButtonXPath();
        } else if ($step == 'shippingMethod') {
            $buttonPath = '//div[@id="shipping-method-buttons-container"]' . $this->_getContinueButtonXPath();
        } else if ($step == 'paymentMethod') {
            $buttonPath = '//div[@id="payment-buttons-container"]' . $this->_getContinueButtonXPath();
        }
        $this->getHelperAssert()->assertElementPresent($buttonPath);
        $this->getHelperCommon()->click($buttonPath);
    }

    /**
     * Add shipping|billing address
     *
     * @param string $country
     * @param $type
     * @param array $address
     * @throws Exception
     * @return array complete address data that was used
     */
    public function addAddress($country='us', $type, array $address=null)
    {
        if (!in_array($type, array('billing', 'shipping'))) {
            throw new Exception('Invalid address type');
        }

        if (is_null($address)) {
            $addressProvider = new MagentoComponents_Provider_Address();
            $address = $addressProvider->getAddressField($type, $country);
        }

        if ($type == 'billing' && !isset($address['email'])) {
            $address['email'] = Menta_ComponentManager::get('MagentoComponents_Pages_CustomerAccount')->createNewMailAddress('oscbillling');
        }

        foreach ($address as $field => $value) {
            if (in_array($field, array('country', 'region'))) {
                $this->getHelperCommon()->select("id={$type}:{$field}_id", "label=" . $value);
            } else {
                $this->getHelperCommon()->type("id=$type:$field", $value);
            }
        }

        return $address;
    }

    /**
     * Enter valid visa credit card data
     */
    public function enterValidCreditCardDataVisa()
    {
        $this->getHelperWait()->waitForElementVisible('//input[@id="p_method_braintree"]');
        $this->getHelperCommon()->click('//input[@id="p_method_braintree"]');
        $this->getHelperWait()->waitForElementVisible('//ul[@id="payment_form_braintree"]');
        $this->getHelperCommon()->select('id=braintree_cc_type', 'label=Visa');
        $this->getHelperCommon()->type('id=braintree_cc_number', '4111111111111111');
        $this->getHelperCommon()->select('id=braintree_expiration', 'label=03 - March');
        $this->getHelperCommon()->select('id=braintree_expiration_yr', 'label=2020');
        $this->getHelperCommon()->type('id=braintree_cc_cid', '123');
    }

    /**
     * Submit order form
     *
     * @param bool $checkValidationPassed
     * @param int $wait in seconds
     */
    public function submitForm($checkValidationPassed = TRUE, $wait = 1)
    {
        $this->getHelperAssert()->assertElementPresent($this->getPlaceOrderButtonPath());
        $this->getHelperCommon()->click($this->getPlaceOrderButtonPath());
        sleep($wait);
        if ($checkValidationPassed) {
            $this->getHelperAssert()->assertElementNotPresent("css=.validation-failed");
            $this->getHelperAssert()->assertTextNotPresent("Please check red fields below and try again");
            $this->getHelperWait()->waitForTextPresent($this->__('Sending your order'), 2);
        }
    }

    /**
     * Toggle ship to different address
     */
    public function toggleShipToDifferentAddress()
    {
        $this->getHelperAssert()->assertElementPresent('//input[@id="billing:use_for_shipping_no"]');
        $this->getHelperCommon()->click('//input[@id="billing:use_for_shipping_no"]');
    }

    /**
     * Get order id from order success page
     * @return integer Order ID
     */
    public function getOrderIdFromSuccessPage()
    {
        $this->getHelperWait()->waitForElementPresent('//h1[contains(text(), "Your order has been received.")]');
        $element = $orderNumber = $this->getHelperCommon()
            ->getElement('//p[contains(text(),"Your order")]//a')->getAttribute('href');

        $orderId = array_pop(explode('/', rtrim($element, '/')));

        $this->getTest()->assertNotEmpty($orderId, 'No order id found!');

        return $orderId;
    }

    /**
     * Get order number from order success page
     *
     * @return mixed
     */
    public function getOrderNumberFromSuccessPage()
    {
        $this->getHelperWait()->waitForElementPresent('//h1[contains(text(), "Your order has been received.")]');
        $orderNumber = $this->getHelperCommon()
            ->getElement('//p[contains(text(),"Your order")]//a')->getText();
        return $orderNumber;
    }

    /**
     * Save account for latter use..
     */
    public function saveAccountForLaterUse()
    {
        $commonHelper = Menta_ComponentManager::get('Menta_Component_Helper_Common');
        /* @var $commonHelper Menta_Component_Helper_Common */
        $password = $this->getConfiguration()->getValue('testing.frontend.password');
        $commonHelper->type('billing:customer_password', $password, true, true);
        $commonHelper->type('billing:confirm_password', $password, true, true);
    }

    /**
     * Select Flat Rate Shipping method
     */
    public function selectShippingFlatRate()
    {
        $this->selectShipping('Flat Rate');
    }

    /**
     * Select any shipping method
     *
     * @param $name
     */
    public function selectShipping($name)
    {
        $this->getHelperCommon()->click("//td[@class='shipping-name']/label[text()='$name']");
    }

    /**
     * Select Check / Money order payment method
     */
    public function selectPaymentMethodCheckmo()
    {
        $this->selectPaymentMethod('checkmo');
    }

    /**
     * Select any payment method
     * @param $code
     */
    public function selectPaymentMethod($code)
    {
        $this->getHelperCommon()->click("//dl[@id='checkout-payment-method-load']//input[@id='p_method_$code']");
    }

    /**
     * Select newsletter checkbox
     */
    public function selectNewsletterCheckbox()
    {
        $this->getHelperCommon()->click("//input[@id='id_subscribe_newsletter']");
    }

    /**
     * Assert Terms and Conditions
     */
    public function acceptTermsAndConditions()
    {
        $this->getHelperAssert()->assertElementPresent("//label[@for='id_accept_terms']", 'Could not find terms and conditions checkbox');
        if (!$this->getHelperCommon()->isSelected("//input[@id='id_accept_terms']")) {
            $this->getHelperCommon()->click("//input[@id='id_accept_terms']");
        }
    }

    /**
     * Wait for billing method step
     */
    public function waitForBillingAddress()
    {
        $this->getHelperWait()->waitForElementVisible('//*[@id="checkout-step-billing"]');
    }

    /**
     * Wait for shipping method step
     */
    public function waitForShippingAddress()
    {
        $this->getHelperWait()->waitForElementVisible('//*[@id="checkout-step-shipping"]');
    }

    /**
     * Wait for shipping method step
     */
    public function waitForShippingMethod()
    {
        $this->getHelperWait()->waitForElementVisible('//*[@id="checkout-shipping-method-load"]');
    }

    /**
     * Wait for payment method step
     */
    public function waitForPaymentMethod()
    {
        $this->getHelperWait()->waitForElementVisible('//*[@id="checkout-step-payment"]');
    }

    /**
     * Wait for review step (last step)
     */
    public function waitForReview()
    {
        $this->getHelperWait()->waitForElementVisible('//*[@id="checkout-review-load"]');
    }

    /**
     * Assert product price in summary
     *
     * @param $count row number in table
     * @param $expectedPrice
     */
    public function assertPriceInSummary($count, $expectedPrice)
    {
        $price = $this->getHelperCommon()->getText('//tbody/tr[' .$count. ']/td[2]//span[@class="cart-price"]');
        $this->getTest()->assertEquals(
            $this->getHelper()->normalize($expectedPrice),
            $this->getHelper()->normalize($price), 'Prices in summary is not as expected');
    }

    /**
     * Assert shipping price in summary
     *
     * @param $price
     */
    public function assertShippingPrice($price)
    {
        /* @var Menta_Component_Helper_Common */
        $actualPrice
            = $this->getHelperCommon()->getText($this->getShippingXpath());
        $this->getTest()->assertEquals(
            $this->getHelper()->normalize($price),
            $this->getHelper()->normalize($actualPrice),
            'Shipping does not match'
        );
    }

    /**
     * Assert subtotal in summary
     *
     * @param $expectedPrice
     */
    public function assertSubtotal($expectedPrice)
    {
        $price = $this->getHelperCommon()->getText($this->getSubtotalXpath());
        $this->getTest()->assertEquals(
            $this->getHelper()->normalize($price),
            $this->getHelper()->normalize($expectedPrice),
            'Subtotal does not match'
        );
    }

    /**
     * Assert tax price in summary
     *
     * @param $expectedPrice
     */
    public function assertTax($expectedPrice)
    {
        $price = $this->getHelperCommon()->getText($this->getTaxXpath());
        $this->getTest()->assertEquals(
            $this->getHelper()->normalize($price),
            $this->getHelper()->normalize($expectedPrice), 'Tax does not match');
    }

    /**
     * Assert grand total in summary
     *
     * @param $expectedPrice
     */
    public function assertGrandTotal($expectedPrice)
    {
        $price = $this->getHelperCommon()->getText($this->getGrandTotalXpath());

        $this->getTest()->assertEquals(
            $this->getHelper()->normalize($price),
            $this->getHelper()->normalize($expectedPrice), 'Grand total does not match');
    }

    /**
     * Assert billing country
     * 
     * @param $countryCode
     */
    public function assertBillingCountry($countryCode)
    {
        $selected = $this->getHelperCommon()->getSelectedValue('//select[@id="billing:country_id"]');
        $this->getTest()->assertEquals($countryCode, $selected);
    }

    /**
     * Prepare shipping address fields for logged in users
     *
     * @param string $conditionForOptionToSelect
     */
    public function prepareShippingAddressFieldsForLoggedInUsers($conditionForOptionToSelect = "label=New Address")
    {
        $this->getHelperWait()->waitForElementPresent("id=shipping-address-select");
        $this->getHelperCommon()->select("id=shipping-address-select", $conditionForOptionToSelect);
    }

    /**
     * Toggle ship to the same address
     */
    public function toogleShipToTheSameAddress()
    {
        $this->getHelperCommon()->click("id=billing:use_for_shipping_yes");
    }

    /**
     * selected save billing address
     * 
     * @param string $conditionForOptionToSelect
     */
    public function selectSavedBillingAddress($conditionForOptionToSelect = "value=")
    {
        $this->getHelperWait()->waitForElementPresent("id=billing-address-select");
        $this->getHelperCommon()->select("id=billing-address-select", $conditionForOptionToSelect);
    }

    /**
     * Get Magento Helper
     *
     * @return MagentoComponents_Helper
     */
    public function getHelper()
    {
        return Menta_ComponentManager::get('MagentoComponents_Helper');
    }
}