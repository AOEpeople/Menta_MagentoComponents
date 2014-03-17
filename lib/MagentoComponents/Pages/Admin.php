<?php

/**
 */
class MagentoComponents_Pages_Admin extends Menta_Component_AbstractTest
{
    protected $defaultLogin = array('username' => 'selenium.user', 'password' => 'test1234', 'adminUrl' => '/admin');

    /**
     * Get logout link xpath
     *
     * @return string
     */
    public function getLogoutLinkXpath()
    {
        return '//a[@class="link-logout"]';
    }

    /**
     * Get login form indicator
     *
     * @return string
     */
    public function getLoginForm()
    {
        return '//form[@id="loginForm"]';
    }
    /**
     * Get admin username and password from config xml
     *
     * @return array $user
     */
    public function getAdminUser()
    {
        $user = array(
            'username' => $this->getConfiguration()->getValue('testing.magento.admin.user'),
            'password' => $this->getConfiguration()->getValue('testing.magento.admin.password')
        );

        if ($user['username'] == null) {
            $user['username'] = $this->defaultLogin['username'];
        }
        if ($user['password'] == null) {
            $user['password'] = $this->defaultLogin['password'];
        }
        return $user;
    }

    /**
     * Get admin url from config xml
     *
     * @return string $adminUrl
     */
    public function getAdminUrl()
    {
        $adminUrl = $this->getConfiguration()->getValue('testing.magento.admin.url');
        if ($adminUrl == null) {
            $adminUrl = $this->defaultLogin['adminUrl'];
        }
        return $adminUrl;
    }

    /**
     * Open admin panel
     *
     * @return void
     */
    public function openAdmin()
    {
        $url = $this->getConfiguration()->getValue('testing.maindomain') . $this->getAdminUrl();
        $this->getSession()->open($url);
    }

    /**
     * Log into the admin panel
     *
     * @param string $username
     * @param string $password
     */
    public function logIntoAdmin($username, $password)
    {
        $this->getHelperCommon()->type('id=username', $username);
        $this->getHelperCommon()->type('id=login', $password);
        $this->getHelperCommon()->getElement($this->getLoginForm())->submit();
    }

    /**
     * Logout from admin panel
     */
    public function logoutFromAdmin()
    {
        $this->getHelperWait()->waitForElementPresent('//a[@class="link-logout"]');
        $this->getHelperCommon()->click($this->getLogoutLinkXpath());
    }

    /**
     * Simple check if login was successful
     */
    public function loginCheck()
    {
        $this->getHelperAssert()->assertTextPresent('Dashboard');
    }
}