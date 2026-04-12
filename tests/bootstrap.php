<?php
/**
 * PHPUnit bootstrap for hypeIcons plugin tests.
 * Plugin must be installed at {elgg_root}/mod/hypeicons/
 */

// tests/ -> mod/hypeicons/ -> mod/ -> elgg_root/
$elggRoot = dirname(dirname(dirname(__DIR__)));

require_once $elggRoot . '/vendor/autoload.php';

// Load Elgg test classes (UnitTestCase, IntegrationTestCase, etc.)
$testClassesDir = $elggRoot . '/vendor/elgg/elgg/engine/tests/classes';
spl_autoload_register(function ($class) use ($testClassesDir) {
	$file = $testClassesDir . '/' . str_replace('\\', '/', $class) . '.php';
	if (file_exists($file)) {
		require_once $file;
	}
});

// Register hypeJunction\Icons namespace PSR-0-ish autoload so tests work even
// if the plugin isn't active in the test DB.
$pluginRoot = dirname(__DIR__);
spl_autoload_register(function ($class) use ($pluginRoot) {
	$prefix = 'hypeJunction\\Icons\\';
	if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
		return;
	}
	$relative = substr($class, strlen($prefix));
	$file = $pluginRoot . '/classes/hypeJunction/Icons/' . str_replace('\\', '/', $relative) . '.php';
	if (file_exists($file)) {
		require_once $file;
	}
});

if (file_exists($pluginRoot . '/vendor/autoload.php')) {
	require_once $pluginRoot . '/vendor/autoload.php';
}

\Elgg\Application::loadCore();
