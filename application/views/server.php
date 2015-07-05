<h1>Gestion du server</h1>
<h2 class="maT10">TS3 :</h2>
<?php
$output = shell_exec('ps -A | grep ts3server_linux');
if(empty($output))
{
	echo '<a href="http://naimy.serveftp.com/G/index.php/server?ts3=1">Démarrer Ts3</a>';
	if(isset($_GET['ts3']) && $_GET['ts3'] == "1"){
		shell_exec('/home/naimy/teamspeak3-server_linux-amd64/ts3server_startscript.sh start');
		header('Location: http://naimy.serveftp.com/G/index.php/server');
	}
}else
{
	echo "Le serveur Ts est déja demarer !<br />";
	echo '<a href="http://naimy.serveftp.com/G/index.php/server?ts3=2">Restart</a>';
	if(isset($_GET['ts3']) && $_GET['ts3'] == "2"){
		shell_exec('/home/naimy/teamspeak3-server_linux-amd64/ts3server_startscript.sh restart');
		header('Location: /G/index.php/server');
	}
}
echo shell_exec('ls -A | grep ts3server_linux');
?>
<h2 class="maT10">Processus :</h2>
<a href="http://naimy.serveftp.com/G/index.php/server?process=1">Voir les processus</a>
<?php
	if(isset($_GET['process']) && $_GET['process'] == "1"){
			$output = shell_exec('ps -Af');
			print_r("<pre>".$output."</pre>");
			echo '<a href="http://naimy.serveftp.com/G/index.php/server">Cacher les processus</a>';
	}
?>
<h2 class="maT10">Transmission :</h2>
<iframe src="http://<?php echo $adresse_Transmission; ?>" width="100%" height="500">
  <p>Votre navigateur ne supporte pas l'élément iframe</p>
</iframe>