<?php

/*
        Groupimage to label text [v1.2]
      (c) Copyright 2013-2016 by Inferno
 
      @author    : Inferno (http://www.Inferno24.pl)
      @contact   : inferno.piotr@gmail.com
      @date      : 03-02-2016
      @update    : 17-11-2017

*/

if(!defined("IN_MYBB")){
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}
$plugins->add_hook("postbit", "rank_postbit");
$plugins->add_hook("member_profile_end", "rank_profile");
$plugins->add_hook("usercp_end", "rank_usercp");
$plugins->add_hook("memberlist_user", "rank_memberlist");

function labelrank_info(){
	global $lang;
	$lang->load("labelrank_group");
	return array(
		'name'			=> 'Groupimage to label text',
		'description'	=> $lang->agi_descr,
		'website'		=> 'http://www.inferno24.pl',
		'author'		=> 'Inferno',
		'authorsite'	=> 'http://www.Inferno24.pl',
		'version'		=> '1.2',
		'guid'			=> 'bab9eaae39210fd31d6c31c4fe4c9baf',
		'codename'		=> 'groupimage_to_labeltext'
	);
}
function labelrank_activate(){
    global $db,$lang;
	$lang->load("labelrank_group");
    $group = array(
        "gid"            => "NULL",
        "title"          => "Group Label",
        "name"           => "labelrank_group",
        "description"    => $lang->setting_description,
        "disporder"      => "1",
        "isdefault"      => "0",
    );
    
    $db->insert_query("settinggroups", $group);
    $gid = $db->insert_id();
    
    
    $setting_1 = array(
        "sid"            => "NULL",
        "name"           => "labelrank_postbit",
        "title"          => $lang->postbit_title,
        "description"    => $lang->postbit_descr,
        "optionscode"    => "yesno",
        "value"          => 'yes',
        "disporder"      => '1',
        "gid"            => intval($gid),
    );

    $db->insert_query("settings", $setting_1);

    $setting_2 = array(
        "sid"            => "NULL",
        "name"           => "labelrank_profile",
        "title"          => $lang->profile_title,
        "description"    => $lang->profile_descr,
        "optionscode"    => "yesno",
        "value"          => 'yes',
        "disporder"      => '1',
        "gid"            => intval($gid),
    );

    $db->insert_query("settings", $setting_2);

    $setting_3 = array(
        "sid"            => "NULL",
        "name"           => "labelrank_usercp",
        "title"          => $lang->usercp_title,
        "description"    => $lang->usercp_descr,
        "optionscode"    => "yesno",
        "value"          => 'yes',
        "disporder"      => '1',
        "gid"            => intval($gid),
    );

    $db->insert_query("settings", $setting_3);

    $setting_4 = array(
        "sid"            => "NULL",
        "name"           => "labelrank_memberlist",
        "title"          => $lang->memberlist_title,
        "description"    => $lang->memberlist_descr,
        "optionscode"    => "yesno",
        "value"          => 'yes',
        "disporder"      => '1',
        "gid"            => intval($gid),
    );

    $db->insert_query("settings", $setting_4);

    rebuild_settings();

    require_once MYBB_ROOT."/inc/adminfunctions_templates.php";

	find_replace_templatesets(
		"memberlist_user",
		"#" . preg_quote('{$usergroup[\'groupimage\']}') . "#i",
		'{$group_label}<br />'
	);
}
function labelrank_deactivate(){
    global $db;

	$db->delete_query("settinggroups", "name=\"labelrank_group\"");
	$db->delete_query("settings", "name LIKE \"labelrank%\"");

	require_once MYBB_ROOT."/inc/adminfunctions_templates.php";

	find_replace_templatesets(
		"memberlist_user",
		"#" . preg_quote('{$group_label}<br />') . "#i",
		'{$usergroup[\'groupimage\']}'
	);
    rebuild_settings();
} 

function rank_postbit(&$post){	
	global $mybb;

	if($mybb->settings['labelrank_postbit']){
		$groupname = getgroupname($post['usergroup']);
		$post['groupimage'] = "<div class=\"profile-rank\"><span class=\"gid-".$post['usergroup']."\">".$groupname."</span></div>";
	}
	
}
function rank_profile(){	
	global $mybb, $memprofile, $groupimage;

	if($mybb->settings['labelrank_profile']){
		$groupname = getgroupname($memprofile['usergroup']);
		$groupimage = "<div class=\"profile-rank\"><span class=\"gid-".$memprofile['usergroup']."\">".$groupname."</span></div>";
	}
}

function rank_usercp(){
	global $mybb, $user, $usergroup;

	if($mybb->settings['labelrank_usercp']){
		$groupname = getgroupname($mybb->user['usergroup']);
		$usergroup = "<div class=\"profile-rank\"><span class=\"gid-".$mybb->user['usergroup']."\">".$groupname."</span></div>";
	}
}

function rank_memberlist(){
	global $mybb, $user, $group_label;
	if($mybb->settings['labelrank_memberlist']){
		$groupname = getgroupname($user['usergroup']);
		eval("\$group_label = '<div class=\"profile-rank\"><span class=\"gid-".$user['usergroup']."\">".$groupname."</span></div>';");
	}
}

function getgroupname($groupid){
	global $cache;

	$usergroups = $cache->read("usergroups");
	$groupname=$usergroups[$groupid]['usertitle'];
	return $groupname;
}
?>