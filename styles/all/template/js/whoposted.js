(function($) {  // Avoid conflicts with other libraries

	'use strict';

	var title = '';

	phpbb.addAjaxCallback('who_posted', function(data) {
		if (data.error) {
			console.log(data.error);
			phpbb.alert(data.title, data.error);

			return;
		}

		var data_array = '<span class="whoposted_header">' + whoposted_header + '</span><span class="whoposted_posts_header">' + whoposted_posts_header + '</span><br>';

		$.each(data, function (index, value)
		{
			if (value.message_title)
			{
				title = '<span class="whoposted_title">' + value.message_title + '</span>';
			}
			else
			{
				data_array += '' + value.username + '<span class="whoposted_posts">' + value.posts + '</span><br>';
			}
		});
		phpbb.alert(title, data_array);
	});

})(jQuery);