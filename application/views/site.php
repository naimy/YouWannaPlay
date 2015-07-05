<hr />
<h1>Gestion du site</h1>
<hr />
<h2>Titre du site :</h2>
<hr />
<input id="changeTitle" type="text" value="<?php echo $title; ?>" /><input type="submit" value="Modifier" onclick="changeTitle()" />
<hr />
<h2>Données du site :</h2>
<hr />
<form method="POST" action="<?php echo site_url() ?>/index.php/site/updateInfo">
<ul id="listOfServerFonctinnality">
	<li><label for="ts3">Adresse du ts3 :</label><input id="ts3" name="ts3" type="text" value="<?php echo $adress[0]->value;?>" /></li>
	<li><label for="transmission">Adresse du transmission :</label><input id="transmission" name="transmission" type="text" value="<?php echo $adress[1]->value; ?>" /></li>
	<li><label for="plex">Adresse du Plex :</label><input id="plex" name="plex" type="text" value="<?php echo $adress[2]->value; ?>" /></li>
	<li><label for="server">Adresse du server : </label><input id="server" name="server" type="text" value="<?php  echo $adress[3]->value; ?>" /></li>
	<li><label for="webmin">Adresse de webmin : </label><input id="webmin" name="webmin" type="text" value="<?php  echo $adress[4]->value; ?>" /></li>
	<li><input type="submit" value="modifier" /></li>
</form>
</ul>