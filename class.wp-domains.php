<?php

/**
 * Main WP_Domains class.
 *
 * Use different domains for admin and frontend on Wordpress.
 * All of the magic is hooked upon WP_Domains object initialization.
 */
class WP_Domains {

	/**
	 * Constructor.
	 *	
	 * Hooks all of the URL replacement functionality.
	 *
	 * @access public
	 */
	public function __construct() {
		// Modify URL "where" necessary
		add_filter( 'admin_url',         array( $this, 'replace_url' ), 20 );
		add_filter( 'login_url',         array( $this, 'replace_url' ), 20 );
		add_filter( 'logout_url',        array( $this, 'replace_url' ), 20 );
		add_filter( 'preview_post_link', array( $this, 'replace_url' ), 20 );

		// Modify URL "when" necessary
		add_filter( 'option_siteurl',    array( $this, 'site_url' ), 20 );
		add_filter( 'option_home',       array( $this, 'site_url' ), 20 );

		// adminurl
		add_filter( 'pre_option_adminurl',     array( $this, 'adminurl_pre_option' ) );
		add_filter( 'default_option_adminurl', array( $this, 'adminurl_default_option' ) );
	}

	/**
	 * Initializes WP_Domains object
	 * 
	 * @return object An instance of WP_Domains object.
	 */
	public static function init() {
        return new WP_Domains();
	}

	/**
	 * Filter the adminurl option before it is retrieved.
	 *
	 * If WP_ADMINURL constant is defined it will short-circuit retrieving the
	 * constant value.
	 *
	 * @access public
	 *
	 * @param  string $url     Value to return instead of the option value.
	 *                         Default false to skip it.
	 * @return mixed           The WP_ADMINURL or false.
	 */
	public function adminurl_pre_option( $url ) {
		return defined( 'WP_ADMINURL' ) ? WP_ADMINURL : false;
	}

	/**
	 * Filter the default value for adminurl option.
	 *
	 * If no value has been defined for adminurl, it returns siteurl as default
	 * value.
	 *
	 * @access public
	 *
	 * @param  string $url     The default value to return if the option does
	 *                         not exist in the database.
	 * @return string          The siteurl value or url.
	 */
	public function adminurl_default_option( $url ) {
		return empty( $url ) ? get_option( 'siteurl' ) : $url;
	}

	/**
	 * Replaces the WordPress site URL with admin URL.
	 *
	 * This function returns a string with all occurrences of site URL replaced
	 * by admin URL. If site and admin URL are the same, it returns URL intact.
	 *
	 * @access public
	 *
	 * @param string $url URL to be replaced.
	 * @return string Replaced URL.
	 */
	public function replace_url( $url = '' ) {
		$adminurl = get_option( 'adminurl' );
		$siteurl  = get_option( 'siteurl' );
		
		if ($adminurl == $siteurl) {
			return $url;
		} else {
			return preg_replace( "|$siteurl|", $adminurl, $url );
		}
	}
	
	/**
	 * Filter URL based on current script name.
	 *
	 * Checks current script name, if wp-login then return admin URL otherwise
	 * returns URL intact.
	 *
	 * @access public
	 *
	 * @return string Filtered URL.
	 */
	public function site_url( $url = '' ) {
		if ($_SERVER['SCRIPT_NAME'] == '/wp-login.php') {
			return get_option( 'adminurl', $url );
		}
		return $url;
	}
}
