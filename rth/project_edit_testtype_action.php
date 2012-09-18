<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Project Edit Test Type Action
#
# $RCSfile: project_edit_testtype_action.php,v $  $Revision: 1.1.1.1 $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$redirect_page		= 'project_manage_testtype_page.php';
$edit_page			= 'project_edit_testtype_page.php';
$proj_properties	= session_set_properties("project_manage", $_POST);

session_validate_form_set($_POST, $edit_page);

project_edit_test_type(	$_POST['project_id'],
						$_POST['test_type_id'],
						session_validate_form_get_field('testtype_required') );

session_validate_form_reset();


html_print_operation_successful( "edit_testtype_page", $redirect_page );

# ------------------------------------
# $Log: project_edit_testtype_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:57  gth2
# importing initial version - gth
#
# ------------------------------------
?>