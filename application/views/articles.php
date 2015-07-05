<?php foreach ( $articles as $i ) { ?>
<a><h2><?php echo mb_strtoupper($i->libelle_articles , 'UTF-8');?></h2></a>
<div class="articles">
	<p><?php echo $i->content_article ;?></p>
</div>
<hr/>
<?php } ?>