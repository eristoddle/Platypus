<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * This file creates a default cache configuration using the most optimized adapter available, and
 * uses it to provide default caching for high-overhead operations.
 */
use lithium\storage\Cache;
use lithium\core\Libraries;
use lithium\core\Environment;
use lithium\action\Dispatcher;
use lithium\storage\cache\adapter\Apc;

/**
 * This configures the default cache, based on whether ot not APC user caching is enabled. If it is
 * not, file caching will be used. Most of this code is for getting you up and running only, and
 * should be replaced with a hard-coded configuration, based on the cache(s) you plan to use.
 */
$default = array('adapter' => 'Memcache', 'host' => '127.0.0.1:11211');
Cache::config(compact('default'));

/**
 * Caches paths for auto-loaded and service-located classes when in production.
 */
Dispatcher::applyFilter('run', function($self, $params, $chain) {
	if (!Environment::get('production')) {
		return $chain->next($self, $params, $chain);
	}
	$key = md5(LITHIUM_APP_PATH) . '.core.libraries';

	if ($cache = Cache::read('default', $key)) {
		$cache = (array) $cache + Libraries::cache();
		Libraries::cache($cache);
	}
	$result = $chain->next($self, $params, $chain);

	if ($cache != Libraries::cache()) {
		Cache::write('default', $key, Libraries::cache(), '+1 day');
	}
	return $result;
});

?>
