<?php
/**
 * Fonts section for the Customizer.
 *
 * Based on Cedaro's custom fonts feature.
 *
 * @package   CareLib
 * @author    Brady Vercher
 * @copyright Copyright (c) 2016, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

/**
 * Fonts section class.
 *
 * @since 0.2.0
 *
 * @see WP_Customize_Section
 */
class CareLib_Customize_Section_Fonts extends WP_Customize_Section {
	/**
	 * Customize section type.
	 *
	 * @since 0.2.0
	 * @var string
	 */
	public $type = 'carelib-fonts';

	/**
	 * An Underscore (JS) template for rendering this section.
	 *
	 * @since 0.2.0
	 * @access protected
	 *
	 * @see WP_Customize_Section::print_template()
	 */
	protected function render_template() {
		$typekit_id = get_theme_mod( 'carelib_fonts_typekit_id', '' );
		?>
		<li id="accordion-section-{{ data.id }}" class="accordion-section control-section control-section-{{ data.type }}">
			<h3 class="accordion-section-title" tabindex="0">
				{{ data.title }}
				<span class="screen-reader-text"><?php esc_html_e( 'Press return or enter to open', 'carelib' ); ?></span>
			</h3>
			<ul class="accordion-section-content">
				<li class="customize-section-description-container customize-info">
					<div class="customize-section-title">
						<button class="customize-section-back" tabindex="-1">
							<span class="screen-reader-text"><?php esc_html_e( 'Back', 'carelib' ); ?></span>
						</button>
						<h3>
							<span class="customize-action">
								{{{ data.customizeAction }}}
							</span>
							{{ data.title }}
						</h3>
						<button type="button" class="customize-help-toggle carelib-fonts-section-toggle dashicons dashicons-editor-help" aria-expanded="false" data-target="#carelib-fonts-section-description">
							<span class="screen-reader-text"><?php esc_html_e( 'Help', 'carelib' ); ?></span>
						</button>
						<button type="button" class="customize-screen-options-toggle carelib-fonts-section-toggle" aria-expanded="false" data-target="#carelib-fonts-section-options">
							<span class="screen-reader-text"><?php esc_html_e( 'Font Options', 'carelib' ); ?></span>
						</button>
					</div>
					<div id="carelib-fonts-section-description" class="description customize-section-description carelib-fonts-section-content">
						<?php esc_html_e( 'Easily customize your fonts. Try to re-use fonts where possible to keep your website snappy.', 'carelib' ); ?>
					</div>
					<div id="carelib-fonts-section-options" class="carelib-fonts-section-content">
						<p>
							<label for="carelib-fonts-option-typekit-id"><?php esc_html_e( 'Typekit Integration', 'carelib' ); ?></label>
						</p>
						<p>
							<?php
							$text = sprintf(
								__( 'Enter a Kit ID to make custom fonts from Typekit available in each dropdown. %s', 'carelib' ),
								sprintf( '<a href="http://support.wpsitecare.com/typekit/">%s</a>', esc_html__( 'Learn more.', 'carelib' ) )
							);

							echo wp_kses( $text, array( 'a' => array( 'href' => array() ) ) );
							?>
						</p>
						<p>
							<input type="text" id="carelib-fonts-option-typekit-id" value="<?php echo esc_attr( $typekit_id ); ?>">
							<span class="spinner"></span>
						</p>
					</div>
				</li>
			</ul>
		</li>
		<?php
	}
}
