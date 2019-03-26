<?php
include_once 'config.php';

//Init folder fot the website
if (!is_dir($sDir)) {
    mkdir($sDir);
}

//If we need to remove folder; we change the date
$folders = scandir($sDir);
if (isset($_GET['remove'])) {
    $sDirRemove = trim($_GET['remove']);
    foreach ($folders as $folder) {
        if ($folder != '.' and $folder != '..') {
            if ($sDir.'/'.$folder == $sDirRemove) {
                file_put_contents($sDir.'/'.$folder.'/date.txt', '2001-01-01');
            }
        }
    }
}

//Remove older folders
$oHelper = new Helper();
foreach ($folders as $folder) {
    if ($folder != '.' and $folder != '..') {
        if (is_dir($sDir.'/'.$folder)) {
            $sDate = '2001-01-01';
            if (file_exists($sDir.'/'.$folder.'/date.txt')) {
                $sDate = file_get_contents($sDir.'/'.$folder.'/date.txt');
            }
            if ($sDate < date('Y-m')) {
                $oHelper->deleteDir($sDir.'/'.$folder);
            }
        }
    }
}

//Redirect after removing
if (isset($_GET['remove'])) {
    header('location: index.php?url='.$_GET['url']);
    exit();
}


//Get url from parameter (for download)
$url_source = "";
$url_dest = "";
if (isset($_GET['url'])){
	$url_dest = $_GET['url']; 	
	$website = $oHelper->clean($url_dest);
    $url_source = getenv("APP_URL")."/".$sDir.'/'.$website;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title><?php echo getenv('APP_NAME'); ?></title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom fonts for this template -->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="vendor/simple-line-icons/css/simple-line-icons.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

  <!-- Custom styles for this template -->
  <link href="css/landing-page.css" rel="stylesheet">

</head>

<body>
<!-- Masthead -->
  <header class="masthead text-white text-center">	
    <div class="overlay"></div>
	<form method="GET">
    <div class="container">
      <div class="row">
        <div class="col-xl-9 mx-auto">
          <h1 class="mb-5"><?php echo getenv('APP_NAME'); ?></h1>
        </div>		
		<div class="col-md-10 col-lg-8 col-xl-7 mx-auto">          
			<div class="form-row">
				  <div class="col-12 col-md-9 mb-2 mb-md-0">                
						<input style="width:90%;display:inline;padding:1.4rem;" required class="form-control" type="text" id="url" name="url" value="<?php echo $url_dest;?>" placeholder="https://www.votre-site.com" />
				  </div>
				  <div class="col-12 col-md-3">
					<script>
						document.getElementById('url').focus();
					</script>
					<button type="submit" class="btn btn-block btn-lg btn-success">Optimiser</button>
				  </div>
				
			</div>
		</div>
      </div>
    </div>
	</form>
  </header>

  <!-- Icons Grid -->
  <section class="features-icons bg-light text-center">
    <div class="container">
      <div class="row">
        <div class="col-lg-4">
          <div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-3">
            <div class="features-icons-icon d-flex">
              <i class="icon-screen-desktop m-auto text-primary"></i>
            </div>
            <h3>Web performance</h3><br/>
            <div class="lead mb-0" style="text-align:left">
				Ce site a pour but de vous montrer les meilleures méthodes d'optimisation.
				<br/>Pour cela, il utilise les technologies:<br/>
				<ul style="list-style:none">
					<li><a href='https://webpack.js.org/' target="_blank">- Webpack</a></li>
					<li><a href='https://gulpjs.com/' target="_blank" >- Gulp JS</a> et les plugins:</li>
					<li><a href='https://www.npmjs.com/package/imagemin-guetzli' target="_blank">imagemin-guetzli</a></li>
					<li><a href='https://www.npmjs.com/package/gulp-imagemin' target="_blank">gulp-imagemin</a></li>
					<li><a href='https://www.npmjs.com/package/imagemin-guetzli' target="_blank">imagemin-guetzli</a></li>
					<?php
                    /*
                        <li><a href='https://www.npmjs.com/package/gulp-imagemin' target="_blank">gulp-imagemin</a></li>
                        <li><a href='https://www.npmjs.com/package/gulp-clean-css' target="_blank">gulp-clean-css</a></li>
                        <li><a href='https://www.npmjs.com/package/gulp-htmlmin' target="_blank">gulp-htmlmin</a></li>
                        <li><a href='https://www.npmjs.com/package/gulp-uglify' target="_blank">gulp-uglify</a></li>
                    */
                    ?>
				</ul>
			</div>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-3">
            <div class="features-icons-icon d-flex">
              <i class="icon-layers m-auto text-primary"></i>
            </div>
            <h3>Optimisation</h3><br/>
            <div>
				<div class="lead mb-0">
					<div id="info" class="alert alert-danger" style="display:none;width:100%;">
						Merci de patientez, ce service n'est malheureusement pas très rapide.<br/>
						Comptez 10 minutes par page...<br/>
						A noter que les sites en lazy loading ne fonctionnent pas toujours.&nbsp;&nbsp;
						<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>
					</div>
					
					<ul id="step" class="list-group" style="padding-bottom:20px">
						
					</ul>					
				</div>
				
				<div id="result" style="display:none;width:100%;">
					<div class="lead mb-0">
						Voici vos notes obtenues depuis <a href='https://developers.google.com/speed/pagespeed/insights/?hl=fr' target="_blank">Google Page Speed</a>:<br/>
						<table class="table table-striped">
							<tr>
								<td>Device</td>
								<td>Site actuel</td>
								<td>Après optimisation</td>
							</tr>
							<tr>
								<td>Mobile</td>
								<td id="note_mobile"></td>
								<td id="note_mobile_optim"></td>
							</tr>
							<tr>
								<td>Desktop</td>
								<td id="note_desktop"></td>
								<td id="note_desktop_optim"></td>
							</tr>
							<tr>
								<td>Taille</td>
								<td id="size"></td>
								<td id="size_optim"></td>
							</tr>
						</table>	
						<br/>
						Ces résultats sont des pistes de réflexion. Il se peut que le code source ai mal été intérprété par l'outil, et que le site ne s'affiche pas à 100% comme il devrait.
						<br/><a href='#' id="url_serveur" target="_blank">Voir le code optimisé</a> / <a href='#' id="url_serveur-nowebpack" target="_blank">(sans webpack).</a><br/>
						<a id="remove_url_serveur" href='#'>Cliquez ici pour forcer un recalcul.</a><br/><br/>
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#dlModal">
						  Télécharger l'optimisation pour votre site
						</button>

					</div>
				</div>
			</div>
          </div>
        </div>        
      </div>
    </div>
  </section>

	<div class="modal fade" id="dlModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Téléchargement des optimisations</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<label for="url_source">Url Source</label>
			<input type="hidden" name="url_origin" id="url_origin" value="<?php echo $url_dest;?>"/>
			<input type="text" class="form-control" name="url_source" id="url_source" value="<?php echo $url_source;?>"/>
			<label for="url_source">Url finale (ou seront stockés les fichiers)</label>
			<input type="text" class="form-control" name="url_dest" id="url_dest" value="<?php echo $url_dest;?>" />
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
			<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="download()">Télécharger</button>
		  </div>
		</div>
	  </div>
	</div>
  
  
  <!-- Footer -->
  <footer class="footer bg-light">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 h-100 text-center text-lg-left my-auto">          
          <p class="lead mb-0">&copy; <a href='https://www.gameandme.fr'>Développeur Web - Expert PHP Nantes</a>
			| <a href='https://www.gameandme.fr/contacts'>Contact</a>
			| <a href='https://www.github.com/ynizon/weboptim'>Github</a>
		  </p>
        </div>        
      </div>
    </div>
  </footer>

  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <script >
		function download(){
			$.ajax({
				type: "POST",
				timeout: 600000,
				url: '<?php echo getenv('APP_URL'); ?>/download.php',
				data: 'url='+$("#url_origin").val()+"&url_source="+$("#url_source").val()+"&url_dest="+$("#url_dest").val(),
				success: function (data) {
					if (data != ""){
						window.location.href=(data);	
					}					
				}
			});
		}
		
		function initUrl(){
			if ($('#info').css('display') == 'none'){	
				$("#result").hide();
				$("#size").html("");
				$("#size_optim").html("");
				$("#note_mobile").html("");
				$("#note_mobile_optim").html("");
				$("#note_desktop").html("");
				$("#note_desktop_optim").html("");
				$("#url_serveur").attr("href","#");
				$("#url_serveur-nowebpack").attr("href","#");
				$("#remove_url_serveur").attr("href","#");
				
				$("#step").html('');
				if ($("#url").val() == ""){
					alert('Url obligatoire');
				}else{
					$('#info').css('display','');
					$("#mybtn").hide();
					
					$.ajax({
						type: "POST",
						timeout: 600000,
						url: '<?php echo getenv('APP_URL'); ?>/getressources.php',
						data: 'url='+$("#url").val(),
						success: function (data) {
							$("#step").html('<li class="list-group-item list-group-item-warning">Récupération des ressources terminée, optimisation en cours...</li>');
							refreshInfo();
						}
					});
				}
			}
		}
		
		function refreshInfo(){
			$.ajax({
				type: "POST",
				url: '<?php echo getenv('APP_URL'); ?>/getressources.php',
				data: 'url='+$("#url").val(),
				success: function (data) {	
					var tabData = JSON.parse(data);
					var sList = "";
					var k = 0;
					if (tabData.ressources.length>0){
						sList = sList+'<li class="list-group-item list-group-item-success">Optimisation de :<br/>'+tabData.ressources[0]+' en cours...</li>';	
						sList = sList+'<li class="list-group-item list-group-item-warning">Reste '+(parseInt(tabData.ressources.length)+1)+' ressource(s) à optimiser.</li>';	
					}else{
						sList = '<li class="list-group-item list-group-item-warning">Calcul des notes Page Speed.</li>';	
					}
					
					$("#step").html(sList);
					if (tabData.scores.length==0){
						refreshInfo();
					}else{
						//Fin
						sList = '<li class="list-group-item list-group-item-success">Optimisation terminée.</li>';	
						$("#step").html(sList);
						$("#size").html(tabData.scores.size);
						$("#size_optim").html(tabData.scores.size_optim);
						$("#note_mobile").html(tabData.scores.mobile);
						$("#note_mobile_optim").html(tabData.scores.mobile_optim);
						$("#note_desktop").html(tabData.scores.desktop);
						$("#note_desktop_optim").html(tabData.scores.desktop_optim);
						$("#url_serveur").attr('href','<?php echo getenv('APP_URL'); ?>/'+tabData.scores.url_serveur);
						$("#url_serveur-nowebpack").attr('href','<?php echo getenv('APP_URL'); ?>/'+tabData.scores.url_serveur+'/index-nowebpack.html');
						$("#remove_url_serveur").attr("href",'<?php echo getenv('APP_URL'); ?>?url='+tabData.scores.url+'&remove='+tabData.scores.url_serveur);
						$("#result").show();
						$('#info').css('display','none');
						$("#mybtn").show();
					}								
				}
			});
		}
		
		<?php
        if (isset($_GET['url'])) {
            ?>
			initUrl();
			<?php
        }
        ?>
	</script>
</body>

</html>