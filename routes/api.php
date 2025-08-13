<?php

use App\Http\Controllers\Api\AlmacenController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\AreasController;
use App\Http\Controllers\Api\CajaController;
use App\Http\Controllers\Api\InputController;
use App\Http\Controllers\Api\ReporteCajaController;
use App\Http\Controllers\Api\TablesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ClientTypeController;
use App\Http\Controllers\Api\DishesController;
use App\Http\Controllers\Api\EmployeeTypeController;
use App\Http\Controllers\Reportes\CategoryPDFController;
use App\Http\Controllers\Reportes\ClientTypePDFController;
use App\Http\Controllers\Reportes\EmployeeTypePDFController;
use App\Http\Controllers\Reportes\ProductPDFController;
use App\Http\Controllers\Reportes\CustomerPDFController;
use App\Http\Controllers\Reportes\EmployeePDFController;
use App\Http\Controllers\Reportes\DishPDFController;
use App\Http\Controllers\Reportes\InputPDFController;
use App\Http\Controllers\Reportes\TablePDFController;
use App\Http\Controllers\Reportes\SupplierPDFController;
use App\Http\Controllers\Reportes\AlmacenPDFController;
use App\Http\Controllers\Reportes\FloorPDFController;
use App\Http\Controllers\Reportes\AreaPDFController;
use App\Http\Controllers\Reportes\PresentationPDFController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\FloorController;
use App\Http\Controllers\Api\PresentationController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Reportes\ReporteCajaPDFController;
use App\Models\ClientType;
use App\Models\EmployeeType;
use App\Models\Presentation;
use App\Models\ReporteCaja;

Route::middleware('auth')->group(function () {
    Route::apiResource('Almacen', AlmacenController::class);
    Route::apiResource('Categoria', CategoryController::class);
    Route::apiResource('proveedor', SupplierController::class);
    Route::apiResource('presentacion', SupplierController::class);
    Route::apiResource('tipos_clientes', ClientType::class);
    Route::apiResource('tipos_empleados', EmployeeType::class);
    Route::apiResource('cliente', CustomerController::class);
    Route::apiResource('empleado', EmployeeController::class);
    Route::apiResource('piso', FloorController::class);
    Route::apiResource('producto', ProductController::class);
    //Route::apiResource('caja', CajaController::class);

    #EXPORT API
    // Exportación a Excel
    Route::get('/categorias/export-excel', [CategoryController::class, 'exportExcel']);
    Route::get('/almacenes/export-excel', [AlmacenController::class, 'exportExcel']);
    Route::get('/proveedores/export-excel', [SupplierController::class, 'exportExcel']);
    Route::get('/pisos/export-excel', [FloorController::class, 'exportExcel']);
    Route::get('/areas/export-excel', [AreasController::class, 'exportExcel']);
    Route::get('/productos/export-excel', [ProductController::class, 'exportExcel']);
    Route::get('/insumos/export-excel', [InputController::class, 'exportExcel']);
    Route::get('/reporteCajas/export-excel', [ReporteCajaController::class, 'exportExcel']);
    Route::get('/platos/export-excel', [DishesController::class, 'exportExcel']);
    Route::get('/mesas/export-excel', [TablesController::class, 'exportExcel']);
    Route::get('/presentaciones/export-excel', [PresentationController::class, 'exportExcel']);
    Route::get('/tipos_clientes/export-excel', [ClientTypeController::class, 'exportExcel']);
    Route::get('/tipos_empleados/export-excel', [EmployeeTypeController::class, 'exportExcel']);
    Route::get('/clientes/export-excel', [CustomerController::class, 'exportExcel']);
    Route::get('/empleados/export-excel', [EmployeeController::class, 'exportExcel']);

    // Exportación a PDF
    Route::get('/categorias/export-pdf', [CategoryPDFController::class, 'exportPDF']);
    Route::get('/almacenes/export-pdf', [AlmacenPDFController::class, 'exportPDF']);
    Route::get('/proveedores/export-pdf', [SupplierPDFController::class, 'exportPDF']);
    Route::get('/pisos/export-pdf', [FloorPDFController::class, 'exportPDF']);
    Route::get('/areas/export-pdf', [AreaPDFController::class, 'exportPDF']);
    Route::get('/productos/export-pdf', [ProductPDFController::class, 'exportPDF']);
    Route::get('/insumos/export-pdf', [InputPDFController::class, 'exportPDF']);
    Route::get('/reporteCajas/export-pdf', [ReporteCajaPDFController::class, 'exportPDF']);
    Route::get('/platos/export-pdf', [DishPDFController::class, 'exportPDF']);
    Route::get('/mesas/export-pdf', [TablePDFController::class, 'exportPDF']);
    Route::get('/presentaciones/export-pdf', [PresentationPDFController::class, 'exportPDF']);
    Route::get('/tipos_clientes/export-pdf', [ClientTypePDFController::class, 'exportPDF']);
    Route::get('/tipos_empleados/export-pdf', [EmployeeTypePDFController::class, 'exportPDF']);
    Route::get('/clientes/export-pdf', [CustomerPDFController::class, 'exportPDF']);
    Route::get('/empleados/export-pdf', [EmployeePDFController::class, 'exportPDF']);

    #IMPORT API
    // Importación de Excel
    Route::post('/categorias/import-excel', [CategoryController::class, 'importExcel']);
    Route::post('/almacenes/import-excel', [AlmacenController::class, 'importExcel']);
    Route::post('/proveedores/import-excel', [SupplierController::class, 'importExcel']);
    Route::post('/pisos/import-excel', [FloorController::class, 'importExcel']);
    Route::post('/areas/import-excel', [AreasController::class, 'importExcel']);
    Route::post('/productos/import-excel', [ProductController::class, 'importExcel']);
    Route::post('/insumos/import-excel', [InputController::class, 'importExcel']);
    Route::post('/platos/import-excel', [DishesController::class, 'importExcel']);
    Route::post('/mesas/import-excel', [TablesController::class, 'importExcel']);
    Route::post('/presentaciones/import-excel', [PresentationController::class, 'importExcel']);
    Route::post('/tipos_clientes/import-excel', [ClientTypeController::class, 'importExcel']);
    Route::post('/tipos_empleados/import-excel', [EmployeeTypeController::class, 'importExcel']);
    Route::post('/clientes/import-excel', [CustomerController::class, 'importExcel']);
    Route::post('/empleados/import-excel', [EmployeeController::class, 'importExcel']);

});

