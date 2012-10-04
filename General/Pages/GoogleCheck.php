<?php
/**
 * 
 * Google Result test
 * @author joerg.winkler<joerg.winkler@aoemedia.de>
 *
 */

class AoeComponents_General_Pages_GoogleCheck extends Menta_Component_AbstractTest {
	
	/**
	 * Open page
	 * @return void
	 */
	public function open($pageId) {
		$this->getTest()->open($pageId);
	}
	
	/**
	 * extract relevant google search results and switch urls to build environment for testing purpose
	 *@return $urls 
	 **/
	public function getURLS($site, $searchResults, $pagesToCheck, $systemDomain){
		
		
		/* ?q= : search query */
		/* &as_qdr=all : deactivate Google Instant */
		/* &num=100 : set to 100 search results */
		$this->open('http://google.de/search?q='.$site.'&as_qdr=all&num='.$searchResults);
		sleep(2);
		
		/* @var $facade Menta_Component_Selenium1Facade */
		$facade = Menta_ComponentManager::get('Menta_Component_Selenium1Facade');
		$baseurl = preg_replace('[site:]', 'www.', $site);
		$urls = array();	
		for ($k = $pagesToCheck;$k >0; $k-- ){
			$i = 1;
			$elements = $this->getSession()->elements(WebDriver_Container::XPATH, "//div[@id='search']//h3[@class='r']//a[contains(@href,'".$baseurl."')]");
			
			if ($elements) {
				foreach ($elements as $element) {
					echo "ELEMENT : ". $element->getAttribute('href') ."\n";
					$url = preg_replace('/^(http|https):\/\/('.$baseurl.')/', $systemDomain, $element->getAttribute('href'));	
					echo "URL : ".$url . "\n";									
					$urls[] = array("url"=> $url);
					$i++;
				}
			}else {
				$this->getTest()->assertTrue(FALSE, "there are no searchresults present");
			}
			if ($k>1){
				$this->getTest()->click("//a[@id='pnnext']");
				sleep(1);
			}			
		}	
		return $urls;
	}

		
	/**
	 * opens all given urls and check if 404 or Error page is given
	 * creats a readable output for jenkins
	 *@return void 
	 **/
	public function checkFor404 ($urls, $errorPageXpath){		
		$errorUrls = array();

		foreach ($urls as $url){
			$this->open($url['url']);
			if ($this->getTest()->isElementPresent ("".$errorPageXpath."") || $this->getTest()->isElementPresent ("//body/h1[contains(text(),'Not Found')]")){
				$errorUrls[] = array("url"=> $url['url']);
			}else{
				echo "No 404 or Error page on URL : " . $url['url']. "\n";
			}			
		}

		$errorString = "";
		foreach ($errorUrls as $url){	
			$errorString .= 	"URL that shows 404 or Error Page : ". $url['url']. "\n";
		}
		if (count($errorUrls)>0){
			echo "######################################## \n";
			echo "amount of 404 pages  : " . count($errorUrls). "\n";
			echo $errorString;		
			$this->getTest()->assertTrue(count($errorUrls)==0, "some pages lead to 404 or Error pages");	
		}else{
			echo "no pages lead to 404 or Error pages";
		}
	}	
}