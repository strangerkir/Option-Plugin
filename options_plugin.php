<?php
/*
Plugin Name: Options Plugin
Description: Just adds two options pages in admin. Nothing more.
Author: Kir
Version: 1.0
*/

if( ! defined('ABSPATH' ) ){
    die('No.');
}

//Registers plugins options page
function op_add_options_page(){
    add_settings_section( 'op_main_section', 'Options', '', 'op_options_page' );

}

//Outputs plugins options page content
function op_show_options_page_content(){
    $options = get_option( 'op_options' );
	?>
	<div class="wrap">
		<h2>Options page</h2>
		<form method="post" action="">
            <input type="hidden" name="action" value="op_form_processing">
            <?php op_fill_checkbox( $options ); ?>
            <br>
            <?php op_fill_select( $options ); ?>
            <br>
            <?php op_fill_input( $options ); ?>
            <?php submit_button('Save' ); ?>
        </form>
	</div>

	<?php
}

//Outputs plugins submenu page content
function op_show_subpage_content(){
    ?>
    <div class="wrap">
        <h2>Options page(Settings API)</h2>
        <form method="post" action="options.php">
            <?php
                settings_fields( 'op_main_settings' );
                do_settings_sections( 'options_plugin_submenu' );
                submit_button( 'Click me!' );
            ?>
        </form>

    </div>
    <?php
}

//Form data processing without settings api
function op_process_form(){
    if( isset( $_POST['select_value'] ) && isset ( $_POST['text_input'] ) ){
	    $clean_options['checkbox_value']    = isset( $_POST['checkbox_value'] ) ? 1 : 0;
	    $clean_options['select_value']      = filter_var( $_POST['select_value'], FILTER_SANITIZE_STRING );
	    $clean_options['text_input']        = filter_var( $_POST['text_input'], FILTER_SANITIZE_STRING );
	    update_option( 'op_options', $clean_options );
    }
}

//Adds settings section and fields
function op_register_options(){
    add_settings_section( 'op_main_section', 'Main', '', 'options_plugin_submenu');
	add_settings_field( 'op_checkbox', 'Checkbox', 'op_fill_checkbox', 'options_plugin_submenu', 'op_main_section' );
	add_settings_field( 'op_select', 'Select', 'op_fill_select', 'options_plugin_submenu', 'op_main_section' );
	add_settings_field( 'op_input', 'Text Input', 'op_fill_input', 'options_plugin_submenu', 'op_main_section' );
	register_setting( 'op_main_settings', 'checkbox_value' );
	register_setting('op_main_settings', 'select_value' );
	register_setting( 'op_main_settings', 'text_input' );
	//var_dump( $_POST );
	//wp_die();

}

//Fills checkbox on options page
function op_fill_checkbox(){
	$op_options = get_option( 'op_options' );
    ?>
        <input name="checkbox_value" type="checkbox" <?php checked( $op_options['checkbox_value'] ) ?> value="1">
    <?php
}

//Fills select on options page
function op_fill_select(){
    $op_options = get_option( 'op_options' );
    ?>
    <select name="select_value" >
        <option value="default" <?php selected( $op_options['select_value'], 'default_value' ); ?> > Default</option>
        <option value="option1" <?php selected( $op_options['select_value'], 'option1'); ?> > Option1 </option>
        <option value="option2" <?php selected( $op_options['select_value'], 'option2'); ?> > Option2 </option>
    </select>
    <?php
}

//Fills text input on options page
function op_fill_input(){
	$op_options = get_option( 'op_options' );
    ?>
        <input type="text" name="text_input" value="<?php echo $op_options['text_input'] ?>" >
    <?php
}

//Adds plugins page in WP admin menu
function register_op_menu_pages(){
	add_menu_page( 'Options Plugin', 'Options Plugin', 'manage_options', 'options_plugin', 'op_show_options_page_content' );
    add_submenu_page( 'options_plugin', 'Settings Api Page', 'Settings Api Page', 'manage_options', 'options_plugin_submenu', 'op_show_subpage_content');
}

//Adds plugins options in DB. Will be executed on plugin activation.
function op_register_actions(){
	add_option( 'op_options', array(
											'checkbox_value' => 0,
											'select_value' => 'default',
											'text_input'   => 'string'
									)
	);
}

//Removes plugins options from DB. Will be executed on plugin uninstalling.
function op_uninstall_actions(){
	delete_option('op_options' );
}

//Hooks
register_activation_hook(__FILE__, 'op_register_actions' );
register_uninstall_hook( __FILE__, 'op_uninstall_actions' );
add_action( 'admin_init', 'op_process_form' );
//add_action( 'admin_init', 'op_add_options_page' );
add_action( 'admin_init', 'op_register_options' );
add_action( 'admin_menu', 'register_op_menu_pages' );
add_action( 'admin_post_op_form_processing', 'op_process_form' );
