<form method="POST" action="<?php echo site_url() ?>index.php/adminusers/modifyusers">
	<input type="hidden" name="id_user" value="<?php echo $id_user;?>">
	<ul class="noList">
		<li>
			<label for="login">Login</label>
			<input name="login" type="text" value="<?php echo $user[0]->login_user; ?>"/>
		</li>
		<li>
			<label for="password">password</label>
			<input name="password" type="password"  value="<?php echo $user[0]->password_user; ?>" disabled/>
		</li>
		<li>
			<label for="Name">Name</label>
			<input name="Name" type="text" value="<?php echo $user[0]->name; ?>"/>
		</li>
		<li>
			<label for="mail">E-mail</label>
			<input name="mail" type="text" value="<?php echo $user[0]->user_email; ?>"/>
		</li>
		<li>
			<label for="Groupe">Groupe</label>
			<select name="groupUsers" id="groupUsers">
				<?php foreach ( $group as $i ) { ?>
				<option <?php if( $i->acces_id == $user[0]->acces_id){echo "selected"; }; ?> value="<?php echo $i->acces_id; ?>"><?php echo $i->libelle_acess; ?></option>
				<?php } ?>
			</select>
		</li>
		<li>
			<button onclick="$('#modifyuser form').remove();return false;" id="modifyCanceled">annuler</button><input type="submit"/>
		</li>
	</ul>
</form>