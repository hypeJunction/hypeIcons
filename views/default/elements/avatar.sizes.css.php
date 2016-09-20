<?php

$sizes = elgg_get_icon_sizes('user');
foreach ($sizes as $name => $opts) {
	echo ".elgg-avatar-$name {\n";
		if ($opts['w']) {
			echo "max-width: {$opts['w']}px;\n";
		}
		if ($opts['h']) {
			echo "max-height: {$opts['h']}px;\n";
		}
	echo "}\n";
}

$sizes = elgg_get_icon_sizes('object', 'file');
foreach ($sizes as $name => $opts) {
	echo ".elgg-avatar-object-file.elgg-avatar-$name {\n";
		if ($opts['w']) {
			echo "max-width: {$opts['w']}px;\n";
		}
		if ($opts['h']) {
			echo "max-height: {$opts['h']}px;\n";
		}
	echo "}\n";
}

$covers = elgg_get_icon_sizes('user', null, 'cover');
?>
.elgg-entity-cover {
    background-size: cover;
    background-repeat: no-repeat;
    background-position: 50%;
    position: relative;
    padding-bottom: <?= $covers['master']['h'] / $covers['master']['w'] * 100 ?>%;
    height: 0;
}
.elgg-menu-topbar > li.elgg-avatar-topbar {
	max-height: none;
	max-width: none;
}