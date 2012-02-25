<?php

namespace app\security\auth\adapter;

use \lithium\security\Auth;
use \lithium\core\Object;

class Proxy extends Object
{
    /**
     * This takes an array of adapter names to ignore.
     *
     * @var array
     */
    protected $_exclude = array();

    /**
     * List of configuration properties to automatically assign to the properties of the adapter
     * when the class is constructed.
     *
     * @var array
     */
    protected $_autoConfig = array('exclude');

    public function check($credentials, array $options = array())
    {
        foreach (Auth::config() as $name => $auth) {
            if ($auth['adapter'] === $this->_config['adapter'] or
                in_array($name, $this->_exclude)) {
                continue;
            }

            $result = Auth::check($name);

            if ($result) {
                return $result;
            }
        }

        return false;
    }

    public function set($data, array $options = array())
    {
        return $data;
    }

    /**
     * Clears all other adapters
     *
     * @param array $options Adapter-specific options. Not implemented in this adapter.
     * @return void
     */
    public function clear(array $options = array()) {
        foreach (Auth::config() as $name => $auth) {
            if ($auth['adapter'] === $this->_config['adapter']) {
                continue;
            }

            Auth::clear($name);
        }
    }
}