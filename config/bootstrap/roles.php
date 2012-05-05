<?php

    # Format: group_name => array('perm1', 'perm2')

    $groupPermMap = array(
        'admin' => array('*'),
        'steering-committee' => array('register.free'),
        'sc-emeritus' => array('register.free'),
        'comp-leagues' => array('register.free'),
        'league-manager' => array('leagues.*', 'users.view_details'),
        'user' => array('leagues.register'),
        'guest' => array()
    );

    $defaultGroups = array('guest');

    $groupsField = 'permission_groups';


    use app\util\Permissions;

    Permissions::config(compact('groupPermMap', 'defaultGroups', 'groupsField'));
