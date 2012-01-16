<?php
/**
 * 
 * some basic backend operation functions
 * @author joerg.winkler <joerg.winkler@aoemedia.de>
 *
 */
class AoeComponents_TYPO3_Pages_Backend extends Menta_Component_AbstractTest {
	protected $defaultusername = "selenium.user";
	protected $defaultpassword = "test1234";
	protected $defaulthomeID = "3";
	
	/**
	 * get username and password from config xml
	 * @return array $user
	 */
	public function getBackendUser() {
		$user = array ("username" => $this->getConfiguration ()->getValue ( 'testing.backend.user' ), "password" => $this->getConfiguration ()->getValue ( 'testing.backend.password' ) );
		
		if ($user ["username"] == null) {
			$user ["username"] = $this->defaultusername;
		}
		if ($user ["password"] == null) {
			$user ["password"] = $this->defaultpassword;
		}
		return $user;
	}
	
	/**
	 * get homeID from config xml
	 * @return string $homeID
	 */
	public function getHomeId() {
		$homeID = $this->getConfiguration ()->getValue ( 'testing.backend.homeid' );
		if ($homeID == null) {
			$homeID = $this->defaulthomeID;
		}
		return $homeID;
	}
	
	/**
	 * open backend
	 *
	 * @return void
	 */
	public function openBackend() {
		$url = "http://backend." . trim ( $this->getConfiguration ()->getValue ( 'testing.maindomain' ), "http://www." ) . "/typo3/index.php";
		$this->getSession ()->open ( $url );
		$this->getTest ()->assertElementPresent ( "//input[@class='c-submit']", "Login button not present!" );
	}
}