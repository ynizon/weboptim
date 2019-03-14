<?php
include_once("config.php");

$tabError = array();

set_time_limit(120);
if (isset($_POST["url"])){
	
	//Fix the url 
	$url = $_POST["url"];
	$protocol = "";
	if (substr(strtolower($url),0,4) != "http"){
		$protocol = "http://";
		$url = "http://".$url;
	}
	
	if (strpos(strtolower($url),"http://") !== false){
		$protocol = "http://";
	}
	if (strpos(strtolower($url),"https://") !== false){
		$protocol = "https://";
	}
	
	//Init
	$website = $oHelper->clean($url);
	$sDir .= "/".$website;
	$oWebpack = new Webpack($sDir);	
	$domain = $oHelper->getdomain($url);
	$tabData = array("ressources"=>array(),"scores"=>array());
	
	//Already calculated ?
	if (file_exists($sDir."/ps.txt")){
		$json = file_get_contents($sDir."/ps.txt");
		$tabInfo = json_decode($json,true);		
		$tabData["scores"] = $tabInfo;
		echo json_encode($tabData);
	}else{
		if (!file_exists($sDir."/ressources.txt")){
			//Creation of the Folder for the website
			if (!is_dir($sDir)){
				mkdir($sDir);
			}
			
			if (!is_dir($sDir."/temp")){
				mkdir($sDir."/temp");
			}
			
			if (!is_dir($sDir."/images")){
				mkdir($sDir."/images");
			}
			
			if (!is_dir($sDir."/css")){
				mkdir($sDir."/css");
			}
			
			if (!is_dir($sDir."/css/import")){
				mkdir($sDir."/css/import");
			}
			
			if (!is_dir($sDir."/js")){
				mkdir($sDir."/js");
			}			
			
			file_put_contents($sDir."/date.txt",date("Y-m-d"));
			
			$sContent = $oHelper->getContent($url);
			
			//Replace absolute links to relative 
			$sContent = str_replace("https://".$domain."/","/",$sContent);
			$sContent = str_replace("http://".$domain."/","/",$sContent);
			$sContent = str_replace("\"//".$domain."/","\"/",$sContent);
			$sContent = str_replace("'//".$domain."/","'/",$sContent);
			
			
			//List all ressources into an array
			$fp = fopen($sDir."/log.txt","w+");
			fclose($fp);
			$tabRessources = array("js"=>array(),"css"=>array(),"images"=>array(),"imagesjpg"=>array(), "html"=>array());
			$dom = MyHtmlDomParser::str_get_html($sContent);
			$oDumper = new Dumper($sDir, $dom);
			if ($dom){				
				$tabRessources["js"] = $oDumper->dumpJs($protocol, $domain, $sContent);
				$tabRessources["images"] = $oDumper->dumpImages("",$protocol,$domain, $sContent);
				$tabRessources["imagesjpg"] = $oDumper->dumpImages("jpg",$protocol,$domain, $sContent);				
				$tabRessources["css"] = $oDumper->dumpCss($protocol, $domain, $sContent, $url);
			}		
			
			$tabRessources["html"][] = $sDir."/index.html";
			
			//Get all resources from HTML, CSS File
			$oDumper->getRessourcesUrl($sContent, "",$url, $sDir, $tabRessources);
			$fp = fopen($sDir."/log.txt","a+");
			fputs($fp,"----Getting HTML : OK ".date("Y-m-d H:i:s")."\n");
			fclose($fp);
			file_put_contents($sDir."/index.html",$sContent);
			file_put_contents($sDir."/ressources.txt",json_encode($tabRessources));
			
			$size = $oHelper->format_calc_size($oHelper->calc_size($sDir));
			file_put_contents($sDir."/size.txt",$size);
		}else{
			$tabRessources = json_decode(file_get_contents($sDir."/ressources.txt"),true);
			if (!file_exists($sDir."/webpack.config.js")){
				//Webpack optimization (dump all css/js content to a new file)			
				$dom = MyHtmlDomParser::file_get_html($sDir."/index.html");
				$oDumper = new Dumper($sDir, $dom);
				$oDumper->moveScriptToExternalFile();
			
				//Reload DOM and add Webpack bundle (CSS/JS)
				$oWebpack->createConfig();
				$oWebpack->launch();
				$dom = MyHtmlDomParser::file_get_html($sDir."/index.html");
				
				if ($oWebpack->error == false){
					$oWebpack->addBundle($dom);			
				}
				file_put_contents($sDir."/ressources.txt",json_encode($tabRessources));
			}			
			
			//Gulp : Pictures Optimization (one after one)			
			$iNbTask = $oWebpack->launchGulp($tabRessources);
			
			if ($iNbTask == 0){
				//Pagespeed call
				$tabInfo = $oHelper->getAllPagespeed($url, $sDir);
				$tabInfo["size"] = file_get_contents($sDir."/size.txt");
				$newsize = $oHelper->calc_size($sDir)-$oHelper->calc_size($sDir."/js")-$oHelper->calc_size($sDir."/css")+$oHelper->calc_size($sDir."/css/import");
				
				//Remove some files from the new folder size
				$files = array("index-nowebpack.html","log.txt","webpack.config.js");
				foreach ($files as $file){
					if (file_exists($sDir."/".$file)){
						$newsize = $newsize - filesize($sDir."/".$file);
					}
				}
				$tabInfo["size_optim"] = $oHelper->format_calc_size($newsize);
				$tabData["scores"] = $tabInfo;
				
				file_put_contents($sDir."/ps.txt",json_encode($tabInfo));
			}
			
			//Log for ajax call
			foreach ($tabRessources as $task=>$tabFiles){
				foreach ($tabFiles as $sFile){
					$tabData["ressources"][] = basename($sFile);
				}
			}
			echo json_encode($tabData);
		}
	}	
}
?>