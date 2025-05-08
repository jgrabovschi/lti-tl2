<?php

use App\Http\Controllers\InterfaceController;
use App\Http\Controllers\StaticController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\WirelessController;
use App\Http\Controllers\DnsController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DhcpController;
use App\Http\Controllers\WireguardController;
use App\Http\Middleware\CheckSessionAccess;
use App\Policies\AccessPolicy;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/', [LoginController::class, 'login'])->name('login.submit');
Route::delete('/profiles/{profile}', [LoginController::class, 'deleteProfile'])->name('profile.delete');


Route::middleware(CheckSessionAccess::class)->group(function () {
    // Interfaces
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('showDashboard');
    Route::post('/dashboard', [DashboardController::class, 'download'])->name('downloadResources');
    Route::get('/interfaces/wireless', [InterfaceController::class, 'wireless'])->name('showInterfacesWireless');
    Route::post('/interfaces/wireless', [InterfaceController::class, 'downloadWireless'])->name('downloadWireless');
    Route::get('/interfaces/bridge', [InterfaceController::class, 'bridge'])->name('showInterfacesBridge');
    Route::post('/interfaces/bridge', [InterfaceController::class, 'downloadBridge'])->name('downloadBridge');
    Route::patch('/interfaces/bridge/{id}', [InterfaceController::class, 'updateBridge'])->name('updateBridge');
    Route::delete('/interfaces/bridge/{id}', [InterfaceController::class, 'destroyBridge'])->name('destroyBridge');
    Route::get('/interfaces/bridge/{id}', [InterfaceController::class, 'editBridge'])->name('editBridge');
    Route::put('/interfaces/create/bridge', [InterfaceController::class, 'storeBridge'])->name('storeBridge');
    Route::get('/interfaces/create/bridge', [InterfaceController::class, 'createBridge'])->name('createBridge');
    Route::put('/interfaces/bridge/port', [InterfaceController::class, 'addPortBridge'])->name('addPortBridge');
    Route::delete('/interfaces/bridge/port/{id}', [InterfaceController::class, 'destroyPortBridge'])->name('destroyPortBridge');


    //ROutes Statics
    Route::get('/routes', [StaticController::class, 'index'])->name('showStatics');
    Route::post('/routes/download', [StaticController::class, 'downloadStatic'])->name('downloadStatic');
    Route::put('/routes/create', [StaticController::class, 'storeStatic'])->name('storeStatic');
    Route::get('/routes/create', [StaticController::class, 'createStatic'])->name('createStatic');
    Route::get('/routes/{id}', [StaticController::class, 'editStatic'])->name('editStatic');
    Route::delete('/routes/{id}', [StaticController::class, 'destroyStatic'])->name('destroyStatic');
    Route::patch('/routes/{id}', [StaticController::class, 'updateStatic'])->name('updateStatic');
    
    

    // Wireless
    Route::put('/wireless/enable/{id}', [WirelessController::class, 'enable'])->name('enableWireless');
    Route::put('/wireless/disable/{id}', [WirelessController::class, 'disable'])->name('disableWireless');
    Route::get('/interfaces/wireless/config/{id}', [WirelessController::class, 'config'])->name('configWireless');
    Route::patch('/interfaces/wireless/config/{id}', [WirelessController::class, 'update'])->name('saveConfigWireless');


    // IP Address
    Route::get('/address', [AddressController::class, 'index'])->name('showAddress');
    Route::post('/address/download', [AddressController::class, 'downloadAddress'])->name('downloadAddress');
    Route::get('/address/create', [AddressController::class, 'createAddress'])->name('createAddress');
    Route::put('/address/create', [AddressController::class, 'storeAddress'])->name('storeAddress');
    Route::delete('/address/{id}', [AddressController::class, 'destroyAddress'])->name('destroyAddress');
    Route::get('/address/{id}', [AddressController::class, 'editAddress'])->name('editAddress');
    Route::patch('/address/{id}', [AddressController::class, 'updateAddress'])->name('updateAddress');



    // DNS
    Route::get('/dns', [DnsController::class, 'index'])->name('showDns');
    Route::post('/dns/download', [DnsController::class, 'downloadDns'])->name('downloadDns');
    Route::get('/dns/add', [DnsController::class, 'AddServersDns'])->name('AddServersDns');
    Route::post('/dns/add', [DnsController::class, 'addServersRDns'])->name('addServersRDns');
    Route::get('/dns/remove', [DnsController::class, 'removeServerDns'])->name('removeServerDns');
    Route::post('/dns/remove', [DnsController::class, 'removeServerRDns'])->name('removeServerRDns');
    //Route::put('/dns/create', [DnsController::class, 'storeDns'])->name('storeDns');
    Route::post('/dns/toggle', [DnsController::class, 'toggleDns'])->name('toggleDns');
    Route::get('/dns/edit', [DnsController::class, 'editDns'])->name('editDns');
    Route::get('/dns/static', [DnsController::class, 'showDnsStatic'])->name('showDnsStatic');
    Route::get('/dns/static/{id}', [DnsController::class, 'editDnsStatic'])->name('editDnsStatic');
    Route::post('/dns/static/{id}', [DnsController::class, 'toggleDnsStatic'])->name('toggleDnsStatic');
    Route::get('/dns/create/static', [DnsController::class, 'createDnsStatic'])->name('createDnsStatic');
    Route::put('/dns/create/static', [DnsController::class, 'storeDnsStatic'])->name('storeDnsStatic');
    Route::patch('/dns/static/{id}', [DnsController::class, 'updateDnsStatic'])->name('updateDnsStatic');
    Route::post('/dns/static/{id}', [DnsController::class, 'toggleDnsStatic'])->name('toggleDnsStatic');
    Route::delete('/dns/static/{id}', [DnsController::class, 'destroyDnsStatic'])->name('destroyDnsStatic');
    Route::post('/dns/download/static', [DnsController::class, 'downloadDnsStatic'])->name('downloadDnsStatic');


    // DHCP
    Route::get('/dhcp/pool', [DhcpController::class, 'indexPool'])->name('showDhcpPool');
    Route::post('/dhcp/pool/download', [DhcpController::class, 'downloadDhcpPool'])->name('downloadDhcpPool');
    Route::get('/dhcp/create/pool', [DhcpController::class, 'createDhcpPool'])->name('createDhcpPool');
    Route::put('/dhcp/create/pool', [DhcpController::class, 'storeDhcpPool'])->name('storeDhcpPool');
    Route::get('/dhcp/pool/{id}', [DhcpController::class, 'editDhcpPool'])->name('editDhcpPool');
    Route::delete('/dhcp/pool/{id}', [DhcpController::class, 'destroyDhcpPool'])->name('destroyDhcpPool');
    Route::patch('/dhcp/pool/{id}', [DhcpController::class, 'updateDhcpPool'])->name('updateDhcpPool');
    Route::get('/dhcp', [DhcpController::class, 'index'])->name('showDhcp');
    Route::post('/dhcp/download', [DhcpController::class, 'downloadDhcp'])->name('downloadDhcp');
    Route::get('/dhcp/create', [DhcpController::class, 'createDhcp'])->name('createDhcp');
    Route::put('/dhcp/create', [DhcpController::class, 'storeDhcp'])->name('storeDhcp');
    Route::get('/dhcp/edit/{id}', [DhcpController::class, 'editDhcp'])->name('editDhcp');
    Route::delete('/dhcp/delete/{id}', [DhcpController::class, 'destroyDhcp'])->name('destroyDhcp');
    Route::patch('/dhcp/edit/{id}', [DhcpController::class, 'updateDhcp'])->name('updateDhcp');


    // Security Profiles
    Route::get('/security', [WirelessController::class, 'showSecurityProfiles'])->name('showSecurityProfiles');
    Route::post('/security/download', [WirelessController::class, 'downloadSecurity'])->name('downloadSecurity');
    Route::get('/security/create', [WirelessController::class, 'createSecurity'])->name('createSecurity');
    Route::put('/security/create', [WirelessController::class, 'storeSecurity'])->name('storeSecurity');
    Route::get('/security/{id}', [WirelessController::class, 'editSecurity'])->name('editSecurity');
    Route::patch('/security/{id}', [WirelessController::class, 'updateSecurity'])->name('updateSecurity');
    Route::delete('/security/{id}', [WirelessController::class, 'deleteSecurity'])->name('deleteSecurity');


    // wireguard
    Route::get('/wireguard', [WireguardController::class, 'showInterfaces'])->name('showInterfacesWireguard');
    Route::post('/wireguard', [WireguardController::class, 'downloadInterfaces'])->name('downloadInterfacesWireguard');
    Route::get('/wireguard/peers', [WireguardController::class, 'showPeers'])->name('showWireguardPeers');
    Route::post('/wireguard/peers', [WireguardController::class, 'downloadPeers'])->name('downloadPeersWireguard');
    Route::get('/wireguard/peers/create', [WireguardController::class, 'createPeer'])->name('createPeerWireguard');
    Route::delete('/wireguard/peers/{id}', [WireguardController::class, 'destroyPeer'])->name('deletePeerWireguard');
    Route::put('/wireguard/peers/create', [WireguardController::class, 'storePeer'])->name('storePeerWireguard');
    Route::get('/wireguard/peers/qr/{id}', [WireguardController::class, 'showQrCode'])->name('showQrCode');

    // Logout
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');



});
