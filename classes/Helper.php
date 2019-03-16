<?php

/**
 * Class Helper
 * Tools for different things
 */
class Helper
{
	/**
	@function calc_size 
	@param text dir rép dont on veut connaitre la taille

	@return string

	formate la taille retourné par la fonction calc_size_Rdir
	*/
	public function format_calc_size($size)
	{
	  $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
	  return round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
	}
	
	
	/**
	@function calc_size 
	@param text dir rép dont on veut connaitre la taille

	@return numeric
	*/
	public function calc_size($dir)
	{
	  $size = $this->calc_size_Rdir($dir);	  
	  return $size;
	}
	
	/**
	@function calc_size_Rdir
	@param text dir_start dossier dont on va calculer la taille

	@return numeric

	calcul de la taille d'un dossier en ajouter celle de ses fichiers
	*/
	public function calc_size_Rdir($dir_start)
	{
	  $size = 0;
	  $open = opendir($dir_start);
	  while($file = readdir($open))
	  {
		if($file != '.' && $file != '..')
		{
		  if(is_dir($dir_start .'/'.$file))
		  {
			$new_dir = $dir_start .'/'.$file;
			$size = $size + $this->calc_size_Rdir($new_dir);
		  }
		  else
		  {
			$size = $size + filesize($dir_start .'/'.$file);
		  }
		}
	  }
	  return $size;
	}

	/** Remove a directory */
	public function deleteDir($dirPath) {
		if (! is_dir($dirPath)) {
			throw new InvalidArgumentException("$dirPath must be a directory");
		}
		if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
			$dirPath .= '/';
		}
		$files = glob($dirPath . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				$this->deleteDir($file);
			} else {
				unlink($file);
			}
		}
		rmdir($dirPath);
	}
	
	/** Replace / and \ char to - to create a directory from a url */
	public function getDirToString($res_url){
		$s = dirname($res_url);
		$s = str_replace("/","-",$s);
		$s = str_replace("\\","-",$s);
		$s .= "-".urldecode(basename($res_url));
		return $s;
	}

	/** Clean some characters */
	public function clean($string) {
	   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
	   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	   $string = str_replace('..', '-', $string); // Replaces all spaces with hyphens.
	   
	   return trim(preg_replace('/-+/', '-', $string)); // Replaces multiple hyphens with single one.
	}

	/** Get domain from a url */
	function getdomain($url) {
		
		if (strpos($url,"http") !== false){
			preg_match (
				"/^(http:\/\/|https:\/\/)?([^\/]+)/i",
				$url, $matches
			);
			$host = $matches[2]; 
			preg_match (
				"/[^\/]+\.[^\.\/]+$/", 
				$host, $matches
			);
			
			if (isset($matches[0])){
				return strtolower("{$matches[0]}");
			}else{
				return "";
			}
		}else{
			return "";
		}
	} 

	/** Get content from a url (empty if error) */
	public function getContent($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; fr; rv:1.9.3.13) Gecko/20101203 Firefox/3.6.13');
		curl_setopt($ch, CURLOPT_COOKIESESSION, true );
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');   
		$sContent = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($httpcode >= 400){
			$sContent = "";
		}
		curl_close($ch);
		
		return $sContent;
	}	
	
	/** Get the pagespeed scores */
	public function getAllPagespeed($url, $sDir){
		$tabInfo = array();
		
		$mobile = "";
		$desktop = "";
		$mobileOptim = "";
		$desktopOptim = "";
		
		if (getenv("PAGESPEED_API") != ""){
			$mobile = $this->getPageSpeed($url, $sDir, "mobile");
			$desktop = $this->getPageSpeed($url, $sDir, "desktop");
			$mobileOptim = $this->getPageSpeed(urlencode(getenv("APP_URL")."/".$sDir), $sDir, "mobile");
			$desktopOptim = $this->getPageSpeed(urlencode(getenv("APP_URL")."/".$sDir), $sDir, "desktop");
		}
		
		
		$tabInfo["url_serveur"] = $sDir;
		$tabInfo["url"] = $url;
		$tabInfo["mobile"] = $mobile;
		$tabInfo["mobile_optim"] = $mobileOptim;
		$tabInfo["desktop"] = $desktop;
		$tabInfo["desktop_optim"] = $desktopOptim;
		
		return $tabInfo;
	}
	
	/** Get the pagespeed scores */
	public function getPagespeed($url, $sDir, $sDevice){
		$tabInfo = array();
		
		$r = "";

		//Scores of the actual website
		$ctx = stream_context_create(array( 
			'http' => array( 
				'timeout' => 120 
				) 
			) 
		); 

		$fp = fopen($sDir."/log.txt","a+");
		fputs($fp,"----Pagespeed ".$sDevice." - Website ".$url." ".date("Y-m-d H:i:s")."\n");				
		fclose($fp);
		
		$json = $this->getContent("https://www.googleapis.com/pagespeedonline/v".getenv("PAGESPEED_VERSION")."/runPagespeed?url=".urlencode($url)."&filter_third_party_resources=true&locale=fr&strategy=".$sDevice."&key=".getenv("PAGESPEED_API"), 0, $ctx);
		if ($json != ""){
			$s = json_decode($json,true);
			if (isset($json["error"]["errors"])){
				$r = "Pb Google: ".$json["error"]["errors"]["message"];
			}else{
				if (isset($s["lighthouseResult"]["audits"]["render-blocking-resources"]["score"])){
					$r = $s["lighthouseResult"]["audits"]["render-blocking-resources"]["score"]*100;	
				}
			}
		}
		return $r;		
	}
}

