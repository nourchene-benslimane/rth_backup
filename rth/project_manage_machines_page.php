<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Manage Project Page
#
# $RCSfile: project_manage_machines_page.php,v $ $Revision: 1.2 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$project_manage_page	= 'project_manage_page.php';
$project_add_page     	= 'project_add_page.php';
$project_edit_page		= 'project_edit_page.php';
$user_manage_page		= 'user_manage_page.php';
$user_add_page			= 'user_add_page.php';
$project_manage_action	= 'project_manage_action.php';
$delete_page			= 'delete_page.php';

$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];

$s_user_properties		= session_get_user_properties();
$user_id				= $s_user_properties['user_id'];

session_set_properties("project_manage", $_GET);
$selected_project_properties 	= session_get_properties("project_manage");
$selected_project_id 			= $selected_project_properties['project_id'];

$project_manager		= user_has_rights( $selected_project_id, $user_id, MANAGER );

$redirect_url			= $page ."?project_id=". $selected_project_id;

$s_user_properties		= session_get_user_properties();
$user_id				= $s_user_properties['user_id'];
$row_style              = '';

$order_by 		= MACH_NAME;
$order_dir		= "ASC";
$page_number	= 1;

util_set_order_by($order_by, $_GET);
util_set_order_dir($order_dir, $_GET);
util_set_page_number($page_number, $_GET);

util_set_order_by($order_by, $_POST);
util_set_order_dir($order_dir, $_POST);
util_set_page_number($page_number, $_POST);


html_window_title();
html_print_body();
html_page_title(project_get_name($selected_project_id) ." - ". lang_get('manage_project_page') );
html_page_header( $db, $project_name );
html_print_menu();
admin_menu_print( $page, $project_id, $user_id );


error_report_check( $_GET );

$project_details = project_get_details( $selected_project_id );
print"<div class=center>". NEWLINE;

html_project_manage_menu();

print"<br>". NEWLINE;

if( !empty( $project_details ) ) {

	$project_id						= $project_details[PROJ_ID];
	$project_name					= $project_details[PROJ_NAME];
	$project_status					= $project_details[PROJ_STATUS];
	$project_description			= $project_details[PROJ_DESCRIPTION];

	####################################################################################
	# Machines
	# ----------------------------------------------------------------------------------

	# ----------------------------------------------------------------------------------
	# Machines Form
	# ----------------------------------------------------------------------------------
	if( $project_manager ) {
		print"<form method=post action='project_add_machine_action.php'>". NEWLINE;
		print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
		print"<table class='width70'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;
		print"<table class=inner>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class=form-header-l>".lang_get('add_machine')."</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;
		print"<table class=hide90>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class='form-lbl-c'>". lang_get('machine_name') ." <span class='required'>*</span>". NEWLINE;
		print"<input type=text size=20 maxlength=20 name='machine_name_required' value='".session_validate_form_get_field( 'machine_name_required' )."'>". NEWLINE;
		print"</td>". NEWLINE;
		print"<td class='form-lbl-c'>". lang_get('machine_location') ." <span class='required'>*</span>". NEWLINE;
		print"<input type=text size=25 maxlength=25 name='machine_location_required' value='".session_validate_form_get_field( 'machine_location_required' )."'>". NEWLINE;
		print"</td>". NEWLINE;
		print"<td class='form-lbl-c'>". lang_get('machine_ip') ." <span class='required'>*</span>". NEWLINE;
		print"<input type=text size=15 maxlength=15 name='machine_ip_required' value='".session_validate_form_get_field( 'machine_ip_required' )."'>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class=center><input type=submit name='new_area_tested' value='".lang_get("add")."'></td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</form>". NEWLINE;
	}

	# ----------------------------------------------------------------------------------
	# Machines Table
	# ----------------------------------------------------------------------------------
	print"<br>". NEWLINE;
    print"<form method=post action='$page?order_by=$order_by&amp;order_dir=$order_dir'>". NEWLINE;
	print"<input type=hidden name=table value=project_manage_machines>". NEWLINE;
	print"<table class=hide70>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
	$rows_machines = project_get_machines($selected_project_id, $order_by, $order_dir, $page_number);
	print"<input type=hidden name='order_dir' value='$order_dir'>";
	print"<input type=hidden name='order_by' value='$order_by'>";
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</form>". NEWLINE;

	if( $rows_machines ) {

		print"<table class='width70' rules='cols'>". NEWLINE;
		print"<tr>". NEWLINE;
		html_tbl_print_header( lang_get('machine_name'), 		MACH_NAME, 		$order_by, $order_dir, "$page?page_number=$page_number" );
		html_tbl_print_header( lang_get('machine_location'),	MACH_LOCATION, 	$order_by, $order_dir, "$page?page_number=$page_number" );
		html_tbl_print_header( lang_get('machine_ip') );
		if( $project_manager ) {
			html_tbl_print_header( lang_get('edit') );
			html_tbl_print_header( lang_get('delete') );
		}
		print"\n</tr>". NEWLINE;

		foreach($rows_machines as $row_machine) {
			$row_machine_id 		= $row_machine[MACH_ID];
			$row_machine_name 		= $row_machine[MACH_NAME];
			$row_machine_location 	= $row_machine[MACH_LOCATION];
			$row_machine_ip			= $row_machine[MACH_IP_ADDRESS];

			$row_style = html_tbl_alternate_bgcolor($row_style);

			print"<tr class='$row_style'>". NEWLINE;
			print"<td>$row_machine_name</td>". NEWLINE;
			print"<td>$row_machine_location</td>". NEWLINE;
			print"<td>$row_machine_ip</td>". NEWLINE;
			if( $project_manager ) {
				print"<td><a href='project_edit_machine_page.php?machine_id=$row_machine_id'>".lang_get("edit")."</a></td>". NEWLINE;
				print"<td>". NEWLINE;
				print"<form name='delete_release' method=post action='$delete_page'>". NEWLINE;
				print"<input type='submit' name='delete_machine' value='". lang_get( 'delete' ) ."' class='page-numbers'>". NEWLINE;
				print"<input type='hidden' name='r_page' value='$redirect_url#machines'>". NEWLINE;
				print"<input type='hidden' name='f' value='remove_machine_from_project'>". NEWLINE;
				print"<input type='hidden' name='id' value='$row_machine_id'>". NEWLINE;
				print"<input type='hidden' name='project_id' value='$selected_project_id'>". NEWLINE;
				print"<input type='hidden' name='msg' value='". DEL_MACHINE_FROM_PROJECT ."'>". NEWLINE;
				print"</form>". NEWLINE;
				print"</td>". NEWLINE;
			}
			print"</tr>". NEWLINE;
		}

		print"</table>". NEWLINE;
		#print"</form>". NEWLINE;

		print"<br>". NEWLINE;
	}

} else {
	html_no_records_found_message( lang_get('no_projects') );
}

print"</div>". NEWLINE;

html_print_footer();

# ------------------------------------
# $Log: project_manage_machines_page.php,v $
# Revision 1.2  2006/08/05 22:08:24  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------

?>