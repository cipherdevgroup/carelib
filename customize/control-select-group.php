<?php
/**
 * The group select customize control extends the WP_Customize_Control class. This class allows
 * developers to create a `<select>` form field with the `<optgroup>` elements mixed in. They
 * can also utilize regular `<option>` choices.
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
class CareLib_Customize_Control_Select_Group extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since  0.2.0
	 * @access public
	 * @var    string
	 */
	public $type = 'select-group';

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_script( 'hybrid-customize-controls' );
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

		$choices = $group = array();

		foreach ( $this->choices as $choice => $maybe_group ) {

			if ( is_array( $maybe_group ) )
				$group[ $choice ] = $maybe_group;
			else
				$choices[ $choice ] = $maybe_group;
		}

		$this->json['choices'] = $choices;
		$this->json['group']   = $group;
		$this->json['link']    = $this->get_link();
		$this->json['value']   = $this->value();
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
		<# if ( ! data.choices && ! data.group ) {
			return;
		} #>

		<label>
			<# if ( data.label ) { #>
				<span class="customize-control-title">{{ data.label }}</span>
			<# } #>

			<# if ( data.description ) { #>
				<span class="description customize-control-description">{{{ data.description }}}</span>
			<# } #>

			<select {{{ data.link }}}>

				<# _.each( data.choices, function( label, choice ) { #>

					<option value="{{ choice }}" <# if ( choice === data.value ) { #> selected="selected" <# } #>>{{ label }}</option>

				<# } ) #>

				<# _.each( data.group, function( group ) { #>

					<optgroup label="{{ group.label }}">

						<# _.each( group.choices, function( label, choice ) { #>

							<option value="{{ choice }}" <# if ( choice === data.value ) { #> selected="selected" <# } #>>{{ label }}</option>

						<# } ) #>

					</optgroup>
				<# } ) #>
			</select>
		</label>
		<?php
	}
}
