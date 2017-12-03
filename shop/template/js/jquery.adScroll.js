(function($) {
	$.fn.extend({
		adScroll: function(opt) {
			var defaultSettings = {
				"line": 1,
				"maxLine": 5,
				"timer": 1500,
				"speed": 3000
			};
			var options = $.extend({}, defaultSettings, opt),
				_this = $(this);
			var scrollUp = function() {
					var _firstLi = $("li:first", _this);
					var _height = options.line * (_firstLi.height());
					_firstLi.animate({
						"margin-top": "-" + _height + "px"
					}, options.speed, function() {
						$(this).removeAttr("style");
						$("ul", _this).append($(this));
						setTimeout(scrollUp, options.timer);
					});
				}
			if ($("li", _this).length > options.maxLine) {
				setTimeout(scrollUp, options.timer);
			}
		}
	});
})(jQuery);