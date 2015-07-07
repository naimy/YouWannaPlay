<header id="header" role="banner" class="line pam">
	<div class="logo">
		<img src="/public_html/images/logo/youwannaplay.png" width="150"/>
	</div>
	<?php if ($this->data['SelectedMenu'] != 'tv'){?>
	<div class="twitch">
		<div class="lecteur">
			<iframe src="http://www.twitch.tv/youwannaplay/embed" frameborder="0" scrolling="no" height="147" width="263"></iframe>
		</div>
	</div>
	<?php };?>
	<div class="login">
	<ul>
		<li class="first"><label for="login">Login :</label></li>
		<li class="Last"><input name="login" id="login" type="text" /></li>
		<li class="first"><label for="pwd">Mot de passe :</label></li>
		<li class="Last"><input name="pwd" id="pwd" type="password" /></li>
	</ul>
	</div>
	<nav id="navigation" role="navigation">
		<ul>
			<li class="<?php if ($this->data['SelectedMenu'] == 'home'){?>active<?php };?> first"><a href="/">Home</a></li>
			<li class="<?php if ($this->data['SelectedMenu'] == 'tv'){?>active<?php };?>"><a href="/index.php/tv">Chaine TV</a></li>
			<li class="<?php if ($this->data['SelectedMenu'] == 'server'){?>active<?php };?>"><a href="/index.php/server">Serveur de jeux</a></li>
			<li class="<?php if ($this->data['SelectedMenu'] == 'video'){?>active<?php };?>"><a href="/index.php/video">Vidéos</a></li>
			<li class="<?php if ($this->data['SelectedMenu'] == 'boutique'){?>active<?php };?>"><a href="/index.php/boutique">Boutique</a></li>
		</ul>
	</nav>
</header>