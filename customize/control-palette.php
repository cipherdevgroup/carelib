<?php
/**
 * Customize control class to handle color palettes.
 *
 * @package    Hybrid
 * @subpackage Classes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Theme Layout customize control class.
 *
 * @since  3.0.0
 * @access public
 */
class CareLib_Customize_Control_Palette extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'palette';

	/**
	 * The default customizer section this control is attached to.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    string
	 */
	public $section = 'colors';

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_script( 'hybrid-customize-controls' );
		wp_enqueue_style(  'hybrid-customize-controls' );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return void
	 */
	public function to_json() {
		parent::to_json();

		// Make sure the colors have a hash.
		foreach ( $this->choices as $choice => $value )
			$this->choices[ $choice ]['colors'] = array_map( 'maybe_hash_hex_color', $value['colors'] );

		$this->json['choices'] = $this->choices;
		$this->json['link']    = $this->get_link();
		$this->json['value']   = $this->value();
		$this->json['id']      = $this->id;
	}

	/**
	 * Underscore JS template to handle the control's output.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return void
	 */
	public function content_template() { ?>

		<# if ( ! data.choices ) {
			return;
		} #>

		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>

		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<# _.each( data.choices, function( palette, choice ) { #>
			<label>
				<input type="radio" value="{{ choice }}" name="_customize-{{ data.type }}-{{ data.id }}" {{{ data.link }}} <# if ( choice === data.value ) { #> checked="checked" <# } #> />

				<span class="palette-label">{{ palette.label }}</span>

				<div class="palette-block">

					<# _.each( palette.colors, function( color ) { #>
						<span class="palette-color" style="background-color: {{ color }}">&nbsp;</span>
					<# } ) #>

				</div>
			</label>
		<# } ) #>
	<?php }
}
