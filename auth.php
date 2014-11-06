<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Another 2-Factor Authentication mehthod to use Google Authenticator time based tokens
 *
 * @package auth_a2fa
 * @author Sam Battat
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');
require_once('GoogleAuthenticator.php');

/**
 * Plugin for 2 factor auth.
 */
class auth_plugin_a2fa extends auth_plugin_base {

    /**
     * Constructor.
     */
    function auth_plugin_a2fa() {
        $this->authtype = 'a2fa';
        $this->config = get_config('auth/a2fa');
    }

    /**
     * Returns true if the username and password work and the token is valid
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */
    function user_login ($username, $password) {
        global $CFG, $DB, $USER;
	$token = required_param('token', PARAM_TEXT);
	
	if ($user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id))) {
		$valid_login = validate_internal_user_password($user, $password);
		if($valid_login && $user->auth == 'a2fa' && !empty($token)){
			$field = $DB->get_record('user_info_field', array('shortname'=>'a2fasecret'));
			$fid = $field->id;
			$uid = $user->id;
			
			$ga = new PHPGangsta_GoogleAuthenticator();
			$secret = $DB->get_record('user_info_data', array('fieldid'=>$fid, 'userid'=>$uid));
			if(empty($secret->data)){
				redirect($CFG->wwwroot.'/auth/a2fa/error.php');
				return false;
			}
			else{
				$checkResult = $ga->verifyCode($secret->data, $token, 2);
				if($checkResult){
					return true;
				}
				else{
					return false;
				}
			}
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
    }

    function loginpage_hook() {
		
    }

    /**
     * Updates the user's password.
     *
     * called when the user password is updated.
     *
     * @param  object  $user        User table object
     * @param  string  $newpassword Plaintext password
     * @return boolean result
     *
     */
    function user_update_password($user, $newpassword) {
        $user = get_complete_user_data('id', $user->id);
        // This will also update the stored hash to the latest algorithm
        // if the existing hash is using an out-of-date algorithm (or the
        // legacy md5 algorithm).
        return update_internal_user_password($user, $newpassword);
    }

    function prevent_local_passwords() {
        return false;
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    function is_internal() {
        return true;
    }

    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    function can_change_password() {
        return true;
    }

    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return moodle_url
     */
    function change_password_url() {
        return null;
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return bool
     */
    function can_reset_password() {
        return true;
    }

    /**
     * Returns true if plugin can be manually set.
     *
     * @return bool
     */
    function can_be_manually_set() {
        return true;
    }

    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param array $page An object containing all the data for this page.
     */
    function config_form($config, $err, $user_fields) {
        include "config.html";
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     */
    function process_config($config) {
        return true;
    }

}


