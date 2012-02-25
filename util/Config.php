<?php
namespace app\util;

class Config
{
    public static $items = array();

    /**
     * Loads a config file or array.
     *
     * @param    mixed    $file         string file | config array
     * @param    mixed    $group        null for no group, true for group is filename, false for not storing in the master config
     * @return   array                  the (loaded) config array
     */
    public static function load($file, $group = null)
    {
        $config = array();

        if (is_array($file)) {
            $config = $file;
        } elseif (is_string($file)) {
            $info = pathinfo($file);
            $type = 'json';

            if (isset($info['extension'])) {
                $type = $info['extension'];
            }

            $loadMethod = 'read' . ucfirst($type);

            if (method_exists(get_called_class(), $loadMethod)) {
                $config = static::$loadMethod($file);
            } else {
                throw new \Exception(sprintf('Invalid config type "%s".', $type));
            }
        } else {
            throw new \Exception('Invalid config specified.');
        }

        if ($group === null) {
            static::$items = array_merge(static::$items, $config);
        } else {
            if (!isset(static::$items[$group])) {
                static::$items[$group] = array();
            }

            static::$items[$group] = array_merge(static::$items[$group],$config);
        }

        return $config;
    }

    public static function readJson($file)
    {
        if (file_exists($file) === false) {
            throw new \Exception("Config file {$file} not found.");
        }

        $contents = file_get_contents($file);

        return json_decode($contents, true);
    }

    /**
     * Returns a (dot notated) config setting
     *
     * @param   string   $item      name of the config item, can be dot notated
     * @param   mixed    $default   the return value if the item isn't found
     * @return  mixed               the config setting or default if not found
     */
    public static function get($item, $default = null)
    {
        $paths   = explode('.', $item);
        $val_arr = static::$items;

        foreach ($paths as $p) {
            if (isset($val_arr[$p]) === true) {
                $val_arr = $val_arr[$p];
            } else {
                return $default;
            }
        }

        return $val_arr;
    }

    /**
     * Sets a (dot notated) config item
     *
     * @param    string    a (dot notated) config key
     * @param    mixed     the config value
     * @return   void      the \Arr::set result
     */
    public static function set($item, $value)
    {
        $paths   = explode('.', $item);
        $val_arr = &static::$items;

        foreach ($paths as $p) {
            if (isset($val_arr[$p]) === false) {
                $val_arr[$p] = array();
            }

            $val_arr = &$val_arr[$p];
        }

        $val_arr = $value;

    }
}