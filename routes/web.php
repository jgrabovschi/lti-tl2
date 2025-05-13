<?php


use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NamespaceController;
use App\Http\Controllers\DeploymentController;
use App\Http\Controllers\ServiceController;
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


    // Deployment
    Route::get('/deployment', [DeploymentController::class, 'index'])->name('showDeployment');
    Route::post('/deployment', [DeploymentController::class, 'download'])->name('downloadDeployment');
    Route::get('/deployment/create', [DeploymentController::class, 'create'])->name('createDeployment');
    Route::put('/deployment/create', [DeploymentController::class, 'store'])->name('storeDeployment');
    Route::delete('/namespaces/{namespace}/deployment/{name}', [DeploymentController::class, 'destroy'])->name('deleteDeployment');

    // Service && Ingress
    Route::get('/service', [ServiceController::class, 'index'])->name('showService');
    Route::post('/service', [ServiceController::class, 'downloadService'])->name('downloadService');
    Route::post('/ingress', [ServiceController::class, 'downloadIngress'])->name('downloadIngress');
    Route::get('/service/create', [ServiceController::class, 'create'])->name('createService');
    Route::put('/service/create', [ServiceController::class, 'store'])->name('storeService');
    Route::delete('/namespaces/{namespace}/deployment/{name}', [ServiceController::class, 'destroy'])->name('deleteService');



    // Logout
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

});
