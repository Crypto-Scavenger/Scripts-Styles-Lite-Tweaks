/**
 * Admin scripts for Scripts & Styles Lite Tweaks
 *
 * @package ScriptsStylesLiteTweaks
 * @since 1.0.0
 */

(function($) {
	'use strict';
	
	$(document).ready(function() {
		// Add confirmation for dangerous options
		$('#disable_jquery_migrate').on('change', function() {
			if ($(this).is(':checked')) {
				if (!confirm('Warning: Disabling jQuery Migrate may break older plugins or themes. Are you sure?')) {
					$(this).prop('checked', false);
				}
			}
		});
	});
})(jQuery);
