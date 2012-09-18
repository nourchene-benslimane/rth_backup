<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Release API
#
# $RCSfile: release_api.php,v $ $Revision: 1.1 $
# ------------------------------------

function release_get_releasenames($project_id){
	
	global $db;
    $build_tbl    		= BUILD_TBL;
    $release_tbl		= RELEASE_TBL;
    $testset_tbl		= TS_TBL;
    $testset_build_id	= $testset_tbl .".". BUILD_ID;
    $build_id			= $build_tbl   .".". BUILD_ID;
    $build_release_id	= $build_tbl   .".". RELEASE_ID;
    $release_id			= $release_tbl .".". RELEASE_ID;
    $release_name		= $release_tbl   .".". RELEASE_NAME;
    $f_release_name		= RELEASE_NAME;
    $f_project_id		= $release_tbl .".". PROJECT_ID;
    $arr_value			= array();
    $q = "SELECT DISTINCT($release_name)
    		FROM $build_tbl, $testset_tbl, $release_tbl
    		WHERE $testset_build_id = $build_id 
    		AND $build_release_id = $release_id
    		AND $project_id = $f_project_id" ;
    		
    $rs = & db_query( $db, $q );
    while($row = db_fetch_row( $db, $rs ) ) { 
		array_push($arr_value, $row[$f_release_name]);
    }

    $arr_value[count($arr_value)+1] = "";
    
    return $arr_value;
}




# ------------------------------------
# $Log: release_api.php,v $
# Revision 1.1  2008/07/17 13:54:12  peter_thal
# added new feature: test sets status (overview)
# +fixed some bugs with project_id parameter in testdetail_page references
#
#
# ------------------------------------
?>
