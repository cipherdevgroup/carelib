<?php
/**
 * The CareLib Logo image control class.
 *
 * Based on the Jetpack site logo image control.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.1.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Custom logo uploader control for the Customizer.
 *
 * @package CareLib
 */
class CareLib_Customize_Control_Site_Logo extends WP_Customize_Control {
	/**
	 * Constructor for our custom control.
	 *
	 * @param object $wp_customize
	 * @param string $control_id
	 * @param array $args
	 * @uses Site_Logo_Image_Control::l10n()
	 */
	public function __construct( $args = array() ) {
		// declare these first so they can be overridden
		$this->l10n = array(
			'upload'      => __( 'Add logo',    'carelib' ),
			'set'         => __( 'Set as logo', 'carelib' ),
			'choose'      => __( 'Choose logo', 'carelib' ),
			'change'      => __( 'Change logo', 'carelib' ),
			'remove'      => __( 'Remove logo', 'carelib' ),
			'placeholder' => __( 'No logo set', 'carelib' ),
		);

		parent::__construct( $args[0], $args[1], $args[2] );
	}

	/**
	 * This will be critical for our JS constructor.
	 */
	public $type = 'site_logo';

	/**
	 * Allows overriding of global labels by a specific control.
	 */
	public $l10n = array();

	/**
	 * The type of files that should be allowed by the media modal.
	 */
	public $mime_type = 'image';

	/**
	 * Enqueue our media manager resources, scripts, and styles.
	 *
	 * @uses wp_enqueue_media()
	 * @uses wp_enqueue_style()
	 * @uses wp_enqueue_script()
	 * @uses plugins_url()
	 */
	public function enqueue() {
		$uri = carelib()->get_uri();
		// Enqueues all needed media resources.
		wp_enqueue_media();

		// Enqueue our control script and styles.
		wp_enqueue_style(
			'site-logo-control',
			esc_url( "${uri}css/site-logo-control.css" )
		);
		wp_enqueue_script(
			'site-logo-control',
			esc_url( "{$uri}js/site-logo-control.js" ),
			array( 'media-views', 'customize-controls', 'underscore' ),
			'',
			true
		);
	}

	/**
	 * Check if we have an active site logo.
	 *
	 * @uses get_option()
	 * @return boolean
	 */
	public function has_site_logo() {
		$logo = get_option( 'site_logo' );

		if ( ! empty( $logo['url'] ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Display our custom control in the Customizer.
	 *
	 * @uses CareLib_Logo_Image_Control::l10n()
	 * @uses CareLib_Logo_Image_Control::mime_type()
	 * @uses CareLib_Logo_Image_Control::label()
	 * @uses CareLib_Logo_Image_Control::description()
	 * @uses esc_attr()
	 * @uses esc_html()
	 */
	public function render_content() {
		// We do this to allow the upload control to specify certain labels
		$l10n = wp_json_encode( $this->l10n );

		// Control title
		printf(
			'<span class="customize-control-title" data-l10n="%s" data-mime="%s">%s</span>',
			esc_attr( $l10n ),
			esc_attr( $this->mime_type ),
			esc_html( $this->label )
		);

		// Control description
		if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description">
				<?php echo $this->description; ?>
			</span>
		<?php endif; ?>

		<div class="current"></div>
		<div class="actions"></div>
		<?php
	}
}
