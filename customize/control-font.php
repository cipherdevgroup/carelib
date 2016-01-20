<?php
/**
 * Font control for the Customizer.
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
 * Font control class.
 *
 * @package CareLib
 * @since 0.2.0
 */
class CareLib_Customize_Control_Font extends WP_Customize_Control {
	/**
	 * Control type.
	 *
	 * @since 0.2.0
	 * @var string
	 */
	public $type = 'carelib-font';

	/**
	 * Default font.
	 *
	 * @since 0.2.0
	 * @var string
	 */
	public $default_font = '';

	/**
	 * Fonts to exclude from the dropdown.
	 *
	 * @since 0.2.0
	 * @var array
	 */
	public $exclude_fonts = array();

	/**
	 * Font tags.
	 *
	 * @since 0.2.0
	 * @var array
	 */
	public $tags = array();

	/**
	 * Refresh the parameters passed to JavaScript via JSON.
	 *
	 * @since 0.2.0
	 * @uses WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();

		$this->json['defaultFont']  = $this->default_font;
		$this->json['excludeFonts'] = $this->exclude_fonts;
		$this->json['tags']         = $this->tags;
		$this->json['value']        = $this->value();
	}

	/**
	 * Render the control's content.
	 *
	 * @since 0.2.0
	 */
	public function render_content() {}
}
