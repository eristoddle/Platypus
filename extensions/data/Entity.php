<?php

namespace app\extensions\data;

use BadMethodCallException;
use lithium\core\Libraries;
use lithium\util\Inflector;

/**
 * This class adds temporary storage to entities. Data can be placed in this cache to reduce
 * computation or queries from multiple calls to the same model methods. 
 *
 * Implement by adding the following to the connection in your connections.php bootstrap:
 * 'classes' => array('entity' => 'path\to\lib\Entity')
 *
 * @see lithium\data\Entity
 */
class Entity extends \lithium\data\Entity {
    protected $_volatileDataCache = array();

    public function tempDataGet($key) {
        if (!isset($this->_volatileDataCache[$key])) {
            return null;
        }
        
        return $this->_volatileDataCache[$key];
    }

    public function tempDataSet($key, $value) {
        $this->_volatileDataCache[$key] = $value;
    }

}