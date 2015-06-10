<?php

/**
 * Components for customer account page
 */
class MagentoComponents_Pages_CustomerAccount extends Menta_Component_AbstractTest
{
    /**
     * Path for element which is present only on dashboard page
     *
     * @return string
     */
    public function getDashboardIndicatorPath()
    {
        return "//div[@class='my-account']/div[@class='dashboard']";
    }

    /**
     * Path for create account button on login/register page
     *
     * @return string
     */
    public function getSplitPageRegistrationButtonPath()
    {
        return "//div[" . Menta_Util_Div::contains('account-login') . "]//button[" . Menta_Util_Div::contains($this->__('Create an Account'), 'title') . "]";
    }

    /**
     * Path for login button on login/register page
     *
     * @return string
     */
    public function getSplitPageLoginButtonPath()
    {
        return '//button[' . Menta_Util_Div::contains($this->__('Login'), 'title') . ']';
    }

    /**
     * Path for newsletter indicator
     *
     * @return string
     */
    public function getNewsletterCheckboxIndicatorPath()
    {
        return "id=is_subscribed";
    }

    /**
     * Path for edit newsletter in customer dashboard page
     *
     * @return string
     */
    public function getNewsletterEditPathInDashoboard()
    {
        return "//div[" . Menta_Util_Div::contains('dashboard') . "]//div[" . Menta_Util_Div::contains('col-2')
        . "][1]//a[" . Menta_Util_Div::containsText($this->__('Edit')) . "]";
    }

    /**
     * Path for submit registration button on create account page
     *
     * @return string
     */
    public function getRegistrationSubmitButtonPath()
    {
        return "//button[@type='submit'][" .
        Menta_Util_Div::contains($this->__('Submit'), 'title') . "]";
    }

    /**
     * Login
     *
     * @param string $username
     * @param string $password
     */
    public function login($username = NULL, $password = NULL)
    {
        if (is_null($username) || is_null($password)) {
            $username = $this->getConfiguration()->getValue('testing.frontend.user');
            $password = $this->getConfiguration()->getValue('testing.frontend.password');
        }
        $this->openSplitLoginOrRegister();

        $this->getHelperCommon()->type("//input[@id='email']", $username, true, true);
        $this->getHelperCommon()->type("//input[@id='pass']", $password, true, true);

        Menta_Events::dispatchEvent('MagentoComponents_Pages_CustomerAccount->login:beforeSubmit', array(
            'component' => $this
        ));

        $this->getHelperCommon()->click($this->getSplitPageLoginButtonPath());

        $this->getHelperAssert()->assertBodyClass('customer-account-index');
    }

    /**
     * Logout
     */
    public function logout()
    {
        $helper = Menta_ComponentManager::get('MagentoComponents_Helper');
        /* @var $helper MagentoComponents_Helper */
        $helper->openLinksMenu();
        $this->getHelperWait()->waitForElementPresent($helper->getLogoutLinkPath());
        $this->getHelperCommon()->click($helper->getLogoutLinkPath());
        $this->getHelperWait()->waitForElementPresent("//h1[" . Menta_Util_Div::containsText($this->__('You are now logged out')) . "]");
        $this->getHelperAssert()->assertElementPresent("//h1[" . Menta_Util_Div::containsText($this->__('You are now logged out')) . "]");
    }

    /**
     * Open login/register page
     *
     * @return void
     */
    public function openSplitLoginOrRegister()
    {
        $this->getHelperCommon()->open('/customer/account/login/');
        $this->getHelperAssert()->assertBodyClass('customer-account-login');
        $this->getHelperAssert()->assertTextPresent($this->__('Login or Create an Account'));
        $this->getHelperAssert()->assertTextPresent($this->__('New Here?'));
        $this->getHelperAssert()->assertTextPresent($this->__('Already registered?'));
    }

    /**
     * Open registration page
     */
    public function openRegistrationPage()
    {
        $this->getHelperCommon()->open('/customer/account/create/');
        $this->getHelperAssert()->assertBodyClass('customer-account-create');
    }

    /**
     * Got to dashboard
     *
     * @return void
     */
    public function openDashboard()
    {
        $this->getHelperCommon()->open('/customer/account/');
        $this->assertIsOnDashboard();
    }

    /**
     * Assert user is in My account dashboard
     */
    public function assertIsOnDashboard()
    {
        $this->getHelperAssert()->assertTitle($this->__('My Account'));
        $this->getHelperAssert()->assertTextPresent($this->__('My Dashboard'));
        $this->getHelperAssert()->assertElementPresent($this->getDashboardIndicatorPath());
    }

    /**
     * Got to order history page
     *
     * @return void
     */
    public function openOrderHistory()
    {
        $this->getHelperCommon()->open('/sales/order/history/');
        $this->getHelperAssert()->assertTitle($this->__('My Orders'));
    }

    /**
     * Open an order from the order history
     *
     * @param string $orderId
     * @return void
     */
    public function openOrder($orderId)
    {
        $this->getHelperCommon()->open('/order/view/order_id/' . $orderId . '/');
    }

    /**
     * Open address info
     *
     * @return void
     */
    public function openAddressInfo()
    {
        $this->getHelperCommon()->open('/customer/address/');
    }

    /**
     * Got to forgot password page
     *
     * @return void
     */
    public function openForgotPassword()
    {
        $this->getHelperCommon()->open('/customer/account/forgotpassword/');
    }

    /**
     * Logout user using direct link
     */
    public function logoutViaOpen()
    {
        $this->getHelperCommon()->open('/customer/account/logout/');
    }

    /**
     * Create new mail address
     *
     * @param string $type
     * @return mixed
     * @throws Exception
     */
    public function createNewMailAddress($type = '')
    {
        if (!$this->getConfiguration()->issetKey('testing.newmailaddresspattern')) {
            throw new Exception('No configuration for testing.newmailaddresspattern found');
        }
        $template = $this->getConfiguration()->getValue('testing.newmailaddresspattern');
        $replace = array(
            '###TYPE###' => $type,
            '###RANDOM###' => Menta_Util_Div::createRandomString(4),
            '###TIME###' => time(),
            '###TESTID###' => $this->getTest()->getTestId()
        );
        return str_replace(array_keys($replace), array_values($replace), $template);
    }

    /**
     * Create random password
     *
     * @param int $length
     * @return string
     */
    public function createRandomPassword($length = 8)
    {
        return Menta_Util_Div::createRandomString($length);
    }

    /**
     * Create random user name
     *
     * @param int $length
     * @return string
     */
    public function createRandomName($length = 8)
    {
        $name = Menta_Util_Div::createRandomString($length, 'abcdefghijklmnopqrstuvwxyz');
        return ucfirst($name);
    }
}