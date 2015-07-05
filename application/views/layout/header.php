<header id="header" role="banner" class="line pam">
	<div class="logo">
		<img src="/public_html/images/logo/youwannaplay.png" width="150"/>
	</div>
	<div class="login">
	<ul>
		<li class="first"><label for="login">Login :</label></li>
		<li class="Last"><input name="login" id="login" type="text" /></li>
		<li class="first"><label for="pwd">Mot de passe :</label></li>
		<li class="Last"><input name="pwd" id="pwd" type="text" /></li>
	</ul>
	</div>
	<nav id="navigation" role="navigation">
		<ul>
			<li class="<?php if ($this->data['SelectedMenu'] == 'home'){?>active<?php };?> first"><a href="/">Home</a></li>
			<li class="<?php if ($this->data['SelectedMenu'] == 'tv'){?>active<?php };?>"><a href="/index.php/tv">Chaine TV</a></li>
			<li class=""><a href="/">Serveur de jeux</a></li>
			<li class=""><a href="/">Vidéos</a></li>
			<li class=""><a href="/">Boutique</a></li>
		</ul>
	</nav>
</header>