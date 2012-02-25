<?php

namespace app\security\auth\adapter;

class Forum extends \lithium\core\Object
{
    /**
     * The name of the model class to query against. This can either be a model name (i.e.
     * `'Users'`), or a fully-namespaced class reference (i.e. `'app\models\Users'`). When
     * authenticating users, the magic `first()` method is invoked against the model to return the
     * first record found when combining the conditions in the `$_scope` property. (Note that the
     * model method called is configurable using the `$_query` property).
     *
     * @var string
     */
    protected $_model = '';

    /**
     * Additional data to apply to the model query conditions when looking up users, i.e.
     * `array('active' => true)` to disallow authenticating against inactive user accounts.
     *
     * @var array
     */
    protected $_scope = array();

    /**
     * If you require custom model logic in your authentication query, use this setting to specify
     * which model method to call, and this method will receive the authentication query. In return,
     * the adapter expects a `Record` object which implements the `data()` method. See the
     * constructor for more information on setting this property. Defaults to `'first'`, which
     * calls, for example, `Users::first()`.
     *
     * @var string
     */
    protected $_query = 'first';

    /**
     * List of configuration properties to automatically assign to the properties of the adapter
     * when the class is constructed.
     *
     * @var array
     */
    protected $_autoConfig = array('model', 'scope', 'query', 'type');

    public function check($credentials, array $options = array()) {
        // This enables PHPBB login:
        global $phpbb_root_path, $phpEx, $user, $auth, $template, $cache, $db, $config;

        define('IN_PHPBB', true);

        $phpbb_root_path = \app\util\Config::get('forum_path');
        $phpEx = substr(strrchr(__FILE__, '.'), 1);

        include($phpbb_root_path . 'common.' . $phpEx);

        // creates $user and $auth objects here.
        $user->session_begin();
        $logged_in = $user->data['is_registered'];
        $username = $user->data['username_clean'];

        if ($logged_in === false) {
            // Not Logged In
            return false;
        }

        $model = $this->_model;
        $query = $this->_query;
        $conditions = $this->_scope + array(
            'prv_uid'  => $username
        );

        $id = $model::$query(compact('conditions'));

        if (!$user) {
            return false;
        }

        return $id;
    }


    public function set($data, array $options = array()) {
        return $data;
    }

    /**
     * Called by `Auth` when a user session is terminated. Not implemented in the `Form` adapter.
     *
     * @param array $options Adapter-specific options.
     * @return void
     */
    public function clear(array $options = array()) {
    }
}