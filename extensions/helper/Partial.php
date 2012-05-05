<?php
# From: http://awhitebox.com/partial_templates_in_lithium/
namespace app\extensions\helper;

use lithium\util\Inflector;


class Partial extends \lithium\template\Helper {
  public function __call($method, $args) {
    $params = empty($args)? array() : $args[0];
    $method = Inflector::underscore($method);
    return $this->_context->view()->render(array('element'=> "{$method}_partial"), $params);
  }
}
