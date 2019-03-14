<?php
class Webpack{
	
	private $dir = "";
	public $error = false;
	
	public function __construct($sDir){
		$this->dir = $sDir;
	}
	
	/** Creation of the webpack.config.json file */
	public function createConfig(){		
		copy("../webpack/index.js",$this->dir."/index.js");
		copy("../webpack/package.json",$this->dir."/package.json");
		$json = json_decode(file_get_contents ($this->dir."/ressources.txt"),true);
		$sJS = "";
		$files = $json["js"];
		foreach ($files as $file){
			$sJS .= "path.resolve(__dirname, 'js/".basename($file)."'),\n";
		}

		$sCSS = "";
		$files = $json["css"];
		foreach ($files as $file){
			$sCSS .= "path.resolve(__dirname, 'css/".basename($file)."'),\n";
		}

		$fp = fopen($this->dir ."/webpack.config.js","w+");
		$s = file_get_contents("../webpack/webpack.config.js");
		$s = str_replace("//@@CSS@@",$sCSS,$s);
		$s = str_replace("//@@JS@@",$sJS,$s);
		fputs($fp, $s);
		fclose($fp);
	}
	
	/** Execute webpack */
	public function launch(){		
		$fp = fopen($this->dir."/log.txt","a+");
		fputs($fp,"----Webpack execute:  ".date("Y-m-d H:i:s")."\n");
		fclose($fp);
		
		$sDir = __DIR__."/../public/".$this->dir;
		$cmd = "webpack --context ".$sDir." --config=".$sDir."/webpack.config.js";
		//echo $cmd;
		$shell = shell_exec($cmd);
		
		//Detect error
		$this->error = true;
		if (strpos($shell,"[built]")!==false){
			$this->error = false;
		}		
		
		$fp = fopen($this->dir."/log.txt","a+");
		fputs($fp,"----Webpack execute: OK ".date("Y-m-d H:i:s")."\n");
		fclose($fp);
	}
	
	/** Replace all javasript with a call to bundle.js and 
		Replace all css with a call of bundle.css 
		to the index.html file */
	public function addBundle($dom){
		$domain = getenv("APP_URL");
		
		//Backup the older (for debug)
		if (getenv("APP_DEBUG") == "true"){
			$s = $dom->outertext;		
			file_put_contents($this->dir."/index-nowebpack.html", $s);
		}
		
		//Remove JS
		$elems = $dom->find('script');
		foreach ($elems as $elem){
			if ($elem->getAttribute("async") == "" and $elem->getAttribute("defer") == "" and ($elem->getAttribute("type") == "" or $elem->getAttribute("type") == "text/javascript")){
				if ($elem->getAttribute("src") != "" and substr($elem->getAttribute("src"),0,2)!="//"){
					if (stripos($elem->getAttribute("src"),$domain) !== false or substr($elem->getAttribute("src"),0,1)== "/"){
						$elem->src = "";
					}
				}
			}
		}
		
		//Remove CSS
		$elems = $dom->find('link');
		foreach ($elems as $elem){
			if ($elem->getAttribute("rel") == "stylesheet"){
				if ($elem->getAttribute("href") != "" and substr($elem->getAttribute("href"),0,2)!="//"){
					if (stripos($elem->getAttribute("href"),$domain) !== false or substr($elem->getAttribute("href"),0,1)== "/"){
						$elem->href = "";
					}
				}
			}			
		}
		
		$s = $dom->outertext;		
		
		if (strpos($s,"dist/bundle.css") === false){
			$s = str_ireplace("</head>","<link rel='stylesheet' href='dist/bundle.css' /></head>",$s);
		}
		if (strpos($s,"dist/bundle.js") === false){
			$s = str_ireplace("</head>","<script src='dist/bundle.js'></script></head>",$s);
			//$s = str_ireplace("</body>","<script src='dist/bundle.js'></script></body>",$s);
		}		
		
		//Remove scripts empty
		$s = str_ireplace('<script type="text/javascript"></script>','',$s);
		
		file_put_contents($this->dir."/index.html", $s);
	}
	
	/** Launch Gulp to optimize all pictures */
	public function launchGulp($tabRessources){
		$iNbTask = 0;
		$sDir = $this->dir;
		if (getenv("OPTIM_PICTURE_WITH_GULP") == "true"){
			$fp = fopen($this->dir."/log.txt","a+");
			fputs($fp,"----Gulp execute:  ".date("Y-m-d H:i:s")."\n");
			fclose($fp);
			foreach ($tabRessources as $task=>$tabFiles){
				if ($task != "js" and $task != "css"){
					foreach ($tabFiles as $sFile){
						if ($iNbTask == 0){
							$iNbTask++;
							//The output file is to temp 
							//So if an error occurs, we keep the original file

							//If gulp not work execute this : node.exe node_modules\gulp\bin\gulp.js (for windows)
							$cmd = "gulp ".$task."file --src=".$sFile." --dest=".$sDir."/temp 2>&1";
							//echo $cmd."<br/>";
							$shell = shell_exec($cmd);
							
							//Log
							$fp = fopen($sDir."/log.txt","a+");
							fputs($fp,$shell."\n");
							fclose ($fp);
							
							//If an error occur, we dont take the new file
							$bError = false;
							if (strpos($shell,"Only YUV color space input jpeg is supported")!==false){
								$bError = true;
							}
							
							//If we have a new file, we write over the original file
							if ($bError == false and file_exists($sDir."/temp/".basename($sFile))){
								rename($sDir."/temp/".basename($sFile),$sFile);
							}
							
							//We remove the resource from the array
							array_shift ($tabRessources[$task]);
							file_put_contents($sDir."/ressources.txt",json_encode($tabRessources));
						}
					}
				}
			}
			@rmdir($sDir."/temp");
			
			$fp = fopen($this->dir."/log.txt","a+");
			fputs($fp,"----Gulp execute: OK ".date("Y-m-d H:i:s")."\n");
			fclose($fp);
		}
		return $iNbTask;
	}
}
?>