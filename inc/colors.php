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
 * @since   0.2.0
 */
class CareLib_Fonts {

	/**
	 * Registered fonts.
	 *
	 * @since 0.2.0
	 * @var   array
	 */
	protected static $fonts;

	/**
	 * Registered text groups.
	 *
	 * @since 0.2.0
	 * @var   array
	 */
	protected static $text_groups;

	/**
	 * Constructor method.
	 *
	 * @since 0.2.0
	 */
	public function __construct() {
		$this->register_default_fonts();
	}

	/**
	 * Wire up theme hooks for supporting custom fonts.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function add_support() {
		$objects = array(
			'customize-setup-register',
			'customize-setup-scripts',
			'public-scripts',
			'public-styles',
			'tinymce',
		);
		foreach ( $objects as $object ) {
			carelib_get( $object )->add_fonts_support();
		}

		return $this;
	}

	/**
	 * Get all registered fonts.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return array $fonts Array of fonts.
	 */
	public function get_fonts() {
		return (array) self::$fonts;
	}

	/**
	 * Get all registered text groups.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return array $groups Array of groups.
	 */
	public function get_text_groups() {
		return (array) self::$text_groups;
	}

	/**
	 * Register supported font.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array $fonts Array of fonts.
	 * @return object CareLib_Fonts
	 */
	public function add_fonts( $fonts ) {
		foreach ( $fonts as $font ) {
			$this->add_font( $font, false );
		}
	}

	/**
	 * Add a font to the collection.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array $font Font properties.
	 * @return object CareLib_Fonts
	 */
	public function add_font( $font ) {
		self::$fonts[] = wp_parse_args( $font, array(
			'family'  => '',
			'stack'   => '',
			'service' => 'google',
			'tags'    => array(),
		) );
	}

	/**
	 * Remove a registered font.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $family Font family name.
	 * @return object CareLib_Fonts
	 */
	public function remove_font( $family ) {
		foreach ( self::$fonts as $key => $font ) {
			if ( $font['family'] === $family ) {
				unset( self::$fonts[ $key ] );
			}
		}

		// Reset the array indexes.
		self::$fonts = array_values( self::$fonts );
	}

	/**
	 * Register a text group whose font can be customized.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array $group Group properties.
	 * @return object CareLib_Fonts
	 */
	public function register_text_group( $group ) {
		self::$text_groups[] = wp_parse_args( $group, array(
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
	}

	/**
	 * Register text groups.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array $groups Array of groups.
	 * @return object CareLib_Fonts
	 */
	public function register_text_groups( $groups ) {
		foreach ( $groups as $group ) {
			$this->register_text_group( $group );
		}
	}

	/**
	 * Retrieve the URL for enqueueing Google fonts.
	 *
	 * @since 0.2.0
	 *
	 * @return string
	 */
	public function get_google_fonts_url() {
		$url      = '';
		$families = array();

		foreach ( self::$text_groups as $group ) {
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

		return empty( $url ) ? false : $url;
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
	public function is_typekit_active() {
		$kit_id = get_theme_mod( 'carelib_fonts_typekit_id', '' );
		if ( empty( $kit_id ) ) {
			return false;
		}

		foreach ( self::$text_groups as $group ) {
			$setting = get_theme_mod( $group['id'] . '_font', array() );

			// Don't attempt to load if the service isn't Typekit.
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
	 * Sanitize a font.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  array $value Value to sanitize.
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
	 * @since  0.2.0
	 * @access public
	 * @param  string $value Font family name.
	 * @return string
	 */
	public function sanitize_font_family( $value ) {
		return preg_replace( '#[^a-zA-Z0-9 ]#', '', $value );
	}

	/**
	 * Sanitize a font stack.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $value Font stack.
	 * @return string
	 */
	public function sanitize_font_stack( $value ) {
		return preg_replace( '#[^a-zA-Z0-9_,\'" -]#', '', $value );
	}

	/**
	 * Retrieve font subsets to load.
	 *
	 * @since 0.2.0
	 *
	 * @return string
	 */
	public function get_subsets() {
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
	public function get_css() {
		$css = '';

		foreach ( self::$text_groups as $group ) {
			$setting = get_theme_mod( $group['id'] . '_font', array() );

			if ( empty( $setting['stack'] ) || $setting['family'] === $group['family'] ) {
				continue;
			}

			$stack = $this->sanitize_font_stack( $setting['stack'] );
			$css .= sprintf( '%s { font-family: %s;}', $group['selector'], $stack );
		}

		return empty( $css ) ? false : $css;
	}

	/**
	 * Register default fonts.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @return void
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

}
