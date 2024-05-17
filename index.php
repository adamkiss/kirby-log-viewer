<?php

use Kirby\Cms\App;
use AdamKiss\LogViewer\LogViewerArea;

require_once __DIR__ . '/LogViewerArea.php';

App::plugin('adamkiss/kirby-log-viewer', [
	'options' => [
		'timezone' => (new \DateTimeZone('Europe/Bratislava')),
	],
	'areas' => [
		LogViewerArea::ID => LogViewerArea::blueprint(...),
	],
]);
