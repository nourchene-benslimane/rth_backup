<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Change Status Page
#
# $RCSfile: requirement_change_status_page.php,v $  $Revision: 1.3 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$rows = array();

# if submit from requirements_page.php
if( isset($_POST['submit_update']) ) {

	foreach($_POST as $key => $value) {

		$exploded_key = explode("_", $key);
		//print"$exploded_key<br>";

		if( $exploded_key[0]=="row" ) {
			$rows[] = $exploded_key[1];
		}
	}

	if( empty($rows) ) {
		error_report_show("requirement_page.php", NO_REQ_SELECTED);
	}
}

# if submit from this page
if( isset($_POST['submit_status']) ) {

	foreach(explode(":", $_POST['req_ids']) as $value) {

		requirement_version_table_set_field($value, REQ_VERS_STATUS, $_POST['req_status']);
	}

	html_print_operation_successful( 'build_page', "requirement_page.php" );
}

$req_ids = implode(":", $rows);

$page                   = basename(__FILE__);
$project_properties     = session_get_project_properties();
$project_name           = $project_properties['project_name'];
$project_id				= $project_properties['project_id'];

html_window_title();
html_print_body();
html_page_title($project_name ." - ". lang_get('req_update_status_page'));
html_page_header( $db, $project_name );
html_print_menu();
requirement_menu_print($page);

print"<br>". NEWLINE;

print"<div align=center>". NEWLINE;

print"<form method=post action=requirement_change_status_page.php>". NEWLINE;
print"<input type='hidden' name=req_ids value='$req_ids'>";

print"<table class=width50>". NEWLINE;
print"<tr>". NEWLINE;
print"<td>". NEWLINE;

print"<table class=inner>". NEWLINE;

# FORM TITLE
print"<tr>". NEWLINE;
print"<td class='form-data-l'><h4>". lang_get('update_status') ."</h4></td>". NEWLINE;
print"</tr>". NEWLINE;

# STATUS
print"<tr>". NEWLINE;
print"<td class='form-data-c'>";

print"<select name=req_status size=1>". NEWLINE;
	$list_box = requirement_get_statuses();

	html_print_list_box_from_array(	$list_box );
print"</select>". NEWLINE;

print"</td>". NEWLINE;
print"</tr>". NEWLINE;

# SUBMIT BUTTON
print"<tr>". NEWLINE;
print"<td class='form-data-c'><input type='submit' name=submit_status value='". lang_get('update') ."'></td>". NEWLINE;
print"</tr>". NEWLINE;

print"</table>". NEWLINE;

print"</td>". NEWLINE;
print"</tr>". NEWLINE;
print"</table>". NEWLINE;

print"</form>". NEWLINE;

print"</div>". NEWLINE;

html_print_footer();

# ---------------------------------------------------------------------
# $Log: requirement_change_status_page.php,v $
# Revision 1.3  2006/08/05 22:08:37  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:37:48  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>