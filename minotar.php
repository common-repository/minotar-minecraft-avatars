<?php
/*
Plugin Name: Minotar
Plugin URI: http://minotar.net/
Description: Enables Minecraft skins to be used as avatars.
Version: 1.6
Author: <a href="http://axxim.net/">Luke Strickland</a> and <a href="http://edvindev.wordpress.com">Joel Bergroth</a>
License:

    Copyright 2011 Minotar

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    
*/

class Minotar {
	 
	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/
	
	/**
	 * Initializes the plugin by setting filters and administration functions.
	 */
	public function __construct() {
		
		add_filter( 'avatar_defaults', array( $this, 'add_minotar_avatar' ) );
		add_filter( 'get_avatar', array( $this, 'get_minotar_avatar' ), 1, 5 );
		
		
		// BuddyPress
		
		// Include plugin.php in order to be able to use is_plugin_active
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
		// If BuddyPress is enabled and minotar is chosen as avatar
		if( is_plugin_active( 'buddypress/bp-loader.php' && get_option( 'avatar_default' ) == 'minotar' ) ) {
		
			add_filter( 'bp_core_fetch_avatar_no_grav', array( $this, 'bp_core_fetch_avatar_no_grav' ) );
			add_filter( 'bp_core_default_avatar_user', array( $this, 'bp_core_default_avatar_user' ), 10, 2 );
			
		}
		
	} // end constructor
	
	/*--------------------------------------------*
	 * Core Functions
	 *--------------------------------------------*/
	 
	/**
	* BuddyPress support
	*/
	public function bp_core_fetch_avatar_no_grav() {

		return true;
		
	} // end bp_core_fetch_avatar_no_grav
	
	public function bp_core_default_avatar_user( $url, $params ) {

		//$minotar_url = 'http://minotar.net/avatar/'.$username.'/'.$size;
		
		$user_info = get_userdata($params['item_id']);
		
		$minotar_url = 'http://minotar.net/avatar/' . $user_info->user_login;
		
		return $minotar_url;
		
	} // end bp_core_default_avatar_user
	
	/**
	* Apply a filter to the default avatar list and add Minotars
	*/
	public function add_minotar_avatar( $avatar_defaults ) {

		$avatar_defaults['minotar'] = 'Minotar';
	
		return $avatar_defaults;
		
	} // end add_minotar_avatar
	
	/**
	* Apply a filter to the default get_avatar function to add
	* Minotar functionality
	*/
	public function get_minotar_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
	
		if( $default == 'minotar' ) {
	
			//Alternative text
			if ( false === $alt)
				$safe_alt = '';
			else
				$safe_alt = esc_attr( $alt );
		
			//Get username
			if ( is_numeric($id_or_email) ) {
				$id = (int) $id_or_email;
				$user = get_userdata($id);
				if ( $user )
				$username = $user->user_login;
			} elseif ( is_object($id_or_email) ) {

				if ( !empty($id_or_email->user_id) ) {
					$id = (int) $id_or_email->user_id;
					$user = get_userdata($id);
					if ( $user)
						$username = $user->user_login;
				} elseif ( !empty($id_or_email->comment_author) ) {
					$username = $id_or_email->comment_author;
				}
			} else {
				require_once(ABSPATH . WPINC . '/ms-functions.php');
				$user = get_user_by('email', $id_or_email);
				$username = $user->user_login;
			}
		
			$minotar = 'http://minotar.net/avatar/'.$username.'/'.$size.'';
		
			$avatar = "<img alt='".$safe_alt."' src='".$minotar."' class='avatar avatar-".$size." photo' height='".$size."' width='".$size."' />";
		
		}
		
		return $avatar;
	}
	
	/*--------------------------------------------*
	 * Static Functions
	 *--------------------------------------------*/
	
	// This is executed when the plugin is activated
	static function activation() {
	
		update_option( 'avatar_default_before_minotar', get_option( 'avatar_default' ) );
		update_option( 'avatar_default', 'minotar' );
		
	}
	
	// This is executed when the plugin is deactivated
	static function deactivation() {
	
		if( get_option( 'avatar_default_before_minotar' ) and get_option( 'avatar_default' ) == 'minotar' ) {
		
			update_option( 'avatar_default', get_option( 'avatar_default_before_minotar' ) );
			
		} // end if
		
		delete_option( 'avatar_default_before_minotar' );
	}
	
	// This is executed when the user clicks on the uninstall
	// link that calls for the plugin to uninstall itself
	static function uninstall() {
		
		if( get_option( 'avatar_default_before_minotar' ) and get_option( 'avatar_default' ) == 'minotar' ) {
		
			update_option( 'avatar_default', get_option( 'avatar_default_before_minotar' ) );
			
		} // end if
		else if ( get_option( 'avatar_default_before_minotar' ) ) {
		
			delete_option( 'avatar_default_before_minotar' );
			
		} // end elseif
		
	}
	
} // end class


register_activation_hook( __FILE__, array( 'Minotar', 'activation' ) );
register_deactivation_hook( __FILE__, array( 'Minotar', 'deactivation' ) );
register_uninstall_hook( __FILE__, array( 'Minotar', 'uninstall' ) ) ;

new Minotar();
?>
