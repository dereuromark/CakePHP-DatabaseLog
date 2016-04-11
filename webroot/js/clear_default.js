/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */

(function($){
	$.fn.clearDefault = function(){
		return this.each(function(){

			var default_value = $(this).val();
			$(this).focus(function(){
				if ($(this).val() == default_value) { $(this).val(""); }
			});
			$(this).blur(function(){
				if ($(this).val() == "") { $(this).val(default_value); }
			});
		});
	};
})(jQuery);

$(document).ready(function() {
	$('input:text.clear_default').clearDefault();
});