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
# $RCSfile: project_manage_reqareacovered_page.php,v $ $Revision: 1.4 $
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
$add_action_page		= 'project_add_reqareacovered_action.php';
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


$order_by 		= REQ_AREA_COVERAGE;
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

html_project_manage_menu();
html_project_manage_reqs_menu();


error_report_check( $_GET );

$project_details = project_get_details( $selected_project_id );
print"<div align=center>". NEWLINE;

print"<br>". NEWLINE;

if( !empty( $project_details ) ) {

	$project_id						= $project_details[PROJ_ID];
	$project_name					= $project_details[PROJ_NAME];
	$project_status					= $project_details[PROJ_STATUS];
	$project_description			= $project_details[PROJ_DESCRIPTION];

	####################################################################################
	# Required Area Covered
	# ----------------------------------------------------------------------------------

	# ----------------------------------------------------------------------------------
	# Required Area Covered Form
	# ----------------------------------------------------------------------------------
	if( $project_manager ) {
		print"<form method=post action=$add_action_page>". NEWLINE;
		print"<span class='required'>*</span><span class='print'>" . lang_get('must_complete_field') . "</span>". NEWLINE;
		print"<table class='width70'>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td>". NEWLINE;
		print"<table class=inner>". NEWLINE;
		print"<tr>". NEWLINE;
		print"<td class=form-header-l>".lang_get('add_req_area_covered')."</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"<tr>". NEWLINE;
  	 	print"<td class='form-lbl-c'>". lang_get('req_area_covered') ." <span class='required'>*</span>". NEWLINE;
   		print"<input type=text size=60 maxlength=50 name='req_area_covered_required' value='".session_validate_form_get_field( 'req_area_covered_required' )."'>". NEWLINE;
		print"&nbsp;<input type=submit name='new_area_tested' value='".lang_get("add")."'>";
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</td>". NEWLINE;
		print"</tr>". NEWLINE;
		print"</table>". NEWLINE;
		print"</form>". NEWLINE;
	}

	# ----------------------------------------------------------------------------------
	# Required Area Covered	 Table
	# ----------------------------------------------------------------------------------
	print"<br>". NEWLINE;
    print"<form method=post action='$page?order_by=$order_by&amp;order_dir=$order_dir'>". NEWLINE;
	print"<table class=hide70>". NEWLINE;
	print"<tr>". NEWLINE;
	print"<td>". NEWLINE;
	$rows_req_areas_covered = project_get_req_areas_covered($selected_project_id, $order_by, $order_dir, $page_number);
	print"</td>". NEWLINE;
	print"</tr>". NEWLINE;
	print"</table>". NEWLINE;
	print"</form>". NEWLINE;

	if( $rows_req_areas_covered ) {
		print"<table id='sortabletable' class='sortable' rules=cols>". NEWLINE;
		print"<thead>".NEWLINE;
		print"<tr>". NEWLINE;
		html_tbl_print_header( lang_get('req_area_covered') );
		if( $project_manager ) {
			html_tbl_print_header_not_sortable( lang_get('edit') );
			html_tbl_print_header_not_sortable( lang_get('delete') );
		}
		print"\n</tr>". NEWLINE;
		print"</thead>".NEWLINE;
		print"<tbody>".NEWLINE;

		foreach($rows_req_areas_covered as $row_req_area_covered) {
			$id		= $row_req_area_covered[REQ_AREA_COVERAGE_ID];
			$name 	= $row_req_area_covered[REQ_AREA_COVERAGE];

			#$row_style = html_tbl_alternate_bgcolor($row_style);
			#print"<tr class='$row_style'>". NEWLINE;

			print"<tr>". NEWLINE;
			print"<td>$name</td>". NEWLINE;
			if( $project_manager ) {
				print"<td><a href='project_edit_reqareacovered_page.php?req_area_covered_id=$id'>".lang_get("edit")."</a></td>". NEWLINE;
				print"<td>". NEWLINE;
				print"<form name='remove_req_area_covered' method=post action='$delete_page'>". NEWLINE;
				print"<input type='submit' name='delete_req_area_covered' value='". lang_get( 'delete' ) ."' class='page-numbers'>". NEWLINE;
				print"<input type='hidden' name='r_page' value='$redirect_url#req_area_covered'>". NEWLINE;
				print"<input type='hidden' name='f' value='remove_req_area_covered_from_project'>". NEWLINE;
				print"<input type='hidden' name='id' value='$id'>". NEWLINE;
				print"<input type='hidden' name='project_id' value='$selected_project_id'>". NEWLINE;
				print"<input type='hidden' name='msg' value='". DEL_REQ_AREA_FROM_PROJECT ."'>". NEWLINE;
				print"</form>". NEWLINE;
				print"</td>". NEWLINE;
			}
			print"</tr>". NEWLINE;
		}
		print"</tbody>".NEWLINE;
		print"</table>". NEWLINE;

	} else {

		html_no_records_found_message( lang_get('no_areas_covered') );
	}
}

print"</div>". NEWLINE;

html_print_footer();

# ------------------------------------
# $Log: project_manage_reqareacovered_page.php,v $
# Revision 1.4  2008/01/22 07:58:02  cryobean
# made the table sortable
#
# Revision 1.3  2006/08/05 22:08:24  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.2  2006/02/24 11:37:48  gth2
# update to div - class=div-c not working in firefox - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------

?>