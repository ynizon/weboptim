<?php
include_once 'config.php';


$tabRessources  =array();
$url = "https://www.search-foresight.com/agence-seo/";
$res_url = "https://www.search-foresight.com/wp-content/themes/shadow-child/style.css?ver=4.9.1";
$sDir .= "/httpswwwsearch-foresightcomagence-seo";
$sContent = $oHelper->getContent($url);
$oDumper = new Dumper($sDir, "");
//$oDumper->getRessourcesUrl($sContent, "", $url, $sDir, $tabRessources);

$s = $sContent;
$iPos = strpos($s,"<head",0);
if ($iPos !== false){
	$iPos = strpos($s,">",$iPos+1);
	$s = substr($sContent,0,$iPos+1);
	$s .= "<link rel='stylesheet' href='dist/bundle.css' />";
	$s .= substr($sContent,$iPos+1);
}
echo $s;

/*
//TEST: TO REMOVE
$url = "https://fleurdesail.gameandme.fr";
$oHelper = new Helper();
$res_url = "https://www.atlasformen.fr/style/homepage.group.css?v=QvO3TVxgv5RJs1GUPhnBVzMq7mvG-WmxzFDF2FtmxCc1";
$res_url = "https://fleurdesail.gameandme.fr/wp-content/themes/beach/style.css?ver=4.8.9";
$sDir .= "httpsfleurdesailgameandmefr";
$oDumper = new Dumper($sDir, "");

$sContentCss = $oHelper->getContent($res_url);
//echo "<hr/>".$res_url;
//$tabRessourcesCSS = array();

//Recupere les ressources inscrits dans le css
$x =  array();
$oDumper->getRessourcesUrl($sContentCss, $res_url,$url, $sDir,$x);
exit();
*/


?>