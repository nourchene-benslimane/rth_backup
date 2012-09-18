<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# Requirement Release Assoc Action Page
#
# $RCSfile: requirement_releases_assoc_action.php,v $  $Revision: 1.1.1.1 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id				= $s_project_properties['project_id'];

$s_properties		= session_set_properties("requirements", $_GET);
$s_req_id			= $s_properties['req_id'];
$s_req_version_id	= $s_properties['req_version_id'];

$redirect_page	= 'requirement_detail_page.php';

requirement_edit_assoc_releases($s_req_version_id, $_POST["row_releases"]);

############################################################################
# EMAIL NOTIFICATION
############################################################################
$recipients		= requirement_get_notify_users($project_id, $s_req_id);

requirement_email($project_id, $s_req_id, $recipients, $action="edit_release_assoc");
############################################################################
############################################################################

html_print_operation_successful("req_assoc_releases_page", $redirect_page);

# ---------------------------------------------------------------------
# $Log: requirement_releases_assoc_action.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:58  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------
?>