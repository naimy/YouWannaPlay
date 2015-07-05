<h1>Gestion des Articles</h1>
<h2>Liste des articles crées</h2>
<form id="selectArticle">
	<select onchange="loadArticle();">
		<?php foreach($Articles as $article){?>
		<option value="<?php echo ($article->id_articles); ?>"><?php echo ($article->libelle_articles); ?></option>
		<?php }?>
	</select>
</form>
<div id='articleAjax'></div>