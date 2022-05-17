(function($) {  // Avoid conflicts with other libraries

	'use strict';

	var title = '';

	phpbb.addAjaxCallback('who_posted', function(data) {
		if (data.error) {
			console.log(data.error);
			phpbb.alert(data.title, data.error);

			return;
		}

		var data_array = '<div class="align-items-center border-bottom d-flex justify-content-between mb-2 fw-bold"><span>' + whoposted_header + '</span><span>' + whoposted_posts_header + '</span></div>';

		$.each(data, function (index, value)
		{
			if (value.message_title)
			{
				title = '<span class="fs-6">' + value.message_title + '</span>';
			}
			else
			{
				data_array += ''+ '<div class="d-flex justify-content-between align-items-center">' + '<span>' + value.username +'</span>'+ '<span>' + value.posts + '</span>'+'</div>';
			}
		});
		phpbb.alert(title, data_array);
	});

})(jQuery);