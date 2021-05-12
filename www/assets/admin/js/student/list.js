jQuery(function($){

	$('.action_delete').on('click', function(e){
		var $btn = $(e.currentTarget),
			studentId = $btn.closest('tr').data('id');
		if ( ! confirm('Вы действительно хотите удалить ученика?')) return;
		$.post('/api/student/delete/' + studentId, {_nonce: $btn.data('nonce')}, function(r){
			location.reload();
		});
	});

});