<?php
/**
 * Loads customizer-related files (see `/inc/customize`) and sets up customizer
 * functionality.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

class CareLib_Customize_Setup_Scripts extends CareLib_Scripts {

	protected $fonts;

	/**
	 * Get our class up and running!
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function run() {
		$this->wp_hooks();
	}

	/**
	 * Register our actions and filters.
	 *
	 * @since  0.2.0
	 * @access protected
	 * @return void
	 */
	protected function wp_hooks() {
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'register_controls' ), 0 );
		add_action( 'customize_preview_init',             array( $this, 'register_preview' ),  0 );
		add_action( 'customize_preview_init',             array( $this, 'enqueue_preview' ),  10 );
	}

	/**
	 * Add support for the CareLib Fonts feature.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function add_fonts_support() {
		$this->fonts = carelib_get( 'fonts-hooks' )->customize_scripts( $this );
	}

	/**
	 * Register customizer controls scripts.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function register_controls() {
		wp_register_script(
			'carelib-customize-controls',
			$this->js_uri( "customize-controls{$this->suffix}.js" ),
			array( 'customize-controls' ),
			$this->version,
			true
		);
		wp_register_script(
			'site-logo-control',
			esc_url( $this->js_uri( "site-logo-control{$this->suffix}.js" ) ),
			array( 'media-views', 'customize-controls', 'underscore' ),
			'',
			true
		);
	}

	/**
	 * Register customizer preview scripts.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function register_preview() {
		wp_register_script(
			'carelib-customize-preview',
			$this->js_uri( "customize-preview{$this->suffix}.js" ),
			array( 'jquery' ),
			$this->version,
			true
		);
		wp_register_script(
			'site-logo-preview',
			esc_url( $this->js_uri( "site-logo-preview{$this->suffix}.js" ) ),
			array( 'media-views' ),
			$this->version,
			true
		);
	}

	/**
	 * Register customizer preview scripts.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function enqueue_preview() {
		wp_enqueue_script( 'carelib-customize-preview' );
	}

	/**
	 * Enqueue assets when previewing the site in the Customizer.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function enqueue_fonts_preview() {
		wp_enqueue_script(
			'carelib-customize-preview-fonts',
			esc_url( $this->js_uri( 'customize-preview-fonts.js' ) ),
			array( 'customize-preview', 'wp-backbone', 'webfontloader' ),
			'1.0.0',
			true
		);

		wp_localize_script( 'carelib-customize-preview-fonts', 'carelibFontsPreviewSettings', array(
			'groups'  => $this->fonts->get_text_groups(),
			'subsets' => $this->fonts->get_subsets(),
		) );
	}

	/**
	 * Enqueue assets for handling custom controls.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function enqueue_fonts_controls() {
		wp_enqueue_style(
			'carelib-customize-controls-fonts',
			esc_url( $this->css_uri( 'customize-controls-fonts.css' ) ),
			array(),
			'1.0.0'
		);

		wp_localize_script( 'carelib-customize-controls-fonts', 'carelibFontsControlsSettings', array(
			'fonts' => $this->fonts->get_fonts(),
			'l10n'  => array(
				'reset'       => esc_html__( 'Reset', 'carelib' ),
				'defaultFont' => esc_html__( 'Default Theme Font', 'carelib' ),
			),
		) );
	}

	/**
	 * Print Underscore.js templates in the Customizer footer.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function print_fonts_templates() {
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

}
