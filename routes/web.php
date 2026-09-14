<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::middleware('guest')->group(function () {

    Route::get('/login', [
        AuthController::class,
        'showLogin'
    ])->name('login');

    Route::post('/login', [
        AuthController::class,
        'login'
    ])->name('login.process');

});

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [
        DashboardController::class,
        'index'
    ])->middleware('permiso:dashboard.ver')->name('dashboard');

    Route::prefix('administracion')->name('admin.')->middleware('permiso:municipalidades.ver')->group(function () {
        Route::get('/municipalidades', [\App\Http\Controllers\AdminController::class, 'municipalidades'])->name('municipalidades');
        Route::get('/municipalidades/{municipalidad}/editar', [\App\Http\Controllers\AdminController::class, 'editMunicipalidad'])->name('municipalidades.edit');
    });
    Route::prefix('administracion')->name('admin.')->middleware('permiso:municipalidades.gestionar')->group(function () {
        Route::post('/municipalidades', [\App\Http\Controllers\AdminController::class, 'storeMunicipalidad'])->name('municipalidades.store');
        Route::post('/municipalidades/{municipalidad}/duplicar', [\App\Http\Controllers\AdminController::class, 'duplicateMunicipalidad'])->name('municipalidades.duplicate');
        Route::put('/municipalidades/{municipalidad}', [\App\Http\Controllers\AdminController::class, 'updateMunicipalidad'])->name('municipalidades.update');
        Route::delete('/municipalidades/{municipalidad}', [\App\Http\Controllers\AdminController::class, 'destroyMunicipalidad'])->name('municipalidades.destroy');
    });

    Route::prefix('administracion')->name('admin.')->middleware('permiso:usuarios.gestionar')->group(function () {
        Route::get('/usuarios', [\App\Http\Controllers\AdminController::class, 'usuarios'])->name('usuarios');
        Route::post('/usuarios', [\App\Http\Controllers\AdminController::class, 'storeUsuario'])->name('usuarios.store');
        Route::get('/usuarios/{usuario}/editar', [\App\Http\Controllers\AdminController::class, 'editUsuario'])->name('usuarios.edit');
        Route::put('/usuarios/{usuario}', [\App\Http\Controllers\AdminController::class, 'updateUsuario'])->name('usuarios.update');
        Route::delete('/usuarios/{usuario}', [\App\Http\Controllers\AdminController::class, 'destroyUsuario'])->name('usuarios.destroy');
    });
    Route::prefix('administracion')->name('admin.')->middleware('permiso:roles.gestionar')->group(function () {
        Route::get('/roles', [\App\Http\Controllers\AdminController::class, 'roles'])->name('roles');
        Route::post('/roles', [\App\Http\Controllers\AdminController::class, 'storeRol'])->name('roles.store');
        Route::put('/roles/{rol}', [\App\Http\Controllers\AdminController::class, 'updateRol'])->name('roles.update');
        Route::delete('/roles/{rol}', [\App\Http\Controllers\AdminController::class, 'destroyRol'])->name('roles.destroy');
        Route::post('/permisos', [\App\Http\Controllers\AdminController::class, 'storePermiso'])->name('permisos.store');
        Route::put('/permisos/{permiso}', [\App\Http\Controllers\AdminController::class, 'updatePermiso'])->name('permisos.update');
        Route::delete('/permisos/{permiso}', [\App\Http\Controllers\AdminController::class, 'destroyPermiso'])->name('permisos.destroy');
    });

    Route::prefix('catalogos')->name('catalogos.')->group(function () {
        Route::get('/estructura-organizacional', [\App\Http\Controllers\CatalogoEstructuraOrganizacionalController::class, 'index'])->name('estructura.index');
        Route::post('/estructura-organizacional', [\App\Http\Controllers\CatalogoEstructuraOrganizacionalController::class, 'store'])->name('estructura.store');
        Route::put('/estructura-organizacional/{catalogo}', [\App\Http\Controllers\CatalogoEstructuraOrganizacionalController::class, 'update'])->name('estructura.update');
        Route::delete('/estructura-organizacional/{catalogo}', [\App\Http\Controllers\CatalogoEstructuraOrganizacionalController::class, 'destroy'])->name('estructura.destroy');
    });

    Route::get('/administracion/auditoria', [\App\Http\Controllers\AuditoriaController::class, 'index'])
        ->middleware('permiso:auditoria.ver')
        ->name('admin.auditoria');

    Route::prefix('organizacion')->name('organizacion.')->middleware('permiso:organizacion.ver')->group(function () {
        Route::get('/', [\App\Http\Controllers\OrganizacionController::class, 'index'])->name('index');
    });
    Route::prefix('organizacion')->name('organizacion.')->middleware('permiso:organizacion.gestionar')->group(function () {
        Route::post('/organos', [\App\Http\Controllers\OrganizacionController::class, 'storeOrgano'])->name('organos.store');
        Route::post('/unidades', [\App\Http\Controllers\OrganizacionController::class, 'storeUnidad'])->name('unidades.store');
        Route::post('/puestos', [\App\Http\Controllers\OrganizacionController::class, 'storePuesto'])->name('puestos.store');
        Route::post('/funciones', [\App\Http\Controllers\OrganizacionController::class, 'storeFuncion'])->name('funciones.store');
        Route::get('/{tipo}/{id}/editar', [\App\Http\Controllers\OrganizacionController::class, 'edit'])->whereIn('tipo', ['organos', 'unidades', 'puestos', 'funciones'])->name('edit');
        Route::put('/{tipo}/{id}', [\App\Http\Controllers\OrganizacionController::class, 'update'])->whereIn('tipo', ['organos', 'unidades', 'puestos', 'funciones'])->name('update');
        Route::delete('/{tipo}/{id}', [\App\Http\Controllers\OrganizacionController::class, 'destroy'])->whereIn('tipo', ['organos', 'unidades', 'puestos', 'funciones'])->name('destroy');
    });

    Route::prefix('normativa')->name('normativa.')->middleware('permiso:normativa.ver')->group(function () {
        Route::get('/', [\App\Http\Controllers\NormativaController::class, 'index'])->name('index');
    });
    Route::prefix('normativa')->name('normativa.')->middleware('permiso:normativa.gestionar')->group(function () {
        Route::post('/', [\App\Http\Controllers\NormativaController::class, 'store'])->name('store');
    });

    Route::get('/instrumentos', [\App\Http\Controllers\InstrumentoController::class, 'index'])
        ->middleware('permiso:instrumentos.ver')
        ->name('instrumentos.index');
    Route::get('/instrumentos/{instrumento}', [\App\Http\Controllers\InstrumentoWorkflowController::class, 'show'])
        ->middleware('permiso:instrumentos.ver')
        ->name('instrumentos.show');
    Route::prefix('instrumentos')->name('instrumentos.')->middleware('permiso:instrumentos.gestionar')->group(function () {
        Route::post('/{instrumento}/versiones', [\App\Http\Controllers\InstrumentoWorkflowController::class, 'storeVersion'])->name('versiones.store');
        Route::post('/{instrumento}/documentos', [\App\Http\Controllers\InstrumentoWorkflowController::class, 'storeDocument'])->name('documentos.store');
        Route::post('/versiones/{version}/enviar-revision', [\App\Http\Controllers\InstrumentoWorkflowController::class, 'submitRevision'])->name('versiones.submit');
        Route::post('/observaciones/{observacion}/responder', [\App\Http\Controllers\InstrumentoWorkflowController::class, 'respondObservation'])->name('observaciones.respond');
    });
    Route::post('/instrumentos/versiones/{version}/observaciones', [\App\Http\Controllers\InstrumentoWorkflowController::class, 'storeObservation'])
        ->middleware('permiso:instrumentos.revisar')
        ->name('instrumentos.versiones.observations.store');
    Route::post('/instrumentos/versiones/{version}/decidir', [\App\Http\Controllers\InstrumentoWorkflowController::class, 'decide'])
        ->middleware('permiso:instrumentos.aprobar')
        ->name('instrumentos.versiones.decide');


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', [
        AuthController::class,
        'logout'
    ])->name('logout');

});

Route::get('/', function () {

    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');

});