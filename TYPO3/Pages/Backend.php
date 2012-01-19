<?php
/**
 * 
 * some basic backend operation functions
 * @author joerg.winkler <joerg.winkler@aoemedia.de>
 *
 */
class AoeComponents_TYPO3_Pages_Backend extends Menta_Component_AbstractTest {
	
	Protected $defaultLogin = array("username"=>"selenium.user", "password"=>"test1234", "homeID"=>"3");
	
	/**
	 * get username and password from config xml
	 * @return array $user
	 */
	public function getBackendUser() {
		$user = array ("username" => $this->getConfiguration ()->getValue ( 'testing.backend.user' ), "password" => $this->getConfiguration ()->getValue ( 'testing.backend.password' ) );
		
		if ($user ["username"] == null) {
			$user ["username"] = $this->defaultLogin["username"];
		}
		if ($user ["password"] == null) {
			$user ["password"] = $this->defaultLogin["password"];
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
			$homeID = $this->defaultLogin["homeID"];
		}
		return $homeID;
	}
	
	/**
	 * open backend
	 *
	 * @return void
	 */
	public function openBackend() {
		if ($this->getConfiguration ()->getValue ( 'testing.backend.https' ) == 1){		
			$url = "https://backend." . trim ( $this->getConfiguration ()->getValue ( 'testing.maindomain' ), "http://www." ) . "/typo3";
			$this->getSession ()->open ( $url );
		}else{		
			$url = "http://backend." . trim ( $this->getConfiguration ()->getValue ( 'testing.maindomain' ), "http://www." ) . "/typo3";
			$this->getSession ()->open ( $url );
		}
				
	}

	/**
	 * 
	 * get the backend version by meta information
	 * @return string $version
	 */
	public function getBackendVersion(){
		$metaInfo = $this->getSession()->element(WebDriver_Container::XPATH, "//meta[contains(@content,'TYPO3')]");
		$version = $metaInfo->getAttribute('content');
		$version = substr($version, strpos($version, " ")+1,(strlen( $version ) - strrpos( $version, ', http://') )* -1  );
		return $version;
	}
		
	/**
	 * 
	 * switch for different TYPO3 versions to get the content Iframe source
	 * @param string $version 
	 * @return string $iframeSrc
	 */
	public function getIframeByVersion ($version){
		switch ((int)$version){
			case ($version>=5.0):
				echo "TYPO3 Version: 5.0";
				break;
			case ($version>=4.7 && $version<5.0):
				echo "TYPO3 Version: 4.7 or greater";
				break;
			case ($version>=4.6 && $version<4.7):
				$iframeSrc = $this->getIframeFromTYPO3_46();
				break;
			case ($version>=4.5 && $version<4.6):
				$iframeSrc = $this->getIframeFromTYPO3_46();
				break;
			case ($version>=4.4 && $version<4.5):
				$iframeSrc = $this->getIframeFromTYPO3_44();
				break;
			case ($version>=4.3 && $version<4.4):
				echo "TYPO3 Version: 4.3";
				break;
			case ($version>=4.2 && $version<4.5):
				echo "TYPO3 Version: 4.2";
				break;
		}
		return $iframeSrc;	
	}	
	
	/**
	 * 
	 * log into the backend
	 * @param string $username
	 * @param string $password
	 */
	public function logIntoBackend($username, $password){
		$this->getTest()->typeAndLeave("t3-username",$username);
		$this->getTest()->typeAndLeave("t3-password",$password);		
		$formElement = $this->getSession()->element(WebDriver_Container::XPATH, "//form [@name='loginform']");
		$formElement->submit();
	}
	
	/**
	 * 
	 * logout from backend
	 */
	public function logoutFromBackend(){	
	 	$this->getTest()->waitForElementPresent("//input[contains(@value,'Logout')]");
		$submitElement = $this->getSession()->element(WebDriver_Container::XPATH, "//input[contains(@value,'Logout')]");
		$submitElement->submit();
	}	
	
	/**
	 * 
	 * simple check if login was successful
	 */	
	public function generalLoginChecks($version){		
		switch ((int)$version){
			case ($version>=4.5):
				$this->getTest()->assertElementPresent("//div[@id='typo3-logo']//img", "backend logo is missing");
				$this->getTest()->assertElementPresent("//div[@id='username']","user/login name is missing");
				$this->getTest()->assertElementPresent("//ul[@id='typo3-menu']", "left menu is missing");
				$this->getTest()->assertElementPresent("//li[@id='web']" ,"category web in left menu is missing");
				$this->getTest()->assertElementPresent("//li[@id='web']//li[1]", "page link missing!");		
				$this->getTest()->assertElementPresent("//li[@id='workspace-selector-menu']", "workspace selector is missing");
				break;
			case ($version>=4.2 && $version<4.5):	
				$this->getTest()->assertElementPresent("//div[@id='typo3-logo']//img", "backend logo is missing");
				$this->getTest()->assertElementPresent("//div[@id='username']", "user/login name is missing");
				$this->getTest()->assertElementPresent("//ul[@id='typo3-menu']", "left menu is missing");
				$this->getTest()->assertElementPresent("//li[@id='modmenu_web']/div" ,"category web in left menu is missing");
				$this->getTest()->assertElementPresent("//li[@id='modmenu_web']//li[1]/a", "page link missing!");			
				break;			
		}
	}
		
	/**
	 * 
	 * get iframe source for TYPO3 4.5 and 4.6 
	 */
	public function getIframeFromTYPO3_46(){
		//click on Web -> Page
		$this->getSession()->element(WebDriver_Container::XPATH, "//li[@id='web_txtemplavoilaM1']/a")->click();
		//get source of the iframe
		$iframe = $this->getSession()->element(WebDriver_Container::XPATH, "//div[@id='typo3-contentContainer']//iframe");
		$src = $iframe->getAttribute('src');	
		$homeID = $this->getHomeId();					
		return $src."?id=".$homeID;
	}	
	
	/**
	 * 
	 * get iframe source for TYPO3 4.4
	 */
	public function getIframeFromTYPO3_44(){	
		//click on Web -> Page
		$this->getSession()->element(WebDriver_Container::XPATH, "//li[@id='modmenu_web']//li[1]/a")->click();				
		$this->getTest()->assertElementPresent("//li[@id='modmenu_web']//li[1][@class='highlighted']/a", "page link missing!");
		//get source of the iframe
		$iframe = $this->getSession()->element(WebDriver_Container::XPATH, "//div[@id='typo3-contentContainer']//iframe");
		$src = $iframe->getAttribute('src');
		$this->getTest()->open($src);
		$iframe = $this->getSession()->element(WebDriver_Container::XPATH, "//frame[@name='list_frame']");		
		$src = $iframe->getAttribute('src');
		$homeID = $this->getHomeId();					
		return $src."?id=".$homeID;
	}

	/**
	 * 
	 * get the src for the first image 
	 * @return string $imageSrc
	 */
	public function getThumbURL(){	
		$this->getTest()->waitForElementPresent("//div[@id='typo3-docbody']");
		$image = $this->getSession()->element(WebDriver_Container::XPATH, "(//img[(contains(@src,'thumb'))])[1]");
		$imageSrc = $image->getAttribute('src');	
		return $imageSrc;				
	}

	/**
	 * 
	 * check if the image contains a fatal, error or warning message
	 * @param string $imageSrc
	 */
	public function checkThumb($imageSrc){
		$imgContent = file_get_contents($imageSrc);
		if ($imgContent != null){			
			//$img = base64_encode($imgcontent);
			//var_dump($img);
			$this->getTest()->assertTrue(stristr($imgContent,"fatal") == false, " Fatal Error present");
			$this->getTest()->assertTrue(stristr($imgContent,"error") == false, " Error present ");
			$this->getTest()->assertTrue(stristr($imgContent,"warning")== false, " Warning present");			
		}else {
			echo "kein Bild";
		}
	}
	
}