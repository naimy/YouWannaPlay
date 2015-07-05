<!doctype html>
<html class="no-js" lang="fr">
<head>
		<meta charset="UTF-8">
		<title>YouWannaPlay - Multigaming</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="/public_html/css/screen.css" media="all">
		<link rel="stylesheet" href="/public_html/css/styles.css" media="all">
</head>
<body>
	<div class="container">
		<div class="header">
			<?php if($header) echo $header ;?>
		</div>
		
		<div class="content">
			<h2><?php echo $this->data['title']; ?></h2>
			<?php if($content) echo $content ;?>
		</div>
		
		<div class="right">
			<?php if($right) echo $right ;?>
		</div>
		
		<div class="footer">
			<?php if($footer) echo $footer ;?>
		</div>
	</div>
</body>
</html>