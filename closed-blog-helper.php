<?php
/*
Plugin Name: Closed blog helper
Plugin URI: http://wordpress.org/extend/plugins/closed-blog-helper
Description: Instead of a shortcode [closed-blog-helper] shows (customizable) message for the visitor according to their access rights to the blog.
Version: 1.3.2
Author: Zaantar
Author URI: http://zaantar.eu
License: GPL2
*/

/*
    Copyright 2010 Zaantar (email: zaantar@gmail.com)

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


/* ************************************************************************* *\
    i18N
\* ************************************************************************* */


define( 'CBH_TEXTDOMAIN', 'closed-blog-helper' );

add_action( 'init', 'cbh_load_textdomain' );

function cbh_load_textdomain() {
	$plugin_dir = basename( dirname(__FILE__) );
	load_plugin_textdomain( CBH_TEXTDOMAIN, false, $plugin_dir.'/languages' );
}


/* ************************************************************************* *\
    SHORTCODE HANDLER
\* ************************************************************************* */

add_shortcode( 'closed-blog-helper', 'cbh_shortcode_handler' );

function cbh_shortcode_handler() {
	
	$uid = get_current_user_id();
	if( $uid != 0 ) {
		$user = wp_get_current_user();
	}
	
	if( is_super_admin( $uid ) ) {
		$option = CBH_OPT_SUPERADMIN;
	} else if( current_user_can( 'manage_options' ) ) {
		$option = CBH_OPT_OWNER;
	} else if( $uid == 0 ) {
		$option = CBH_OPT_VISITOR;
	} else if( current_user_can( 'read' ) ) {
		$option = CBH_OPT_REGISTERED;
	} else {
		$option = CBH_OPT_LOGGED_IN;
	}
	
	$output = '<span class="closed-blog-helper">'.cbh_get_message( $option ).'</span>';
	return $output;
}

/* ************************************************************************* *\
    OPTIONS PAGE
\* ************************************************************************* */

define( 'CBH_OPT_SUPERADMIN', 'cbh_superadmin_message' );
define( 'CBH_OPT_OWNER', 'cbh_owner_message' );
define( 'CBH_OPT_REGISTERED', 'cbh_registered_message' );
define( 'CBH_OPT_LOGGED_IN', 'cbh_logged_in_message' );
define( 'CBH_OPT_VISITOR', 'cbh_visitor_message' );

/* define( 'CBH_OPT_SUPERADMIN_DEF', 'Jste superadministrátor. Můžete všechno a všude.' );
define( 'CBH_OPT_OWNER_DEF', 'Jste vlastníkem tohoto blogu.' );
define( 'CBH_OPT_REGISTERED_DEF', 'Jste přihlášen/a jako %USER_DISPLAY_NAME% (přihlašovacím jménem <em>%USER_LOGIN%</em>) a máte dostatečná práva k přístupu na blog. Můžete <a href="%HOME_URL%">pokračovat na hlavní stranu</a>.' );
define( 'CBH_OPT_LOGGED_IN_DEF', 'Jste přihlášen/a jako %USER_DISPLAY_NAME% (přihlašovacím jménem <em>%USER_LOGIN%</em>), ale vypadá to, že <strong>na tento blog ještě nemáte povolený přístup</strong>. Pokud to chcete změnit, můžete <a href="mailto:%ADMIN_MAIL%">napsat</a> vlastníkovi blogu.' );
define( 'CBH_OPT_VISITOR_DEF', 'Nejste přihlášen/a! <a href="%LOGIN_URL%">Přihlašte se</a> pod svým uživatelským jménem anebo si <a href="mailto:%ADMIN_MAIL%">napište</a> o registraci. Pokud jste zapomněli heslo, klikněte <a href="wp-login.php?action=lostpassword">sem</a>.' );*/

define( 'CBH_OPT_SUPERADMIN_DEF', 'You are the superadmin. You can do anything and everything.' );
define( 'CBH_OPT_OWNER_DEF', 'You are the owner of this blog.' );
define( 'CBH_OPT_REGISTERED_DEF', 'You are logged in as %USER_DISPLAY_NAME% (login name <em>%USER_LOGIN%</em>) and you have enough rights to access the blog\'s content. You can <a href="%HOME_URL%">continue to the homepage</a>.' );
define( 'CBH_OPT_LOGGED_IN_DEF', 'You are logged in as %USER_DISPLAY_NAME% (login name <em>%USER_LOGIN%</em>), but it seems that <strong>you are not allowed to access this blog yet</strong>. If you want to change that, you can <a href="mailto:%ADMIN_MAIL%">send an e-mail</a> to the blog owner.' );
define( 'CBH_OPT_VISITOR_DEF', 'You are not logged in! <a href="%LOGIN_URL%">Log in</a> with your user account or <a href="mailto:%ADMIN_MAIL%">ask</a> for registration. If you have forgotten your password, please go <a href="wp-login.php?action=lostpassword">here</a>.' );



define( 'CBH_KW_USER_LOGIN', '%USER_LOGIN%' );
define( 'CBH_KW_USER_DISPLAY_NAME', '%USER_DISPLAY_NAME%' );
define( 'CBH_KW_HOME_URL', '%HOME_URL%' );
define( 'CBH_KW_ADMIN_MAIL', '%ADMIN_MAIL%' );
define( 'CBH_KW_SUPERADMIN_MAIL', '%SUPERADMIN_MAIL%' );
define( 'CBH_KW_LOGIN_URL', '%LOGIN_URL%' );

define( 'CBH_OPTIONS_PAGE', 'closed-blog-helper-options' );

add_action( 'admin_menu','cbh_add_admin_menu' );

function cbh_add_admin_menu() {
	add_submenu_page('options-general.php', __( 'Closed Blog Helper Settings', CBH_TEXTDOMAIN ), __( 'Closed Blog Helper', CBH_TEXTDOMAIN ), 'manage_options', CBH_OPTIONS_PAGE, 'cbh_options_page');
}

function cbh_options_page() {
    if( isset($_REQUEST['action']) ) {
        $action = $_REQUEST['action'];
    } else {
        $action = 'default';
    }
    
    switch( $action ) {
    case 'update_options':
    	cbh_options_update(); //TODO indikace chyb
    	cbh_options_page_default();
    	break;
    default:
    	cbh_options_page_default();
    	break;
    }
}


/*
<?php _e( '', CBH_TEXTDOMAIN ); ?>
*/

function cbh_options_page_default() {
	?>
	<div class="wrap">
		<h2><?php _e( 'Closed Blog Helper settings', CBH_TEXTDOMAIN ); ?></h2>
		<h3><?php _e( 'Information', CBH_TEXTDOMAIN ); ?></h3>
		<p>
			<?php printf( __( 'Here you can specify the messages that will be printed instead of %s according to the situation.', CBH_TEXTDOMAIN ), '<code>[closed-blog-helper]</code>' ); ?>
		</p>
		<p>
			<?php _e( 'You can use HTML syntax and following keywords:', CBH_TEXTDOMAIN ); ?>
		</p>
		<ol>
			<li><code><?php echo( CBH_KW_USER_LOGIN ); ?></code>: <?php _e( 'user login name', CBH_TEXTDOMAIN ); ?></li>
			<li><code><?php echo( CBH_KW_USER_DISPLAY_NAME ); ?></code>: <?php _e( 'displayed user\'s nickname', CBH_TEXTDOMAIN ); ?></li>
			<li><code><?php echo( CBH_KW_HOME_URL ); ?></code>: <?php _e( 'main blog page url', CBH_TEXTDOMAIN ); ?></li>
			<li><code><?php echo( CBH_KW_LOGIN_URL ); ?></code>: <?php _e( 'login page url', CBH_TEXTDOMAIN ); ?></li>
			<li><code><?php echo( CBH_KW_ADMIN_MAIL ); ?></code>: <?php _e( 'administrator\'s e-mail address', CBH_TEXTDOMAIN ); ?></li>
			<li><code><?php echo( CBH_KW_SUPERADMIN_MAIL ); ?></code>: <?php _e( 'superadministrator\'s e-mail address (works on multisite only)', CBH_TEXTDOMAIN ); ?></li>
		</ol>
		<p>
			<?php printf( __( 'If you\'d like to have more keywords or another functionality implemented, kindly contact the plugin developer via %s', CBH_TEXTDOMAIN ), '<a href="mailto:zaantar@zaantar.eu">zaantar@zaantar.eu</a>'); ?>
		</p>
		<h3><?php _e( 'Settings', CBH_TEXTDOMAIN ); ?></h3>
		<form method="post" ?>">
            <input type="hidden" name="action" value="update_options" />
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e( 'Superadmin', CBH_TEXTDOMAIN ); ?></th>
                    <td>
                        <textarea name="cbh_superadmin" cols="50" rows="5"><?php echo( esc_attr( get_option( CBH_OPT_SUPERADMIN, __( CBH_OPT_SUPERADMIN_DEF, CBH_TEXTDOMAIN ) ) ) ); ?></textarea>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Blog owner / admin', CBH_TEXTDOMAIN ); ?></th>
                    <td>
                        <textarea name="cbh_owner" cols="50" rows="5"><?php echo( esc_attr( get_option( CBH_OPT_OWNER, __( CBH_OPT_OWNER_DEF, CBH_TEXTDOMAIN ) ) ) ); ?></textarea>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Logged in registered user', CBH_TEXTDOMAIN ); ?></th>
                    <td>
                        <textarea name="cbh_registered" cols="50" rows="5"><?php echo( esc_attr( get_option( CBH_OPT_REGISTERED, __( CBH_OPT_REGISTERED_DEF, CBH_TEXTDOMAIN ) ) ) ); ?></textarea>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Logged in user without registration', CBH_TEXTDOMAIN ); ?></th>
                    <td>
                        <textarea name="cbh_logged_in" cols="50" rows="5"><?php echo( esc_attr( get_option( CBH_OPT_LOGGED_IN, __( CBH_OPT_LOGGED_IN_DEF, CBH_TEXTDOMAIN ) ) ) ); ?></textarea>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Visitor', CBH_TEXTDOMAIN ); ?></th>
                    <td>
                        <textarea name="cbh_visitor" cols="50" rows="5"><?php echo( esc_attr( get_option( CBH_OPT_VISITOR, __( CBH_OPT_VISITOR_DEF, CBH_TEXTDOMAIN ) ) ) ); ?></textarea>
                    </td>
                </tr>                            
            </table>
            <p class="submit">
	            <input type="submit" value="<?php _e( 'Save', CBH_TEXTDOMAIN ); ?>" />    
	        </p>        
        </form>		
	</div>
	<?php
}


function cbh_options_update() {
	update_option( CBH_OPT_SUPERADMIN, stripslashes( $_POST['cbh_superadmin'] ) );
	update_option( CBH_OPT_OWNER, stripslashes( $_POST['cbh_owner'] ) );
	update_option( CBH_OPT_REGISTERED, stripslashes( $_POST['cbh_registered'] ) );
	update_option( CBH_OPT_LOGGED_IN, stripslashes( $_POST['cbh_logged_in'] ) );
	update_option( CBH_OPT_VISITOR, stripslashes( $_POST['cbh_visitor'] ) );
}


//define( 'CBH_KW_SUPERADMIN_MAIL', '%SUPERADMIN_MAIL%' );
function cbh_get_message( $option ) {
	$message = get_option( $option, cbh_get_default_message( $option ) );

	$user = wp_get_current_user();
	
	$patterns[0] = '/'.CBH_KW_USER_LOGIN.'/';
	$replacements[0] = $user->user_login; 
	
	$patterns[1] = '/'.CBH_KW_USER_DISPLAY_NAME.'/';
	$replacements[1] = $user->display_name;
	
	$patterns[2] = '/'.CBH_KW_HOME_URL.'/';
	$replacements[2] = home_url();
	
	$patterns[3] = '/'.CBH_KW_ADMIN_MAIL.'/';
	$replacements[3] = get_bloginfo('admin_email');
	
	$patterns[4] = '/'.CBH_KW_LOGIN_URL.'/';
	$replacements[4] = wp_login_url();
	
	$patterns[5] = '/'.CBH_KW_SUPERADMIN_MAIL.'/';
	$replacements[5] = get_site_option('admin_email');

	$message = preg_replace( $patterns, $replacements, $message );
	return $message;
}

function cbh_get_default_message( $option ) {
	switch( $option ) {
		case CBH_OPT_SUPERADMIN:
			return CBH_OPT_SUPERADMIN_DEF;
			break;
		case CBH_OPT_OWNER:
			return CBH_OPT_OWNER_DEF;
			break;
		case CBH_OPT_REGISTERED:
			return CBH_OPT_REGISTERED_DEF;
			break;
		case CBH_OPT_LOGGED_IN:
			return CBH_OPT_LOGGED_IN_DEF;
			break;
		case CBH_OPT_VISITOR:
		default:
			return CBH_OPT_VISITOR_DEF;
			break;
	}
}
?>
