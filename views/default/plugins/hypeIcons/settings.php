<?php
$entity = elgg_extract('entity', $vars);

ob_start();
echo elgg_view_input('select', [
	'name' => 'params[icon_corners]',
	'value' => $entity->icon_corners ? : 'rounded',
	'options_values' => array(
		'square' => elgg_echo('interface:icons:corners:square'),
		'rounded' => elgg_echo('interface:icons:corners:rounded'),
		'circle' => elgg_echo('interface:icons:corners:circle'),
	),
	'label' => elgg_echo('interface:icons:corners'),
	'help' => elgg_echo('interface:icons:corners:help'),
]);

echo elgg_view_input('select', [
	'name' => 'params[replace_default_icons]',
	'value' => $entity->replace_default_icons ? : false,
	'options_values' => array(
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	),
	'label' => elgg_echo('interface:icons:replace_default_icons'),
	'label' => elgg_echo('interface:icons:replace_default_icons:help'),
]);

echo elgg_view_input('select', [
	'name' => 'params[replace_filetype_icons]',
	'value' => $entity->replace_filetype_icons ? : false,
	'options_values' => array(
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	),
	'label' => elgg_echo('interface:icons:replace_filetype_icons'),
	'label' => elgg_echo('interface:icons:replace_filetype_icons:help'),
]);
$fields = ob_get_clean();

echo elgg_view_module('aside', elgg_echo('interactions:settings:icon_display'), $fields);

$dbprefix = elgg_get_config('dbprefix');
$sql = "
	SELECT *
	FROM {$dbprefix}entity_subtypes
	ORDER BY subtype
";

$rows = get_data($sql);

$options = [
	'user:' => elgg_echo('item:user'),
	'group:' => elgg_echo('item:group'),
];

foreach ($rows as $row) {
	$type = $row->type;
	$subtype = $row->subtype;
	$options["$type:$subtype"] = elgg_echo("item:$type:$subtype");
}

ob_start();
?>
<table class="elgg-table-alt">
	<thead>
		<tr>
			<th></th>
			<th><?= elgg_echo('interactions:icon_types:icon') ?></th>
			<th><?= elgg_echo('interactions:icon_types:cover') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($options as $option => $label) {
			?>
			<tr>
				<td><?= $label ?></td>
				<td>
					<?=
					elgg_view('input/checkbox', [
						'name' => "params[icon:$option]",
						'value' => '1',
						'default' => '0',
						'checked' => (bool) $entity->{"icon:$option"},
					]);
					?>
				</td>
				<td>
					<?=
					elgg_view('input/checkbox', [
						'name' => "params[cover:$option]",
						'value' => '1',
						'default' => '0',
						'checked' => (bool) $entity->{"cover:$option"},
					]);
					?>
				</td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>
<?php
$table = ob_get_clean();

$help = elgg_format_element('p', [
	'class' => 'elgg-text-help',
], elgg_echo('interactions:settings:icon_types:help'));

echo elgg_view_module('aside', elgg_echo('interactions:settings:icon_types'), $table);
