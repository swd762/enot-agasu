jQuery(function($) {
	$('.module_special_visually #special_visually input').on('click', function() {
		$('.module_special_visually #special_visually').submit();
	});
	
	$('.module_special_visually #special_visually').submit(function(e) {
		e.preventDefault();
		$.ajax({
			type: 'POST',
			url: '',
			data: $(this).serialize(),
			success: function() {
				location.reload();
			},
			error: function (xhr, ajaxOptions, thrownError) {
				console.log(arguments);
			}
		});
	});
	
	$('.module_special_visually #special_visually .close_special_block').on('click', function() {
		$(this).siblings('.params').slideToggle();
	});
})