<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ---------------------------------------------------------------------
# User Edit My Account Page
#
# $RCSfile: user_edit_my_account_page.php,v $  $Revision: 1.1.1.1 $
# ---------------------------------------------------------------------

include"./api/include_api.php";
auth_authenticate_user();

$page                   = basename(__FILE__);
$action_page			= "user_edit_my_account_action.php";

$s_project_properties   = session_get_project_properties();
$project_name           = $s_project_properties['project_name'];
$project_id 			= $s_project_properties['project_id'];

$s_user_properties 	= session_get_user_properties();
$user_id 			= $s_user_properties['user_id'];

$selected_user_id = $user_id;

require_once("user_edit_page.php");

# ---------------------------------------------------------------------
# $Log: user_edit_my_account_page.php,v $
# Revision 1.1.1.1  2005/11/30 23:00:59  gth2
# importing initial version - gth
#
# ---------------------------------------------------------------------

?>
