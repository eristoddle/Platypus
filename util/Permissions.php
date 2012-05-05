<?php
namespace app\util;

class Permissions extends \lithium\core\StaticObject
{
    protected static $_groupPermMap  = array();
    protected static $_defaultGroups = array();
    protected static $_groupsField   = 'groups';

    public static function config($config = array()) 
    {
        if (isset($config['groupPermMap'])) {
            foreach ((array)$config['groupPermMap'] as $grp => $permList) {
                static::$_groupPermMap[$grp] = array();

                foreach ($permList as $p) {
                    $list = explode('.', $p);

                    // if (substr($list[0],0,1) === '!') {
                    //     $list[0] = substr($list[0], 1);
                    //     $list[] = '!';
                    // }

                    static::$_groupPermMap[$grp][] = $list;
                }
            }

            # Remove so auto-config doesn't overwrite
            unset($config['groupPermMap']);
        }

        foreach ($config as $key => $val) {
            if (isset(static::${'_' . $key})) {
                static::${'_' . $key} = $val;
            }
        }
    }

    public static function check($requested_action, $requester_groups = null)
    {
        if (is_object($requester_groups)) {
            if (isset($requester_groups->{static::$_groupsField})) {
                $gf = $requester_groups->{static::$_groupsField};

                if (is_object($requester_groups->{static::$_groupsField})) {
                    $requester_groups = $gf->to('array');
                } else if (is_array($gf)) {
                    $requester_groups = $gf;
                } else {
                    $requester_groups = array($gf);
                }
            } else {
                $requester_groups = array();
            }
        }

        if (!isset($requester_groups)) {
            $requester_groups = (array) self::$_defaultGroups;
        }

        $actionStack = explode('.', $requested_action);
        $groupList  = (array) $requester_groups;
        $remainingPerms = array();

        # Merge rule lists:
        foreach ($groupList as $g) {
            if (isset(static::$_groupPermMap[$g]) and is_array(static::$_groupPermMap[$g])) {
                $remainingPerms = array_merge($remainingPerms, static::$_groupPermMap[$g]);
            }
        }

        if (empty($remainingPerms) or empty($actionStack)) {
            return false;
        }

        while (!empty($actionStack)) {
            $currentLevel = $actionStack[0];
            $isLast       = (count($actionStack) === 1) ?: false;

            $newRemainingPerms = array();

            foreach ($remainingPerms as $p) {
                if (count($p) === 1 and ($p[0] === '*' or $p[0] === $currentLevel)) {
                    return true;
                }

                if ($p[0] === '*' or $p[0] === $currentLevel) {
                    $newRemainingPerms[] = array_slice($p, 1);
                }
            }

            $remainingPerms = $newRemainingPerms;

            $actionStack = array_slice($actionStack, 1);
        }

        # Nothing matched, return false;
        return false;
    }
}