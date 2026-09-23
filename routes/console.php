<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('about:portal', function () {
    $this->info('Viwanda na Biashara Portal — Ministry of Industry and Trade');
})->purpose('Portal information');
