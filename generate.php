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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.
/**
* Handle manual badge award.
*
* @package a2fa
* @copyright 2014 Sam Battat
* @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
require_once('../../config.php');
require_once('GoogleAuthenticator.php');

$isadmin = is_siteadmin($USER);

if($isadmin){
	$ga = new PHPGangsta_GoogleAuthenticator();
	$field = $DB->get_record('user_info_field', array('shortname'=>'a2fasecret'));
        $fid = $field->id;

	do{
		$secret = $ga->createSecret();
		$row = $DB->get_records_select('user_info_data', "fieldid = {$fid} AND ".$DB->sql_compare_text('data')." = '$secret'");
	} while(!empty($row));

	echo json_encode(array('status' => 'success', 'secret' => $secret));
}
