<?php

class MagentoComponents_Pages_CustomerAccount extends Menta_Component_AbstractTest {

	/**
	 * Path for element which is present only on dashboard page
	 * @return string
	 */
	public function getDashboardIndicatorPath() {
		return "//div[@class='my-account']/div[@class='dashboard']";
	}


	public function getSplitPageRegistrationButtonPath() {
        return "//div[contains(@id,'account-login')]//a[contains(text(),'Register')]";
	}

    /**
     * Path for edit newsletter in customer dashboard page
     * @return string
     */
    public function getNewsletterEditPathInDashoboard()
    {
        return "//div[" . Menta_Util_Div::contains('dashboard') . "]//div[" . Menta_Util_Div::contains('col-2')
        . "][1]//a[" . Menta_Util_Div::containsText($this->__('Edit')) . "]";
    }

	/**
	 * Open login/register page
	 *
	 * @return void
	 */
	public function openSplitLoginOrRegister() {
		$this->getHelperCommon()->open('/customer/account/login/');
		$this->getHelperAssert()->assertBodyClass('customer-account-login');
		$this->getHelperAssert()->assertTextPresent($this->__('Login or Create an Account'));
		$this->getHelperAssert()->assertTextPresent($this->__('New Customers'));
		$this->getHelperAssert()->assertTextPresent($this->__('Registered Customers'));
	}

	/**
	 * Got to dashboard
	 *
	 * @return void
	 */
	public function openDashboard() {
		$this->getHelperCommon()->open('/customer/account/');
		$this->assertIsOnDashboard();
	}

	public function assertIsOnDashboard() {
		$this->getHelperAssert()->assertTitle($this->__('My Account'));
		$this->getHelperAssert()->assertTextPresent($this->__('My Dashboard'));
		$this->getHelperAssert()->assertElementPresent($this->getDashboardIndicatorPath());
	}

	/**
	 * Got to history
	 *
	 * @return void
	 */
	public function openOrderHistory() {
		$this->getHelperCommon()->open('/sales/order/history/');
		$this->getHelperAssert()->assertTitle($this->__('My Orders'));
	}

	/**
	 * Open an order from the order history
	 *
	 * @param string $orderId
	 * @return void
	 */
	public function openOrder($orderId) {
		$this->getHelperCommon()->open('/order/view/order_id/'.$orderId.'/');
	}

	/**
	 * Open address info
	 *
	 * @return void
	 */
	public function openAddressInfo() {
		$this->getHelperCommon()->open('/customer/address/');
	}

	/**
	 * Login
	 *
	 * @param string $username
	 * @param string $password
	 */
	public function login($username=NULL, $password=NULL) {
		if (is_null($username) || is_null($password)) {
			$username = $this->getConfiguration()->getValue('testing.frontend.user');
			$password = $this->getConfiguration()->getValue('testing.frontend.password');
		}
		$this->openSplitLoginOrRegister();
		//$this->getHelperCommon()->click("//ul[@class='links personal-items']/li[@class='first']/a");
		$this->getHelperCommon()->type("//input[@id='email']", $username, true, true);
		$this->getHelperCommon()->type("//input[@id='pass']", $password, true, true);

		Menta_Events::dispatchEvent('MagentoComponents_Pages_CustomerAccount->login:beforeSubmit', array(
			'component' => $this
		));

		$this->getHelperCommon()->click($this->getSplitPageLoginButtonPath());

		$this->getHelperAssert()->assertBodyClass('customer-account-index');
	}

    public function getSplitPageLoginButtonPath(){
        return '//button['. Menta_Util_Div::contains($this->__('Login'), 'title') . ']';
    }

	/**
	 * Got to forgot password page
	 *
	 * @return void
	 */
	public function openForgotPassword() {
		$this->getHelperCommon()->open('/customer/account/forgotpassword/');
	}

	/**
	 * Logout
	 *
	 */
	public function logout() {
        /* @var $helper MagentoComponents_Helper*/
        $helper = Menta_ComponentManager::get('MagentoComponents_Helper');
        $this->getHelperCommon()->click($helper->getLogoutLinkPath());
		$this->getHelperWait()->waitForElementPresent("//h1[" . Menta_Util_Div::containsText($this->__('You are now logged out')) . "]");
		$this->getHelperAssert()->assertElementPresent("//h1[" . Menta_Util_Div::containsText($this->__('You are now logged out')) . "]");
	}

	public function logoutViaOpen() {
		$this->getHelperCommon()->open('/customer/account/logout/');
	}

	public function createNewMailAddress($type='') {
		if (!$this->getConfiguration()->issetKey('testing.newmailaddresspattern')) {
			throw new Exception('No configuration for testing.newmailaddresspattern found');
		}
		$template = $this->getConfiguration()->getValue('testing.newmailaddresspattern');
		$replace = array(
			'###TYPE###' => $type,
			'###RANDOM###' => $this->createRandomString(4),
			'###TIME###' => time(),
			'###TESTID###' => $this->getTest()->getTestId()
		);
		return str_replace(array_keys($replace), array_values($replace), $template);
	}

    public function createRandomPassword($length=8) {
		return Menta_Util_Div::createRandomString($length);
	}

	public function createRandomName($length = 8) {
		$name = Menta_Util_Div::createRandomString($length, 'abcdefghijklmnopqrstuvwxyz');
		return ucfirst($name);
	}

	public function createRandomString($length = 8, $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789") {
        return substr(str_shuffle($chars),0, $length);
    }

	/**
	 * Open registration page
	 */
	public function openRegistrationPage() {
		$this->getHelperCommon()->open('/customer/account/create/');
		$this->getHelperAssert()->assertBodyClass('customer-account-create');
	}

}