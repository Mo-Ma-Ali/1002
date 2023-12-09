<?php

use Illuminate\Support\Facades\Route;
use Stichoza\GoogleTranslate\GoogleTranslate;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $tr = new GoogleTranslate('en');
    return $tr->setSource("ar")->setTarget("it")->translate("قدام مريتها عادي بتدلع برحتها استناها واستعجلها تضحكي و ابصلها تختار ألوانها وتسألني عن رأيي بفستنها طب أختار أزاي وجمالها يحلي الدنيا بحالها");
});
