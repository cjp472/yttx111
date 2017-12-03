(function($) {
    $.extend({
        _jsonp: {
            scripts: {},
            counter: 1,
            charset: "UTF-8",
            head: document.getElementsByTagName("head")[0],
            name: function(callback) {
                var name = '_jsonp_' + (new Date).getTime() + '_' + this.counter;
                this.counter++;
                var cb = function(json) {
                    eval('delete ' + name);
                    callback(json);
                    $._jsonp.head.removeChild($._jsonp.scripts[name]);
                    delete $._jsonp.scripts[name];
                };
                eval(name + ' = cb');
                return name;
            },
            load: function(url, name) {
                var script = document.createElement('script');
                script.type = 'text/javascript';
                script.charset = this.charset;
                script.src = url;
                this.head.appendChild(script);
                this.scripts[name] = script;
            }
        },
        getJSONP: function(url, callback) {
            var name = $._jsonp.name(callback);
            var url = url.replace(/{callback};/, name);
            $._jsonp.load(url, name);
            return this;
        }
    });
})(jQuery);

//屏蔽F12
$(function(){
	$(document).bind('keydown', function(e){
		//if(e.keyCode == 123) return false;
	});
});