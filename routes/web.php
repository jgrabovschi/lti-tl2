<?php


use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NamespaceController;
use App\Http\Controllers\NodeController;
use App\Http\Controllers\PodController;
use App\Http\Middleware\CheckSessionAccess;
use App\Policies\AccessPolicy;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/', [LoginController::class, 'login'])->name('login.submit');
Route::delete('/profiles/{profile}', [LoginController::class, 'deleteProfile'])->name('profile.delete');


Route::middleware(CheckSessionAccess::class)->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('showDashboard');
    Route::post('/dashboard', [DashboardController::class, 'download'])->name('downloadResources');

    // Nodes
    Route::get('/nodes', [NodeController::class, 'index'])->name('showNodes');
    Route::post('/nodes', [NodeController::class, 'download'])->name('downloadNodes');

    // Namespaces
    Route::get('/namespaces', [NamespaceController::class, 'index'])->name('showNamespaces');
    Route::post('/namespaces', [NamespaceController::class, 'download'])->name('downloadNamespaces');
    Route::get('/namespaces/create', [NamespaceController::class, 'create'])->name('createNamespace');
    Route::put('/namespaces/create', [NamespaceController::class, 'store'])->name('storeNamespace');
    Route::delete('/namespaces/{name}', [NamespaceController::class, 'destroy'])->name('deleteNamespace');

    // Pods
    Route::get('/pods', [PodController::class, 'index'])->name('showPods');
    Route::post('/pods', [PodController::class, 'download'])->name('downloadPods');
    Route::get('/pods/create', [PodController::class, 'create'])->name('createPod');
    Route::put('/pods/create', [PodController::class, 'store'])->name('storePod');
    Route::delete('/namespaces/{namespace}/pods/{name}', [PodController::class, 'destroy'])->name('deletePod');




    // Logout
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

});
