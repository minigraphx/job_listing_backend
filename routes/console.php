<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Superadmin\PageBuilder\PageBuilderComponentCategory;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command("inspire", function () {
    $this->comment(Inspiring::quote());
})->purpose("Display an inspiring quote");

Artisan::command("app:seed-page-builder-components", function () {
    //
})->describe("Seed component categories");

Artisan::command("app:seed-page-builder-component-categories", function () {
    //
})->describe("Seed component categories");
