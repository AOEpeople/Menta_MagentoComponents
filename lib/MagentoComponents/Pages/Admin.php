<?php
/**
 * @author David Robinson <david.robinson@aoemedia.com>
 * @since 19/2/2013
 */
class MagentoComponents_Pages_Admin extends Menta_Component_AbstractTest
{
	protected $defaultLogin = array('username' => 'selenium.user', 'password' => 'test1234', 'adminUrl' => '/admin');

	/**
	 * get username and password from config xml
	 * @return array $user
	 */
	public function getAdminUser()
	{
		$user = array (
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

	/**Z
	 * get admin url from config xml
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
	 * open admin
	 *
	 * @return void
	 */
	public function openAdmin()
	{
		$url = 'http://'.$this->getConfiguration()->getValue('testing.maindomain').$this->getAdminUrl();
		$this->getSession()->open($url);
	}

	/**
	 * log into the admin
	 *
	 * @param string $username
	 * @param string $password
	 */
	public function logIntoAdmin($username, $password)
	{
		$this->getTest()->typeAndLeave('id=username',$username);
		$this->getTest()->typeAndLeave('id=login',$password);
		$formElement = $this->getSession()->element(\WebDriver\LocatorStrategy::XPATH, '//form[@id="loginForm"]');
		$formElement->submit();
	}

	/**
	 * logout from admin
	 */
	public function logoutFromAdmin()
	{
	 	$this->getTest()->waitForElementPresent('//a[@class="link-logout"]');
		$this->getSession()->element(\WebDriver\LocatorStrategy::XPATH, '//a[@class="link-logout"]')->click();
	}

	/**
	 * simple check if login was successful
	 */
	public function loginCheck()
	{
		$this->getTest()->assertTextPresent('Dashboard');
	}
}