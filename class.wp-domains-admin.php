<?php

/**
 * Main WP_Domains_Admin class.
 *
 * Page settings for WP Domains plugin.
 */
class WP_Domains_Admin {

	/**
	 * Constructor.
	 *	
	 * Hooks all of the URL replacement functionality.
	 *
	 * @access public
	 */
	public function __construct() {
		// Actions
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'admin_menu',     array( $this, 'admin_menu' ) );
		add_action( 'admin_init',     array( $this, 'admin_init' ) );
	}

	/**
	 * Initializes WP_Domains_Admin object
	 * 
	 * @return object An instance of WP_Domains_Admin object.
	 */
	public static function init() {
        return new WP_Domains_Admin();
	}

	/**
	 * Load the translation file for current language
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wp-domains', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Add sub menu page to the Settings menu
	 */
	public function admin_menu() {
		add_options_page(
			__( 'Domains', 'wp-domains' ),
			__( 'Domains', 'wp-domains' ),
			'manage_options',
			'wp-domains',
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Register fields and settings to the settings page.
	 */
	public function admin_init() {
		add_settings_section(
			'wp_domains_settings',
			'',
			'__return_false',
			'wp-domains'
		);

		register_setting(
			'wp-domains',
			'adminurl'
		);

		add_settings_field(
			'wp_domains_adminurl',
			__( 'Admin Address (URL)' , 'wp-domains' ),
			array( $this, 'textbox_url_render_callback' ),
			'wp-domains',
			'wp_domains_settings',
			array( 'adminurl', 'WP_ADMINURL' )
		);
	}

	/**
	 * Output field with the desired inputs as part of the larger form
	 */
	public function textbox_url_render_callback( $args ) {
		$field    = $args[0];
		$value    = get_option( $args[0] );
		$disabled = defined( $args[1] ) ? 'disabled' : '';
		
		echo "<input name=\"$field\" type=\"url\" id=\"$field\" value=\"$value\"";
		echo " class=\"regular-text code $disabled\" $disabled />";
	}

	/**
	 * Output previously registered settings page
	 */
	public function settings_page() {
		?>
		<div class="wrap">
			<h1><?php _e( 'Domains', 'wp-domains' ); ?></h1>
			<form method="post" action="options.php" novalidate="novalidate">
	            <?php settings_fields( 'wp-domains' ); ?>
	            <?php do_settings_sections( 'wp-domains' ); ?>
	            <?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
