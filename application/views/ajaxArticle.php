<form id="articleModify" method="POST" action="adminArticles/modifyArticle">
	<ul>
		<li><label>Titre : </label><input type="text" name="title"
			value="<?php echo $articles[0]->libelle_articles ;?>" /></li>
		<li><label>Groupe : </label> <select name="acces">
			<?php foreach ( $group as $i ) { ?>
				<option value="<?php echo $i->acces_id; ?>"><?php echo $i->libelle_acess; ?></option>
			<?php } ?>
		</select></li>
		<li>
		<div id="toolbar" style="display: none;">
	    <a data-wysihtml5-command="bold" title="CTRL+B">bold</a> |
	    <a data-wysihtml5-command="italic" title="CTRL+I">italic</a> |
	    <a data-wysihtml5-command="createLink">insert link</a> |
	    <a data-wysihtml5-command="insertImage">insert image</a> |
	    <a data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h1">h1</a> |
	    <a data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h2">h2</a> |
	    <a data-wysihtml5-command="insertUnorderedList">insertUnorderedList</a> |
	    <a data-wysihtml5-command="insertOrderedList">insertOrderedList</a> |
	    <a data-wysihtml5-command="foreColor" data-wysihtml5-command-value="red">red</a> |
	    <a data-wysihtml5-command="foreColor" data-wysihtml5-command-value="green">green</a> |
	    <a data-wysihtml5-command="foreColor" data-wysihtml5-command-value="blue">blue</a> |
	    <a data-wysihtml5-command="insertSpeech">speech</a>
	    <a data-wysihtml5-action="change_view">switch to html view</a>
    	<div data-wysihtml5-dialog="createLink" style="display: none;">
      <label>
        Link:
        <input data-wysihtml5-dialog-field="href" value="http://">
      </label>
      <a data-wysihtml5-dialog-action="save">OK</a>&nbsp;<a data-wysihtml5-dialog-action="cancel">Cancel</a>
    </div>

    <div data-wysihtml5-dialog="insertImage" style="display: none;">
      <label>
        Image:
        <input data-wysihtml5-dialog-field="src" value="http://">
      </label>
      <label>
        Align:
        <select data-wysihtml5-dialog-field="className">
          <option value="">default</option>
          <option value="wysiwyg-float-left">left</option>
          <option value="wysiwyg-float-right">right</option>
        </select>
      </label>
      <a data-wysihtml5-dialog-action="save">OK</a>&nbsp;<a data-wysihtml5-dialog-action="cancel">Cancel</a>
    </div>

  		</div>
		</li>
		<li>
		<label>Contenu : </label>
		<textarea id="textarea" name="content" rows="7" cols="100"><?php echo $articles[0]->content_article ;?></textarea>
		</li>
		<li>
			<input type="hidden" name="id" value="<?php echo $articles[0]->id_articles ; ?>">
			<input type="submit" value="modifier">
		</li>
	</ul>
</form>
<div id="log" style="display:none;"></div>
<script>
  var editor = new wysihtml5.Editor("textarea", {
    toolbar:      "toolbar",
    stylesheets:  "/G/public/js/css/stylesheet.css",
    parserRules:  wysihtml5ParserRules
  });

  var log = document.getElementById("log");

  editor
    .on("load", function() {
      log.innerHTML += "<div>load</div>";
    })
    .on("focus", function() {
      log.innerHTML += "<div>focus</div>";
    })
    .on("blur", function() {
      log.innerHTML += "<div>blur</div>";
    })
    .on("change", function() {
      log.innerHTML += "<div>change</div>";
    })
    .on("paste", function() {
      log.innerHTML += "<div>paste</div>";
    })
    .on("newword:composer", function() {
      log.innerHTML += "<div>newword:composer</div>";
    })
    .on("undo:composer", function() {
      log.innerHTML += "<div>undo:composer</div>";
    })
    .on("redo:composer", function() {
      log.innerHTML += "<div>redo:composer</div>";
    });
</script>
