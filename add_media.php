<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$m = new \App\Models\MediaUpload();
$m->title = 'PayFast Logo Premium';
$m->path = 'payfast_logo_premium.png';
$m->alt = 'PayFast';
$m->size = '100KB';
$m->dimensions = '1024x1024';
$m->save();

update_static_option('payfast_preview_logo', $m->id);
echo "Done! ID is " . $m->id;
