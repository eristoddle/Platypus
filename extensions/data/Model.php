<?php

namespace app\extensions\data;

use lithium\util\String;

    class Model extends \lithium\data\Model {
        /**
         * Modules are namespaced with the model class, and then stored with keys named for the module. 
         * Adding the 'sample' module to the Test class would result in an array like this:
         * {'app\models\Test': {'sample': {'config_var_1': 1, 'config_var_2': 2}}}
         */
        protected static $_modules = array();

        public function rules()
        {
            if (isset($this->validates)) {
                return $this->validates;
            } else {
                return array();
            }
        }

        public static function __init()
        {
            parent::__init();
            $class = get_called_class();

            $typeCastingCallback = function($self, $params, $chain) {
                $schema = $self::schema();

                foreach ($params['data'] ? $params['data'] : array() as $k => $v) {
                    if (!isset($schema[$k]) or !isset($schema[$k]['type'])) {
                        continue;
                    }

                    switch ($schema[$k]['type']) {
                        case 'date':
                            if (is_string($v)) {
                                $params['data'][$k] = strtotime($v);
                            }
                            break;
                        case 'id':
                            if (is_string($v)) {
                                $params['data'][$k] = new \MongoId($v);
                            }
                            break;
                    }
                }
                return $chain->next($self, $params, $chain);
            };
            $class::applyFilter('save', $typeCastingCallback);
        }

        public static function addModules($modules)
        {
            $modules = (array) $modules;
            $calledClass = get_called_class();
            $baseClassName   = array_pop(explode('\\', $calledClass));

            if (!isset(self::$_modules[$calledClass])) {
                self::$_modules[$calledClass] = array();
            }

            foreach ($modules as $name => $config) {
                if (is_integer($name) and is_string($config)) {
                    $name = $config;
                    $config = array();
                }

                $current = isset(self::$_modules[$calledClass][$name]) ? self::$_modules[$calledClass][$name] : $current = array();

                $defaults = array(
                    'class' => String::insert('\app\modules\{:class}\{:name}Module', array('class' => $baseClassName, 'name' => ucfirst($name))),
                    'partial' => String::insert('modules/{:class}/{:name}', array('class' => $baseClassName, 'name' => $name))
                );

                self::$_modules[$calledClass][$name] = ($config + $defaults) + $current;

                if (!class_exists(self::$_modules[$calledClass][$name]['class'])) {
                    throw new \Exception('Module class ' . self::$_modules[$calledClass][$name]['class'] . ' not found.');
                }

                if (isset($config['filters'])) {
                    foreach ($config['filters'] as $function => $filter) {
                        static::applyFilter($function, $filter);
                    }
                }
            }

            return true;
        }

        public static function getModules()
        {
            $calledClass = get_called_class();

            if (!isset(self::$_modules[$calledClass])) {
                self::$_modules[$calledClass] = array();
            }

            return self::$_modules[$calledClass];
        }

        public function modules($entity, array $modules = null)
        {
            if (!isset($modules)) {
                return static::getModules();
            } else {
                return static::addModules($modules);
            }
        }
    }