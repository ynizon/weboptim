<?php

include_once 'config.php';

$tabError = [];

set_time_limit(120);
if (isset($_POST['url'])) {

    //Fix the url
    $url = $_POST['url'];
	$url_source = $_POST['url_source'];
	$url_dest = $_POST['url_dest'];
	
	//Remove last / from urls
	if (substr($url_source,-1)  == "/"){
		$url_source = substr($url_source,0,strlen($url_source)-1);
	}
	if (substr($url_dest,-1)  == "/"){
		$url_dest = substr($url_dest,0,strlen($url_dest)-1);
	}
	
    $protocol = '';
    if (substr(strtolower($url), 0, 4) != 'http') {
        $protocol = 'http://';
        $url = 'http://'.$url;
    }

    if (strpos(strtolower($url), 'http://') !== false) {
        $protocol = 'http://';
    }
    if (strpos(strtolower($url), 'https://') !== false) {
        $protocol = 'https://';
    }

    //Init
    $website = $oHelper->clean($url);
    $sDir .= '/'.$website;
    
	//Replace urls into wo_files
	$directories = array($sDir."/js", $sDir."/css", $sDir."/css/import",$sDir);
	
	foreach ($directories as $directory){
		$files = scandir($directory);
		foreach ($files as $file){
			if ($file != ".." and $file != "."){
				if (strtolower(substr($file,-2)) == "js" or strtolower(substr($file,-3)) == "css" or strtolower(substr($file,-4)) == "html"){
					$s = file_get_contents($directory."/".$file);
					$s = str_replace($url_source,$url_dest,$s);
					file_put_contents($directory."/wo__".$file,$s);
				}
			}
		}
	}
	
	
	
	//Create zip
	$zip = new ZipArchive();
	$filename = $sDir."/download.zip";

	$zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
	
	$tabFolders = array("css","js","images");
	foreach ($tabFolders as $dir){
		$rootPath = realpath($sDir."/".$dir);
		
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($rootPath),
			RecursiveIteratorIterator::LEAVES_ONLY
		);
		
		foreach ($files as $name => $file)
		{
			// Skip directories (they would be added automatically)
			if (!$file->isDir())
			{
				if (strtolower(substr($file,-2)) == "js" or strtolower(substr($file,-3)) == "css" or strtolower(substr($file,-4)) == "html"){
					if (strpos($file,"wo__") !== false){
						// Get real and relative path for current file
						$filePath = $file->getRealPath();
						$relativePath = $dir."/".substr($filePath, strlen($rootPath) + 1);

						// Add current file to archive
						$zip->addFile($filePath, str_replace("wo__","",$relativePath));
					}
				}else{
					// Get real and relative path for current file
					$filePath = $file->getRealPath();
					$relativePath = $dir."/".substr($filePath, strlen($rootPath) + 1);

					// Add current file to archive
					$zip->addFile($filePath, $relativePath);
				}
			}
		}
	}	
	$zip->addFile($sDir . "/wo__index.html","index.html");
	$zip->addFile($sDir . "/wo__index-nowebpack.html","index-nowebpack.html");
	$zip->close();

	//Remove all wo files
	$directories = array($sDir."/js", $sDir."/css", $sDir."/css/import",$sDir);
	foreach ($directories as $directory){
		$files = scandir($directory);
		foreach ($files as $file){
			if ($file != ".." and $file != "."){
				if (substr($file,0,4) == "wo__"){
					unlink($directory."/".$file);
				}
			}
		}
	}
	echo getenv("APP_URL")."/".$sDir."/download.zip";
}

?>