<?php
/**
 * Custom fonts feature.
 *
 * Based on Cedaro's custom fonts feature.
 *
 * @package   CareLib
 * @author    Brady Vercher
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

/**
 * Class for custom fonts feature.
 *
 * @package CareLib
 * @since 0.2.0
 */
class CareLib_Fonts {
	/**
	 * Registered fonts.
	 *
	 * @since 0.2.0
	 * @var array
	 */
	protected $fonts;

	/**
	 * Registered text groups.
	 *
	 * @since 0.2.0
	 * @var array
	 */
	protected $text_groups;

	/**
	 * Library prefix which can be set within themes.
	 *
	 * @since 0.2.0
	 * @var   string
	 */
	protected $prefix;

	/**
	 * WP enqueue handle for the theme's main style sheet.
	 *
	 * @since 0.2.0
	 * @var string
	 */
	protected $stylesheet_handle = '';

	/**
	 * Constructor method.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {
		$this->prefix = carelib()->get_prefix();
		$this->stylesheet_handle = "{$this->prefix}-style";
		$this->register_default_fonts();
	}

	/*
	 * Public API methods.
	 */

	/**
	 * Wire up theme hooks for supporting custom fonts.
	 *
	 * @since 0.2.0
	 */
	public function add_support() {
		// Front-end hooks.
		add_action( 'init', array( $this, 'register_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_fonts' ), 15 );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_inline_styles' ), 15 );

		// Customizer hooks.
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_action( 'customize_preview_init', array( $this, 'enqueue_customizer_preview_assets' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customizer_controls_assets' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'print_templates' ) );

		// Editor hooks.
		add_filter( 'tiny_mce_before_init', array( $this, 'register_tinymce_settings' ) );
		add_filter( 'mce_external_plugins', array( $this, 'register_tinymce_plugin' ) );
		add_action( 'mce_css', array( $this, 'add_editor_styles' ) );
		add_action( 'wp_ajax_carelib-fonts-editor-css', array( $this, 'output_editor_styles' ) );

		return $this;
	}

	/**
	 * Register supported font.
	 *
	 * @since 0.2.0
	 *
	 * @param array $fonts Array of fonts.
	 * @return $this
	 */
	public function add_fonts( $fonts ) {
		foreach ( $fonts as $font ) {
			$this->add_font( $font, false );
		}

		return $this;
	}

	/**
	 * Add a font to the collection.
	 *
	 * @param array $font Font properties.
	 * @return $this
	 */
	public function add_font( $font ) {
		$this->fonts[] = wp_parse_args( $font, array(
			'family'  => '',
			'stack'   => '',
			'service' => 'google',
			'tags'    => array(),
		) );

		return $this;
	}

	/**
	 * Remove a registered font.
	 *
	 * @since 0.2.0
	 *
	 * @param  string $family Font family name.
	 * @return $this
	 */
	public function remove_font( $family ) {
		foreach ( $this->fonts as $key => $font ) {
			if ( $font['family'] === $family ) {
				unset( $this->fonts[ $key ] );
			}
		}

		// Reset the array indexes.
		$this->fonts = array_values( $this->fonts );

		return $this;
	}

	/**
	 * Register a text group whose font can be customized.
	 *
	 * @since 0.2.0
	 *
	 * @param  array $group Group properties.
	 * @return $this
	 */
	public function register_text_group( $group ) {
		$this->text_groups[] = wp_parse_args( $group, array(
			'id'          => '',
			'label'       => '',
			'description' => '',
			'selector'    => '',
			'family'      => '',
			'variations'  => '400',
			'exclude'     => array(),
			'tags'        => array(),
			'service'     => 'google',
		) );

		return $this;
	}

	/**
	 * Register text groups.
	 *
	 * @since 0.2.0
	 *
	 * @param  array $groups Array of groups.
	 * @return $this
	 */
	public function register_text_groups( $groups ) {
		foreach ( $groups as $group ) {
			$this->register_text_group( $group );
		}

		return $this;
	}

	/*
	 * Hook callbacks.
	 */

	/**
	 * Register assets for enqueueing on demand.
	 *
	 * @since 0.2.0
	 */
	public function register_assets() {
		wp_register_script(
			'webfontloader',
			'https://ajax.googleapis.com/ajax/libs/webfont/1.5.18/webfont.js',
			array(),
			'1.5.18'
		);

		// Add Google Fonts to the editor.
		$url = $this->get_google_fonts_url();
		if ( ! empty( $url ) ) {
			add_editor_style( $url );
		}
	}

	/**
	 * Enqueue fonts.
	 *
	 * @since 0.2.0
	 */
	public function enqueue_fonts() {
		$url = $this->get_google_fonts_url();
		if ( ! empty( $url ) ) {
			wp_enqueue_style( "{$this->prefix}-fonts-google", $url );
		}

		if ( ! $this->is_typekit_active() || is_customize_preview() ) {
			return;
		}

		// Enqueue the Typekit kit.
		$kit_id = get_theme_mod( 'carelib_fonts_typekit_id', '' );
		wp_enqueue_script(
			"{$this->prefix}-fonts-typekit",
			sprintf( 'https://use.typekit.net/%s.js', sanitize_key( $kit_id ) )
		);

		add_action( 'wp_head', array( $this, 'load_typekit_fonts' ) );
	}

	/**
	 * Add embedded styles to render custom fonts for text groups.
	 *
	 * The Customizer JavaScript handles CSS, so short-circuit if the current
	 * request is a Customizer preview frame.
	 *
	 * @since 0.2.0
	 */
	public function add_inline_styles() {
		if ( is_customize_preview() ) {
			return;
		}

		$css = $this->get_css();
		if ( ! empty( $css ) ) {
			wp_add_inline_style( $this->stylesheet_handle, $css );
		}
	}

	/**
	 * Load Typekit fonts when the kit script is enqueued.
	 *
	 * @since 0.2.0
	 */
	public function load_typekit_fonts() {
		if ( wp_script_is( "{$this->prefix}-fonts-typekit", 'done' ) ) {
			echo '<script>try{Typekit.load({ async: true });}catch(e){}</script>';
		}
	}

	/**
	 * Register TinyMCE settings.
	 *
	 * Adds the Typekit Kit ID to the settings for loading in the editor.
	 *
	 * @since 0.2.0
	 *
	 * @param  array $settings TinyMCE settings.
	 * @return array
	 */
	public function register_tinymce_settings( $settings ) {
		$settings['carelibFontsTypekitId'] = get_theme_mod( 'carelib_fonts_typekit_id', '' );
		return $settings;
	}

	/**
	 * Register a TinyMCE plugin for loading custom fonts.
	 *
	 * Loads a Typekit Kit.
	 *
	 * @param  array $external_plugins List of external plugins.
	 * @return array
	 */
	public function register_tinymce_plugin( $external_plugins ) {
		if ( $this->is_typekit_active() ) {
			$external_plugins['carelibfonts'] = $this->theme->get_library_uri( '/assets/js/tinymce-fonts.js' );
		}

		return $external_plugins;
	}

	/**
	 * Register a dynamic style sheet URL for the editor.
	 *
	 * This needs to be registered after the main theme style sheet.
	 *
	 * @since 0.2.0
	 *
	 * @param string $stylesheets Comma-separated list of style sheet URLs.
	 */
	public function add_editor_styles( $stylesheets ) {
		$stylesheets .= ',' . add_query_arg( 'action', "{$this->prefix}-fonts-editor-css", admin_url( 'admin-ajax.php' ) );
		return $stylesheets;
	}

	/**
	 * Output editor styles for custom fonts.
	 *
	 * @since 0.2.0
	 *
	 * @link http://wordpress.stackexchange.com/a/120835
	 */
	public function output_editor_styles() {
		header( 'Content-Type: text/css' );
		echo $this->get_css(); // WPCS: XSS OK.
		exit;
	}

	/**
	 * Register Customizer settings and controls.
	 *
	 * @since 0.2.0
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
	 */
	public function customize_register( $wp_customize ) {
		$wp_customize->register_section_type( 'CareLib_Customize_Section_Fonts' );

		$wp_customize->add_section( new CareLib_Customize_Section_Fonts( $wp_customize, 'carelib_fonts', array(
			'title'       => esc_html__( 'Fonts', 'carelib' ),
			'priority'    => 50,
		) ) );

		$wp_customize->add_setting( 'carelib_fonts_typekit_id', array(
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );

		foreach ( $this->text_groups as $group ) {
			$id = $group['id'] . '_font';

			$wp_customize->add_setting( $id, array(
				'sanitize_callback' => array( $this, 'sanitize_font' ),
				'transport'         => 'postMessage',
			) );

			$wp_customize->add_control( new CareLib_Customize_Control_Font( $wp_customize, $id, array(
				'label'         => $group['label'],
				'description'   => $group['description'],
				'section'       => 'carelib_fonts',
				'settings'      => $id,
				'default_font'  => $group['family'],
				'exclude_fonts' => $group['exclude'],
				'tags'          => $group['tags'],
			) ) );
		}
	}

	/**
	 * Enqueue assets when previewing the site in the Customizer.
	 *
	 * @since 0.2.0
	 */
	public function enqueue_customizer_preview_assets() {
		wp_enqueue_script(
			"{$this->prefix}-customize-preview-fonts",
			$this->theme->get_library_uri( 'assets/js/customize-preview-fonts.js' ),
			array( 'customize-preview', 'wp-backbone', 'webfontloader' ),
			'1.0.0',
			true
		);

		wp_localize_script( "{$this->prefix}-customize-preview-fonts", '_carelibThemeFontsPreviewSettings', array(
			'groups'  => $this->text_groups,
			'subsets' => $this->get_subsets(),
		) );
	}

	/**
	 * Enqueue assets for handling custom controls.
	 *
	 * @since 0.2.0
	 */
	public function enqueue_customizer_controls_assets() {
		wp_enqueue_style(
			"{$this->prefix}-customize-controls-fonts",
			$this->theme->get_library_uri( 'assets/css/customize-controls-fonts.css' ),
			array(),
			'1.0.0'
		);

		wp_enqueue_script(
			"{$this->prefix}-customize-controls-fonts",
			$this->theme->get_library_uri( 'assets/js/customize-controls-fonts.js' ),
			array( 'customize-controls', 'wp-backbone', 'webfontloader' ),
			'1.0.0',
			true
		);

		wp_localize_script( "{$this->prefix}-customize-controls-fonts", '_carelibThemeFontsControlsSettings', array(
			'fonts' => $this->fonts,
			'l10n'  => array(
				'reset'       => esc_html__( 'Reset', 'carelib' ),
				'defaultFont' => esc_html__( 'Default Theme Font', 'carelib' ),
			),
		) );
	}

	/**
	 * Print Underscore.js templates in the Customizer footer.
	 *
	 * @since 0.2.0
	 */
	public function print_templates() {
		?>
		<script type="text/html" id="tmpl-carelib-fonts-control-font">
			<label>
				<# if ( data.label ) { #>
					<span class="customize-control-title">{{{ data.label }}}</span>
				<# } #>

				<# if ( data.description ) { #>
					<span class="description customize-control-description">{{{ data.description }}}</span>
				<# } #>
			</label>
			<div class="carelib-fonts-control-content"></div>
		</script>
		<?php
	}

	/**
	 * Sanitize a font.
	 *
	 * @sine 0.2.0
	 *
	 * @param array $value Value to sanitize.
	 * @return array
	 */
	public function sanitize_font( $value ) {
		$defaults = array(
			'family'  => '',
			'stack'   => '',
			'service' => '',
		);

		$value = wp_parse_args( (array) $value, $defaults );
		$value = array_intersect_key( $value, $defaults );

		$value['family']  = $this->sanitize_font_family( $value['family'] );
		$value['stack']   = $this->sanitize_font_stack( $value['stack'] );
		$value['service'] = sanitize_key( $value['service'] );

		return $value;
	}

	/**
	 * Sanitize a font family name.
	 *
	 * @since 0.2.0
	 *
	 * @param  string $value Font family name.
	 * @return string
	 */
	public function sanitize_font_family( $value ) {
		return preg_replace( '#[^a-zA-Z0-9 ]#', '', $value );
	}

	/**
	 * Sanitize a font stack.
	 *
	 * @since 0.2.0
	 *
	 * @param  string $value Font stack.
	 * @return string
	 */
	public function sanitize_font_stack( $value ) {
		return preg_replace( '#[^a-zA-Z0-9_,\'" -]#', '', $value );
	}

	/*
	 * Protected methods.
	 */

	/**
	 * Register default fonts.
	 *
	 * @since 0.2.0
	 */
	protected function register_default_fonts() {
		$this->add_fonts( array(
			array( 'family' => 'Anonymous Pro',      'stack' => '"Anonymous Pro", monospace',     'tags' => array( 'content' ) ),
			array( 'family' => 'Arimo',              'stack' => 'Arimo, sans-serif',              'tags' => array( 'content' ) ),
			array( 'family' => 'Chivo',              'stack' => '"Chivo", sans-serif',            'tags' => array( 'heading' ) ),
			array( 'family' => 'Cousine',            'stack' => '"Cousine", sans-serif',          'tags' => array( 'content' ) ),
			array( 'family' => 'Crimson Text',       'stack' => '"Crimson Text", serif',          'tags' => array( 'content' ) ),
			array( 'family' => 'Gentium Book Basic', 'stack' => '"Gentium Book Basic", serif',    'tags' => array( 'content' ) ),
			array( 'family' => 'Kameron',            'stack' => '"Kameron", serif',               'tags' => array( 'heading' ) ),
			array( 'family' => 'Karla',              'stack' => '"Karla", sans-serif',            'tags' => array( 'content' ) ),
			array( 'family' => 'Lato',               'stack' => 'Lato, sans-serif',               'tags' => array( 'content' ) ),
			array( 'family' => 'Libre Baskerville',  'stack' => '"Libre Baskerville", serif',     'tags' => array( 'content' ) ),
			array( 'family' => 'Lora',               'stack' => 'Lora, serif',                    'tags' => array( 'content' ) ),
			array( 'family' => 'Merriweather',       'stack' => 'Merriweather, serif',            'tags' => array( 'content' ) ),
			array( 'family' => 'Montserrat',         'stack' => '"Montserrat", sans-serif',       'tags' => array( 'heading' ) ),
			array( 'family' => 'Noticia Text',       'stack' => '"Noticia Text", serif',          'tags' => array( 'content' ) ),
			array( 'family' => 'Noto Serif',         'stack' => '"Noto Serif", serif',            'tags' => array( 'content' ) ),
			array( 'family' => 'Open Sans',          'stack' => '"Open Sans", sans-serif',        'tags' => array( 'content' ) ),
			array( 'family' => 'Oswald',             'stack' => '"Oswald", sans-serif',           'tags' => array( 'heading' ) ),
			array( 'family' => 'Playfair Display',   'stack' => '"Playfair Display", serif',      'tags' => array( 'heading' ) ),
			array( 'family' => 'PT Sans',            'stack' => '"PT Sans", sans-serif',          'tags' => array( 'content' ) ),
			array( 'family' => 'PT Serif',           'stack' => '"PT Serif", serif',              'tags' => array( 'content' ) ),
			array( 'family' => 'Raleway',            'stack' => '"Raleway", sans-serif',          'tags' => array( 'heading' ) ),
			array( 'family' => 'Roboto',             'stack' => 'Roboto, sans-serif',             'tags' => array( 'content' ) ),
			array( 'family' => 'Roboto Condensed',   'stack' => '"Roboto Condensed", sans-serif', 'tags' => array( 'heading' ) ),
			array( 'family' => 'Roboto Slab',        'stack' => '"Roboto Slab", serif',           'tags' => array( 'heading' ) ),
			array( 'family' => 'Source Code Pro',    'stack' => '"Source Code Pro", monospace',   'tags' => array( 'content' ) ),
			array( 'family' => 'Source Sans Pro',    'stack' => '"Source Sans Pro", sans-serif',  'tags' => array( 'content' ) ),
			array( 'family' => 'Vollkorn',           'stack' => '"Vollkorn", serif',              'tags' => array( 'content' ) ),
		) );
	}

	/**
	 * Retrieve the URL for enqueueing Google fonts.
	 *
	 * @since 0.2.0
	 *
	 * @return string
	 */
	protected function get_google_fonts_url() {
		$url      = '';
		$families = array();

		foreach ( $this->text_groups as $group ) {
			$setting = get_theme_mod( $group['id'] . '_font', array() );

			// Don't attempt to load if the service isn't Google.
			if (
				( empty( $setting['family'] ) && 'google' !== $group['service'] ) ||
				( ! empty( $setting['family'] ) && 'google' !== $setting['service'] )
			) {
				continue;
			}

			$family = empty( $setting['family'] ) ? $group['family'] : $setting['family'];
			if ( ! empty( $group['variations'] ) ) {
				$family .= ':' . $group['variations'];
			}

			$families[] = $family;
		}

		if ( ! empty( $families ) ) {
			$query_args = array(
				'family' => rawurlencode( rtrim( implode( '|', $families ), ':' ) ),
				'subset' => urlencode( $this->get_subsets() ),
			);

			$url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
		}

		return $url;
	}

	/**
	 * Whether Typekit should be loaded on the front-end.
	 *
	 * Checks to ensure a Typekit Kit ID has been saved and a Typekit font has
	 * been selected for at least one text group.
	 *
	 * @since 0.2.0
	 *
	 * @return boolean
	 */
	protected function is_typekit_active() {
		$kit_id = get_theme_mod( 'carelib_fonts_typekit_id', '' );
		if ( empty( $kit_id ) ) {
			return false;
		}

		foreach ( $this->text_groups as $group ) {
			$setting = get_theme_mod( $group['id'] . '_font', array() );

			// Don't attempt to load if the service isn't Google.
			if (
				( empty( $setting['family'] ) && 'typekit' === $group['service'] ) ||
				( ! empty( $setting['family'] ) && 'typekit' === $setting['service'] )
			) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Retrieve font subsets to load.
	 *
	 * @since 0.2.0
	 *
	 * @return string
	 */
	protected function get_subsets() {
		$subsets = 'latin';

		/*
		 * translators: To add a character subset specific to your language,
		 * translate this to 'latin-ext', 'cyrillic', 'greek', or 'vietnamese'.
		 * Do not translate into your own language.
		 */
		$subset = esc_html_x( 'no-subset', 'Add new subset (latin-ext)', 'carelib' );

		if ( 'latin-ext' === $subset ) {
			$subsets .= ',latin-ext';
		} elseif ( 'cyrillic' === $subset ) {
			$subsets .= ',cyrillic,cyrillic-ext';
		} elseif ( 'greek' === $subset ) {
			$subsets .= ',greek,greek-ext';
		} elseif ( 'vietnamese' === $subset ) {
			$subsets .= ',vietnamese';
		}

		return $subsets;
	}

	/**
	 * Retrieve CSS for overriding default fonts with custom fonts.
	 *
	 * @since 0.2.0
	 *
	 * @return string
	 */
	protected function get_css() {
		$css = '';

		foreach ( $this->text_groups as $group ) {
			$setting = get_theme_mod( $group['id'] . '_font', array() );

			if ( empty( $setting['stack'] ) || $setting['family'] === $group['family'] ) {
				continue;
			}

			$stack = $this->sanitize_font_stack( $setting['stack'] );
			$css .= sprintf( '%s { font-family: %s;}', $group['selector'], $stack );
		}

		return $css;
	}

}
