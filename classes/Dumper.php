<?php

/**
 * Class Dumper
 * Some tools for dump resources : css, js...
 */
class Dumper
{
    private $directory = '';
    private $dom;

    /**
     * Dumper constructor.
     *
     * @param $sDir (directory of the project)
     * @param $dom (MyHtmlDomParser of the website)
     */
    public function __construct($sDir, $dom)
    {
        $this->directory = $sDir;
        $this->dom = $dom;
    }

    public function dumpImages($sFormat, $protocol, $domain, &$sContent)
    {
        $oHelper = new Helper();
        $fp = fopen($this->directory.'/log.txt', 'a+');
        $dom = $this->dom;
        $tabImages = [];
        $tabReplace = [];
        //On recopie toutes les images du site dans images (sans sous repertoire pour l optimisation)
        $elems = $dom->find('img');
        foreach ($elems as $elem) {
            for ($z = 1; $z <= 3; $z++) {
                $attribute = 'src';
                if ($z == 2) {
                    $attribute = 'data-src';
                }

                if ($z == 3) {
                    $attribute = 'data-lazy-src';
                }

                if ($elem->getAttribute($attribute) != '') {
                    if (stripos($elem->getAttribute($attribute), $domain) !== false or substr($elem->getAttribute($attribute), 0, 1) == '/') {
                        $res_url = $elem->getAttribute($attribute);
                        fwrite($fp, $res_url."\n");
                        if (substr($res_url, 0, 2) != '//' and $res_url != '' and strpos($res_url, 'data:image') === false) {
                            $file = urldecode(basename($res_url));

                            if (strpos(strtolower($file), '.php') === false) {
                                //Au cas ou le nom possede une variable
                                $pos = strpos($file, '?');
                                if ($pos !== false) {
                                    $file = substr($file, 0, $pos);
                                }
                                $pos = strpos($file, '#');
                                if ($pos !== false) {
                                    $file = substr($file, 0, $pos);
                                }

                                if ($sFormat == '' or strpos($file, '.'.$sFormat) !== false) {
                                    //Remplacement de lurl de limage
                                    $pos = strpos($res_url, '?');
                                    if ($pos !== false) {
                                        $res_url = substr($res_url, 0, $pos);
                                    }
                                    $pos = strpos($res_url, '#');
                                    if ($pos !== false) {
                                        $res_url = substr($res_url, 0, $pos);
                                    }

                                    //On ne remplace lurl de la ressource qu une fois
                                    if (!in_array($res_url, $tabReplace)) {
                                        $sContent = str_replace($res_url, getenv('APP_URL').'/'.$this->directory.'/images/'.$file, $sContent);
                                        $tabReplace[] = $res_url;
                                    }

                                    if (substr($res_url, 0, 1) == '/') {
                                        $res_url = $protocol.$domain.$res_url;
                                    }

                                    $tabImages[] = $this->directory.'/images/'.$file;

                                    if (!file_exists($this->directory.'/images/'.$file)) {
                                        file_put_contents($this->directory.'/images/'.$file, $oHelper->getContent($res_url));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        fwrite($fp, '----Getting pictures '.$sFormat.': OK '.date('Y-m-d H:i:s')."\n");

        fclose($fp);

        return $tabImages;
    }

    public function dumpCss($protocol, $domain, &$sContent, $url)
    {
        //On recopie tous les css du site dans css (sans sous repertoire pour l optimisation)
        $oHelper = new Helper();
        $fp = fopen($this->directory.'/log.txt', 'a+');
        $dom = $this->dom;
        $tabCss = [];
        $tabReplace = [];
        $elems = $dom->find('link');
        foreach ($elems as $elem) {
            if ($elem->getAttribute('rel') == 'stylesheet' and $elem->getAttribute("media") != "print") {
                if (stripos($elem->getAttribute('href'), $domain) !== false or substr($elem->getAttribute('href'), 0, 1) == '/') {                    
					$res_url = $elem->getAttribute('href');
                    fwrite($fp, $res_url."\n");

                    if (substr($res_url, 0, 2) != '//' and $res_url != '') {
                        $file = $oHelper->getDirToString($res_url);

                        if (strpos(strtolower($file), '.php') === false) {
                            //Au cas ou le nom possede une variable
                            $pos = strpos($file, '?');
                            if ($pos !== false) {
                                $file = substr($file, 0, $pos);
                            }
                            $pos = strpos($file, '#');
                            if ($pos !== false) {
                                $file = substr($file, 0, $pos);
                            }

                            if (!file_exists($this->directory.'/css/'.$file)) {

                                //Remplacement du lien
                                $pos = strpos($res_url, '?');
                                if ($pos !== false) {
                                    $res_url = substr($res_url, 0, $pos);
                                }
                                $pos = strpos($res_url, '#');
                                if ($pos !== false) {
                                    $res_url = substr($res_url, 0, $pos);
                                }

                                //On ne remplace lurl de la ressource qu une fois
                                if (!in_array($res_url, $tabReplace)) {
                                    $sContent = str_replace($res_url, getenv('APP_URL').'/'.$this->directory.'/css/'.$file, $sContent);
                                    $tabReplace[] = $res_url;
                                    //echo "<br/>".$res_url."->".getenv("APP_URL")."/".$sDir."/css/".$file;
                                }

                                if (substr($res_url, 0, 1) == '/') {
                                    $res_url = $protocol.$domain.$res_url;
                                }

                                //Si il y a des urls à importer, alors il faut les ramener, et refaire le lien
                                $sContentCss = $oHelper->getContent($res_url);
                                //echo "<hr/>".$res_url;
                                //$tabRessourcesCSS = array();

                                //Recupere les ressources inscrits dans le css
                                $this->getRessourcesUrl($sContentCss, $res_url, $url, $this->directory, $tabRessources);

                                $tabCss[] = $this->directory.'/css/'.$file;

                                file_put_contents($this->directory.'/css/'.$file, $sContentCss);
                            }
                        }
                    }
                }
            }
        }
        fwrite($fp, '----Getting css : OK '.date('Y-m-d H:i:s')."\n");

        fclose($fp);

        return $tabCss;
    }

    public function dumpJs($protocol, $domain, &$sContent)
    {
        //On recopie tous les js du site dans js (sans sous repertoire pour l optimisation)
        $oHelper = new Helper();
        $fp = fopen($this->directory.'/log.txt', 'a+');
        $dom = $this->dom;
        $tabJs = [];
        $tabReplace = [];
        $elems = $dom->find('script');
        foreach ($elems as $elem) {
            if ($elem->getAttribute('src') != '') {
                if (stripos($elem->getAttribute('src'), $domain) !== false or substr($elem->getAttribute('src'), 0, 1) == '/') {
                    $res_url = $elem->getAttribute('src');
                    fwrite($fp, $res_url."\n");
                    if (substr($res_url, 0, 2) != '//' and $res_url != '') {
                        $file = $oHelper->getDirToString($res_url);

                        if (strpos(strtolower($file), '.php') === false) {
                            //Au cas ou le nom possede une variable
                            $pos = strpos($file, '?');
                            if ($pos !== false) {
                                $file = substr($file, 0, $pos);
                                $file = uniqid().'-'.$file;
                            }
                            $pos = strpos($file, '#');
                            if ($pos !== false) {
                                $file = substr($file, 0, $pos);
                            }

                            //Remplacement de lurl du js
                            $true_url = $res_url;
                            $pos = strpos($res_url, '?');
                            if ($pos !== false) {
                                $res_url = substr($res_url, 0, $pos);
                            }
                            $pos = strpos($res_url, '#');
                            if ($pos !== false) {
                                $res_url = substr($res_url, 0, $pos);
                            }

                            //On ne remplace lurl de la ressource qu une fois
                            if (!in_array($res_url, $tabReplace)) {
                                $sContent = str_replace($res_url, getenv('APP_URL').'/'.$this->directory.'/js/'.$file, $sContent);
                                $tabReplace[] = $res_url;
                            }

                            //echo $res_url."->".getenv("APP_URL")."/".$sDir."/js/".$file."\n";
                            if (substr($true_url, 0, 1) == '/') {
                                $true_url = $protocol.$domain.$true_url;
                            }

                            //No serviceworker js file
                            $sContentJs = $oHelper->getContent($true_url);
                            if (stripos($sContentJs, 'serviceworker') === false) {
                                $tabJs[] = $this->directory.'/js/'.$file;
                                if (!file_exists($this->directory.'/js/'.$file)) {
                                    file_put_contents($this->directory.'/js/'.$file, $sContentJs);
                                }
                            }
                        }
                    }
                }
            }
        }
        fwrite($fp, '----Getting js : OK '.date('Y-m-d H:i:s')."\n");

        fclose($fp);

        return $tabJs;
    }

    /** Move all script content into another js file (__allscript__code.js) and link it at the end of the body
     * @TODO
     */
    public function moveScriptToExternalFile()
    {
        $fp = fopen($this->directory.'/log.txt', 'a+');
        fwrite($fp, '----Déplacement du code css/js '.date('Y-m-d H:i:s')."\n");
        fclose($fp);

        $dom = $this->dom;

        //NOT WORK 100%, it depends of the content of JS
        $sAllScripts = '';

        /*
        $elems = $dom->find('script');
        foreach ($elems as $elem){
            if ($elem->getAttribute("async") == "" and $elem->getAttribute("defer") == "" and ($elem->getAttribute("type") == "text/javascript" or $elem->getAttribute("type") == "")){
                if ($elem->getAttribute("src") == ""){
                    if (stripos($elem->plaintext,"CDATA") === false){
                        $sAllScripts .= $elem->plaintext;
                        $elem->outertext = "";
                    }
                }
            }
        }
        */
        file_put_contents($this->directory.'/js/__alljs__code.js', $sAllScripts);

        //NOT WORK 100%, it depends of the order of css files
        $sAllCss = '';
        /*
        $elems = $dom->find('style');
        foreach ($elems as $elem){
            $sAllCss .= $elem->plaintext;
            $elem->outertext = "";
        }

        */
		
		//Copy all import at the beginning of the new file
		$sAllImportCss = "";
		$files = scandir($this->directory."/css");
		foreach ($files as $file){
			if ($file != "." and $file != ".." and $file != "__allcss__import.css" and $file != "__allcss__code.css" and $file != "import"){
				$fpcss = fopen($this->directory."/css/".$file,"r");
				while (!feof($fpcss)){
					$buffer = str_replace("\r","",str_replace("\n","",fgets($fpcss)));

					if (stripos($buffer,"@import") !== false){
						$sAllImportCss.= $buffer."\r\n";
					}
				}
				fclose($fpcss);
			}
		}
		
		file_put_contents($this->directory.'/css/__allcss__import.css', $sAllImportCss);		
        file_put_contents($this->directory.'/css/__allcss__code.css', $sAllCss);
        file_put_contents($this->directory.'/index.html', $dom->outertext);

        $fp = fopen($this->directory.'/log.txt', 'a+');
        fwrite($fp, '----Déplacement du code css/js: OK '.date('Y-m-d H:i:s')."\n");
        fclose($fp);
    }

    public function getRessourcesUrl(&$sContentCss, $res_url, $url, $sDir, &$tabRessources)
    {
        //On remplace certains caracteres encodés
        $oHelper = new Helper();
        $sContentCss = str_replace('&#39;', "'", $sContentCss);
        $sContentCss = str_replace('&#34;', '"', $sContentCss);

        $kMax = 0;

        $iPosUrl = stripos($sContentCss, 'url(', 0);
        while ($iPosUrl !== false and $kMax < 1000) {
            $iPosUrl = $iPosUrl + 4;
            $kMax++;

            $char = substr($sContentCss, $iPosUrl, 1);
            $iAjust = 0;

            $sBackgroundUrl = 'url(';
            //Warning: chars encoding are differents
            if ($char == "'" or $char == '"' or $char == "'") {
                $iPosUrl = $iPosUrl + 1;
                $iAjust = 1;
                $sBackgroundUrl = 'url('.$char;
            }
            $pos = strpos($sContentCss, ')', $iPosUrl);

            $dest = trim(substr($sContentCss, $iPosUrl, $pos - $iPosUrl - $iAjust));
			
			if ($dest != ""){
				$oHelper = new Helper();
				$sOrigineImportUrl = $dest;
				if ($res_url != ""){
					$sImportUrl = $oHelper->getLinkFrom($res_url,$dest);
				}else{
					$sImportUrl = $oHelper->getLinkFrom($url,$dest);
				}
				
				if (substr($sImportUrl, 0, 2) != '//' and $sImportUrl != '' and stripos($sImportUrl, 'data:image') === false) {
					$fileCss = urldecode(basename($sImportUrl));

					if (strpos(strtolower($fileCss), '.php') === false) {
						//Au cas ou le nom possede une variable
						$pos = strpos($fileCss, '?');
						if ($pos !== false) {
							$fileCss = substr($fileCss, 0, $pos);
						}
						$pos = strpos($fileCss, '#');
						if ($pos !== false) {
							$fileCss = substr($fileCss, 0, $pos);
						}
						$pos = strpos($fileCss, '&');
						if ($pos !== false) {
							$fileCss = substr($fileCss, 0, $pos);
						}

						//echo $sOrigineImportUrl."---->".getenv("APP_URL")."/".$sDir."/css/import/".$fileCss;
						$sContentCssImp = $oHelper->getContent($sImportUrl);
						
						//echo $sBackgroundUrl.$sOrigineImportUrl;exit();
						$sContentCss = str_replace($sBackgroundUrl.$sOrigineImportUrl, $sBackgroundUrl.getenv('APP_URL').'/'.$sDir.'/css/import/'.$fileCss, $sContentCss);
						//echo $sContentCss;exit();
						//echo "x".$sOrigineImportUrl."x<br/>\n";

						if (!file_exists($sDir.'/css/import/'.$fileCss)) {						
							$bOk = false;
							$sExt = strtolower(substr($fileCss, -3));
							switch ($sExt) {
								case 'jpg':
									$tabRessources['imagesjpg'][] = $sDir.'/css/import/'.$fileCss;
									$bOk = true;
									break;
								case 'gif':
								case 'png':
									$tabRessources['images'][] = $sDir.'/css/import/'.$fileCss;
									$bOk = true;
									break;
								case 'css':
								case 'eot':
								case 'ttf':
								case 'woff':
								case 'woff2':
								case 'svg':
								case 'cur':
									$bOk = true;
									break;
							}
							
							//No download php code or something like (security)
							if ($bOk){
								if ($sContentCssImp == ""){
									$fpx = fopen("failed.log","a+");
									fputs($fpx,"From ".$url ." and ".$dest."\r\n");
									fputs($fpx,"To" .$sImportUrl."\r\n");
									fputs($fpx,"--------------\r\n");
									fclose($fpx);
								}else{
									file_put_contents($sDir.'/css/import/'.$fileCss, $sContentCssImp);	
								}
								//echo $sContentCssImp.$sDir."/css/import/".$fileCss."<br/>";
								
								
							}
							
							
						}
					}
				}
			}
			$iPosUrl = strpos($sContentCss, 'url(', $iPosUrl + 1);
        }
    }
}
