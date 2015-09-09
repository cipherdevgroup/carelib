<?php
/**
 * The multiple select customize control extends the WP_Customize_Control class.
 *
 * This class allows developers to create a `<select>` form field with the
 * `multiple` attribute within the WordPress theme customizer.
 *
 * @package   CareLib
 * @copyright Copyright (c) 2015, WP Site Care, LLC
 * @license   GPL-2.0+
 * @since     0.2.0
 */

/**
 * Multiple select customize control class.
 *
 * @since  0.2.0
 * @access public
 */
class CareLib_Customize_Control_Select_Multiple extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since  0.2.0
	 * @access public
	 * @var    string
	 */
	public $type = 'select-multiple';

	/**
	 * Loads the library scripts/styles.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_script( 'carelib-customize-controls' );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function to_json() {
		parent::to_json();

		$this->json['choices'] = $this->choices;
		$this->json['link']    = $this->get_link();
		$this->json['value']   = (array) $this->value();
		$this->json['id']      = $this->id;
	}

	/**
	 * Underscore JS template to handle the control's output.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function content_template() {
		?>
		<# if ( ! data.choices ) {
			return;
		} #>

		<label>
			<# if ( data.label ) { #>
				<span class="customize-control-title">{{ data.label }}</span>
			<# } #>

			<# if ( data.description ) { #>
				<span class="description customize-control-description">{{{ data.description }}}</span>
			<# } #>

			<select multiple="multiple" {{{ data.link }}}>

				<# _.each( data.choices, function( label, choice ) { #>

					<option value="{{ choice }}" <# if ( -1 !== data.value.indexOf( choice ) ) { #> selected="selected" <# } #>>{{ label }}</option>

				<# } ) #>

			</select>
		</label>
	<?php
	}
}
