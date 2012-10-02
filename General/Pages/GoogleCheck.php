<?php

require_once dirname(__FILE__) . '/AbstractDriver.php';

class AoeComponents_General_Pages_GoogleCheck extends Menta_Component_AbstractTest {

	
	public function getURL($searchResults, $pagesToCheck, $systemDomain){
		$urls = array();	
		for ($k = $pagesToCheck;$k >0; $k-- ){
			$i = 1;
			$count = $this->testCase->getKnotCount("//div[@id='search']//h3[@class='r']//a/@href");
			if ($count == 0){
				$this->testCase->assertTrue(FALSE, "there are no searchresults present");
			}			
			while ($i <=$count ) {
				$rawurl = $this->testCase->getValue ("xpath=(//div[@id='search']//h3[@class='r']//a/@href)[".$i."]");
				$url = preg_replace('/^(http|https):\/\/(www.managementcircle.de)/', $systemDomain, $rawurl);					
				$urls[] = array("url"=> $url);
				$i++;
			}
			if ($k>1){
				$this->testCase->click("//a[@id='pnnext']");
			}			
			sleep(1);
		}	
//		foreach ($urls as $bla){						
//			echo "URL : " . $bla['url']. "\n";
//		}
		return $urls;
	}
	
	public function checkFor404 ($urls){
		$errorUrls = array();		
		foreach ($urls as $url){
			$this->testCase->open($url['url']);
			if ($this->testCase->isElementPresent ("//ul[@class='bcrumbs']/li/span[contains(text(),'Error 404')]")){
				$errorUrls[] = array("url"=> $url['url']);
			}else{
				echo "No 404 page on URL : " . $url['url']. "\n";
			}			
		}

		$errorString = "";
		foreach ($errorUrls as $url){	
			$errorString .= 	"URL that shows 404 Page : ". $url['url']. "\n";
		}
		if (count($errorUrls)>0){
			echo "######################################## \n";
			echo "amount of 404 pages  : " . count($errorUrls). "\n";
			echo $errorString;		
			$this->testCase->assertTrue(count($errorUrls)==0, "some pages lead to 404 pages");	
		}else{
			echo "no pages lead to 404 pages";
		}
	}	
}