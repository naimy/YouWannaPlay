<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<META http-equiv="Content-Script-Type" content="type">
<title>Serveur G</title>

<link href="<?php echo site_url() ?>public/css/templatemo_style.css"
	rel="stylesheet" type="text/css" />
<link href="<?php echo site_url() ?>public/css/global.css"
	rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" media="print"
	href="<?php echo site_url() ?>public/css/print.css" />
</head>
<body>
	<div id="templatemo_wrapper">
		<div id="ts3Viewer">
	<?php
	/*
	 * require_once("/var/www/G/ts3/tsstatus/tsstatus.php"); $tsstatus = new TSStatus("192.168.1.13", 10011); $tsstatus->useServerPort(9987); $tsstatus->imagePath = "/G/ts3/tsstatus/img/"; $tsstatus->timeout = 2; $tsstatus->hideEmptyChannels = false; $tsstatus->hideParentChannels = false; $tsstatus->showNicknameBox = true; $tsstatus->showPasswordBox = false; echo $tsstatus->render();
	 */
	?>
	</div>
		<div id="site_title">
			<h1><?php echo $title; ?></h1>
		</div>
		<div id="templatemo_header">
			<div id="templatemo_menu">
				<div class="auth">
			<?php if($connexion == false){ ?>
			<form method="POST" action="<?php echo site_url() ?>index.php/login">
						<ul>
							<li><input type="text" name="login" class="login"
								placeholder="Login" /><input type="password" name="password"
								class="pawd" placeholder="password" /><input type="submit"
								class="learn_more" /></li>
						</ul>
					</form>
			<?php }else{?>
				<span id="pseudo" class="hidden"><?php echo $_SESSION['pseudo'] ?></span>
					<div>
						Bienvenue <span id="nom"><?php echo $_SESSION['name'] ?></span> Groupe <?php echo $_SESSION['acces']?>
				<a href="<?php echo site_url() ?>index.php/login/logout"
							title="deconnexion" class="logout learn_more">Deconnexion</a>
					</div>
			<?php }?>
		</div>
				<ul>
			<?php foreach ( $nav as $i ) { ?>
				<?php if($i->libelle_nav == 'Chat'){ ?>
					<?php if($connexion){?><li class="<?php echo $i->libelle_nav; ?>"><?php echo $i->libelle_nav; ?></li><?php } ?>
				<?php } else { ?>
					<li><a href="<?php echo site_url().$i->link_nav; ?>"><?php echo $i->libelle_nav; ?></a>
					</li>
				<?php } ?>

			<?php }; ?>
			</ul>
			</div>
			<div class="cleaner"></div>
		</div>
		<div class="tchatContainer">
			<form id="input" style="display:none">
				<input/>
			</form>
			<div id="content"></div>
		</div>
		<?php if( isset($_GET['error']) && $_GET['error'] == 1){?>
			<div class="error">Erreur de connexion, verifier vos identifiants</div>
			<?php } ?>
		<!-- end of middle -->
		<div id="templatemo_main">
		<?php if( isset($_GET['error']) && $_GET['error'] == 1){?>
		<div class="error">Erreur sur la modification</div>
		<?php } ?>
		<?php if( isset($_GET['sucess']) && $_GET['sucess'] == 1){?>
			<div class="success">Modifications effectués</div>
		<?php } ?>