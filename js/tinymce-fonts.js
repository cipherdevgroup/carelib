/**
 * TinyMCE plugin for loading Typekit fonts.
 */
(function() {
	function loadTypekit( editor, kitId ) {
		var scriptTag = editor.getDoc().createElement( 'script' );

		scriptTag.type = 'text/javascript';
		scriptTag.src = 'https://use.typekit.net/' + kitId + '.js';

		scriptTag.onload = function () {
			try {
				editor.getWin().Typekit.load({ async: true });
			} catch( e ) {}
		};

		editor.getDoc().getElementsByTagName( 'head' )[0].appendChild( scriptTag );
	}

	tinymce.PluginManager.add( 'carelibfonts', function( editor ) {
		var kitId = editor.settings.carelibFontsTypekitId.replace( /[^a-z0-9]+/, '' );

		if ( '' !== kitId ) {
			editor.on( 'init', function() {
				loadTypekit( editor, kitId );
			});
		}
	});
})();
