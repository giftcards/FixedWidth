<?php
if (!is_file($autoloadFile = __DIR__.'/../vendor/autoload.php')) {
	throw new \LogicException('Could not find autoload.php in vendor/. Did you run "composer install --dev"?');
}

$loader = require $autoloadFile;

function giftcardsFixedWidthAutoload($class)
{
    $dir = dirname(__DIR__) . '/';
	$prefixes = array('Giftcards\\FixedWidth');
	foreach ($prefixes as $prefix) {
		if (0 !== strpos($class, $prefix)) {
			continue;
		}
		$path = $dir . implode('/', array_slice(explode('\\', $class), 2)).'.php';

		if (!$path = stream_resolve_include_path($path)) {
			return false;
		}
		require $path;

		return true;
	}
}

spl_autoload_register('giftcardsFixedWidthAutoload', true, true);

return $loader;