<?php

$json = app(App\Http\Controllers\Admin\AdminDashboardController::class)->logsFeed();
$data = $json->getData(true);

echo 'counts: ' . json_encode($data['counts']) . PHP_EOL;
echo 'logs returned: ' . count($data['logs']) . PHP_EOL;
echo 'first log: ' . json_encode($data['logs'][0] ?? null, JSON_UNESCAPED_UNICODE) . PHP_EOL;
