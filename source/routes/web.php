<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProblemController;


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/minh-nguyet-thi', [CategoryController::class, 'index'])->name('category.index');
Route::get('/am-da-lau', [ProblemController::class, 'index'])->name('problem.index');
Route::post('/problem', [ProblemController::class, 'store'])->name('problem.store');
Route::get('/am-da-lau/result_add/{id}', [ProblemController::class, 'result_add'])->name('problem.result_add');
Route::post('/am-da-lau/result_save/{id}', [ProblemController::class, 'result_save'])->name('problem.result_save');
Route::get('/am-da-lau/play/{id}', [ProblemController::class, 'play'])->name('problem.play');