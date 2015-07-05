function modify() {
	valueSelect = $('#userModidy option:selected').val();
	$.ajax({
		url : '/G/index.php/modificationUsers',
		type : 'POST', // Le type de la requête HTTP.
		data : 'id=' + valueSelect,
		dataType : 'html',
		success : function(data) {
			$('#modifyuser').html(data);
		}
	});
};

function loadArticle() {
    valueSelect = $('#selectArticle option:selected').val();
    $.ajax({
        url : '/G/index.php/ajaxArticle',
        type : 'POST', // Le type de la requête HTTP.
        data : 'id=' + valueSelect,
        dataType : 'html',
        success : function(data) {
            $('#articleAjax').html(data);
        }
    });
};

function changeTitle() {
    valueSelect = $('#changeTitle').val();
    $.ajax({
        url : '/G/index.php/ajaxTitle',
        type : 'POST', // Le type de la requête HTTP.
        data : 'title=' + valueSelect,
        dataType : 'html',
        success : function(data) {
            alert('mise à jour réussi');
            window.location='/G/index.php/site';
        }

    });
};