<h1>Gestion des users</h1>
	<div class="blocAdmin fL">
	<h2>Ajouter un user :</h2>
	<form method="POST" action="<?php echo site_url() ?>index.php/adminusers/addlogin">
		<ul class="noList">
			<li>
				<label for="login">Login</label>
				<input name="login" type="text" />
			</li>
			<li>
				<label for="password">password</label>
				<input name="password" type="password" />
			</li>
			<li>
				<label for="Name">Name</label>
				<input name="Name" type="text" />
			</li>
			<li>
				<label for="mail">E-mail</label>
				<input name="mail" type="text" />
			</li>
			<li>
				<label for="Groupe">Groupe</label>
				<select name="groupUsers" id="groupUsers">
					<?php foreach ( $group as $i ) { ?>
					<option value="<?php echo $i->acces_id; ?>"><?php echo $i->libelle_acess; ?></option>
					<?php } ?>
				</select>
			</li>
			<li>
				<input type="submit"/>
			</li>
		</ul>
	</form>
</div>
<div class="blocAdmin fL">
	<h2>modifier un user :</h2>
		<select id="userModidy" name="userModidy">
			<?php foreach ( $users as $i ) { ?>
			<option value="<?php echo $i->id_user; ?>"><?php echo $i->login_user; ?></option>
			<?php } ?>
		</select>
		<button onclick="modify()">Modifier</button>
		<div id="modifyuser"></div>
</div>
<div class="blocAdmin fL">
<h2>supprimer un user :</h2>
	<form method="POST" action="<?php echo site_url() ?>index.php/adminusers/deletelogin">
	<select id="idUserDelete" name='idUserDelete'>
		<?php foreach ( $users as $i ) { ?>
		<option value="<?php echo $i->id_user; ?>"><?php echo $i->login_user; ?></option>
		<?php } ?>
	</select>
	<input type="submit" value="Supprimer" />
	</form>
</div>