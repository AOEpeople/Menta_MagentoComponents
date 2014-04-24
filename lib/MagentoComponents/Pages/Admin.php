<?php

/**
 * Components for admin page
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
     * Get header location
     *
     * @return string
     */
    public function getHeaderLocation()
    {
        return 'css=.content-header h3';
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
        $this->getHelperCommon()->open($this->getAdminUrl());
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

    /**
     * Check username in header
     *
     * @param string $username
     * @author Fabrizio Branca
     * @since 2014-04-22
     */
    public function assertUsernameInHeader($username)
    {
        $this->getHelperAssert()->assertElementContainsText('css=.header-right .super', sprintf($this->__('Logged in as %s'), $username));
    }

    /**
     * Get menu item element
     * Pass the parent menu item as second parameter to find submenu items
     *
     * @param string $label
     * @param string|\WebDriver\Element $parent
     * @return \WebDriver\Element
     */
    public function getMenuItemElement($label, $parent=null)
    {
        return $this->getHelperCommon()->getElement("//li/a/span[text()='$label']", $parent);
    }

    /**
     * Click menu item
     * Will traverse to menu item
     * e.g. array('System', 'Permission', 'Users')
     *
     * @param array $items
     * @author Fabrizio Branca
     * @since 2014-04-22
     */
    public function clickMenuItem(array $items)
    {
        $element = null;
        $i=0;
        foreach ($items as $item) {
            $element = $this->getMenuItemElement($item, $element);
            $i++;
            if ($i<count($items)) {
                $this->getHelperCommon()->moveTo($element);
            } else {
                $this->getHelperCommon()->click($element);
            }
        }
    }

    /**
     * Assert header
     *
     * @param string $header
     */
    public function assertHeader($header)
    {
        return $this->getHelperAssert()->assertElementContainsText($this->getHeaderLocation(), $header);
    }

}