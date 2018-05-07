<?php

/*
        Groupimage to label text [v2.0]
      (c) Copyright 2013-2016 by Inferno
 
      @author    : Inferno (http://www.github.com/inferno211)
      @contact   : piotr.grencel@mybboard.pl
      @date      : 03-02-2016
      @update    : 07-05-2018

*/

if(!defined("IN_MYBB")){
    die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook("postbit", "rank_postbit");
$plugins->add_hook("member_profile_end", "rank_profile");
$plugins->add_hook("usercp_end", "rank_usercp");
$plugins->add_hook("memberlist_user", "rank_memberlist");
/*$plugins->add_hook("private_read_end", "rank_read_pw");*/

function labelrank_info(){
    global $lang;
    $lang->load("labelrank_group");
    return array(
        'name'          => 'Groupimage to label text',
        'description'   => $lang->agi_descr,
        'website'       => 'http://www.inferno24.pl',
        'author'        => 'Inferno',
        'authorsite'    => 'http://www.Inferno24.pl',
        'version'       => '1.2',
        'guid'          => 'bab9eaae39210fd31d6c31c4fe4c9baf',
        'codename'      => 'groupimage_to_labeltext'
    );
}

function labelrank_activate(){
    global $db,$lang;
    $lang->load("labelrank_group");
    $setting_group = array(
        "title"          => "Group Label",
        "name"           => "labelrank_group",
        "description"    => $lang->setting_description,
        "disporder"      => "1",
        "isdefault"      => "0",
    );
    
    $gid = $db->insert_query("settinggroups", $setting_group);


    $setting_array = array(
        'labelrank_other' => array(
            'title' => $lang->show_other_group_title,
            'description' => $lang->show_other_group_descr,
            'optionscode' => 'yesno',
            'value' => 1,
            'disporder' => 1
        ),
        'labelrank_postbit' => array(
            'title' => $lang->postbit_title,
            'description' => $lang->postbit_descr,
            'optionscode' => 'yesno',
            'value' => 1,
            'disporder' => 2
        ),
        'labelrank_profile' => array(
            'title' => $lang->profile_title,
            'description' => $lang->profile_descr,
            'optionscode' => 'yesno',
            'value' => 1,
            'disporder' => 3
        ),
        'labelrank_usercp' => array(
            'title' => $lang->usercp_title,
            'description' => $lang->usercp_descr,
            'optionscode' => 'yesno',
            'value' => 1,
            'disporder' => 4
        ),
        'labelrank_memberlist' => array(
            'title' => $lang->memberlist_title,
            'description' => $lang->memberlist_descr,
            'optionscode' => 'yesno',
            'value' => 1,
            'disporder' => 5
        ),
        'labelrank_read_pw' => array(
            'title' => $lang->read_pw_title,
            'description' => $lang->read_pw_descr,
            'optionscode' => 'yesno',
            'value' => 1,
            'disporder' => 6
        ),
    );

    foreach($setting_array as $name => $setting)
    {
        $setting['name'] = $name;
        $setting['gid'] = $gid;

        $db->insert_query('settings', $setting);
    }

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

    rebuild_settings();

    require_once MYBB_ROOT."/inc/adminfunctions_templates.php";

    find_replace_templatesets(
        "memberlist_user",
        "#" . preg_quote('{$group_label}<br />') . "#i",
        '{$usergroup[\'groupimage\']}'
    );
} 

function rank_postbit(&$post){  
    global $mybb, $lang;

    $lang->load("labelrank_group");

    if($mybb->settings['labelrank_postbit']){
        $groupname = getgroupname($post['usergroup']);
        $post['groupimage'] = "<div class=\"profile-rank\"><span class=\"gid-".$post['usergroup']."\">".$groupname."</span></div>";
        if($mybb->settings['labelrank_other'] && strlen($post['additionalgroups'])){
            $groups = explode(",", $post['additionalgroups']);
            $post['groupimage'] .= "<br /><strong>".$lang->labelrank_othergroups."</strong><br />";
            foreach($groups as $group)
            {
                $groupname = getgroupname($group);
                $post['groupimage'] .= "<div class=\"profile-rank\"><span class=\"gid-".$group."\">".$groupname."</span></div> ";
            }
        }
    }
}
function rank_profile(){    
    global $mybb, $memprofile, $groupimage, $lang;

    $lang->load("labelrank_group");

    if($mybb->settings['labelrank_profile']){
        $groupname = getgroupname($memprofile['usergroup']);
        $groupimage = "<div class=\"profile-rank\"><span class=\"gid-".$memprofile['usergroup']."\">".$groupname."</span></div>";

        if($mybb->settings['labelrank_other'] && strlen($memprofile['additionalgroups'])){
            $groups = explode(",", $memprofile['additionalgroups']);
            $groupimage .= "<br /><strong>".$lang->labelrank_othergroups."</strong><br />";
            foreach($groups as $group)
            {
                $groupname = getgroupname($group);
                $groupimage .= "<div class=\"profile-rank\"><span class=\"gid-".$group."\">".$groupname."</span></div> ";
            }
            $groupimage .= "<br />";
        }
    }
}

function rank_usercp(){
    global $mybb, $usergroup, $lang;

    $lang->load("labelrank_group");

    if($mybb->settings['labelrank_usercp']){
        $groupname = getgroupname($mybb->user['usergroup']);
        $usergroup = "<div class=\"profile-rank\"><span class=\"gid-".$mybb->user['usergroup']."\">".$groupname."</span></div>";

        if($mybb->settings['labelrank_other'] && strlen($mybb->user['additionalgroups'])){
            $groups = explode(",", $mybb->user['additionalgroups']);
            $usergroup .= "<br /><strong>".$lang->labelrank_othergroups."</strong> ";
            foreach($groups as $group)
            {
                $groupname = getgroupname($group);
                $usergroup .= "<div class=\"profile-rank\"><span class=\"gid-".$group."\">".$groupname."</span></div> ";
            }
        }
    }
}

function rank_memberlist(){
    global $mybb, $user, $group_label;

    if($mybb->settings['labelrank_memberlist']){
        $groupname = getgroupname($user['usergroup']);
        eval("\$group_label = '<div class=\"profile-rank\"><span class=\"gid-".$user['usergroup']."\">".$groupname."</span></div>';");
    }
}

/*function rank_read_pw(){
    global $mybb, $pm, $usergroup;

    if($mybb->settings['labelrank_read_pw']){
        $groupname = getgroupname($pm['usergroup']);
        eval("\$group_label = '<div class=\"profile-rank\"><span class=\"gid-".$pm['usergroup']."\">".$groupname."</span></div>';");
    }
}*/

function getgroupname($groupid){
    global $cache;

    $usergroups = $cache->read("usergroups");
    $groupname=$usergroups[$groupid]['title'];
    return $groupname;
}
?>