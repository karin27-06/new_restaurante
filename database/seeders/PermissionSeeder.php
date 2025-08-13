<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        #User
        Permission::create(['name' => 'crear usuarios']);
        Permission::create(['name' => 'editar usuarios']);
        Permission::create(['name' => 'eliminar usuarios']);
        Permission::create(['name' => 'ver usuarios']);
        # Roles
        Permission::create(['name' =>'crear roles']);
        Permission::create(['name' =>'editar roles']);
        Permission::create(['name' =>'eliminar roles']);
        Permission::create(['name' =>'ver roles']);
        # Permisos
        Permission::create(['name' =>'crear permisos']);
        Permission::create(['name' =>'editar permisos']);
        Permission::create(['name' =>'eliminar permisos']);
        Permission::create(['name' =>'ver permisos']);
        #Pisos
        Permission::create(['name' => 'crear pisos']);
        Permission::create(['name' => 'editar pisos']);
        Permission::create(['name' => 'eliminar pisos']);
        Permission::create(['name' => 'ver pisos']);
        #Productos
        Permission::create(['name' => 'crear productos']);
        Permission::create(['name' => 'editar productos']);
        Permission::create(['name' => 'eliminar productos']);
        Permission::create(['name' => 'ver productos']);
        #cajas
        Permission::create(['name' => 'crear cajas']);
        Permission::create(['name' => 'editar cajas']);
        Permission::create(['name' => 'eliminar cajas']);
        Permission::create(['name' => 'ver cajas']);
        #Almacenes
        Permission::create(['name' => 'crear almacenes']);
        Permission::create(['name' => 'editar almacenes']);
        Permission::create(['name' => 'eliminar almacenes']);
        Permission::create(['name' => 'ver almacenes']);
        #Categorias
        Permission::create(['name' => 'crear categorias']);
        Permission::create(['name' => 'editar categorias']);
        Permission::create(['name' => 'eliminar categorias']);
        Permission::create(['name' => 'ver categorias']);
        #Proveedores
        Permission::create(['name' => 'crear proveedores']);
        Permission::create(['name' => 'editar proveedores']);
        Permission::create(['name' => 'eliminar proveedores']);
        Permission::create(['name' => 'ver proveedores']);
        #Presentaciones
        Permission::create(['name' => 'crear presentaciones']);
        Permission::create(['name' => 'editar presentaciones']);
        Permission::create(['name' => 'eliminar presentaciones']);
        Permission::create(['name' => 'ver presentaciones']);
        #cliente
        Permission::create(['name' => 'crear clientes']);
        Permission::create(['name' => 'editar clientes']);
        Permission::create(['name' => 'eliminar clientes']);
        Permission::create(['name' => 'ver clientes']);
        #Tipo Clientes
        Permission::create(['name' => 'crear tipos_clientes']);
        Permission::create(['name' => 'editar tipos_clientes']);
        Permission::create(['name' => 'eliminar tipos_clientes']);
        Permission::create(['name' => 'ver tipos_clientes']);
        #Areas
        Permission::create(['name' => 'crear areas']);
        Permission::create(['name' => 'editar areas']);
        Permission::create(['name' => 'eliminar areas']);
        Permission::create(['name' => 'ver areas']);
        #Dishes
        Permission::create(['name' => 'crear dishes']);
        Permission::create(['name' => 'editar dishes']);
        Permission::create(['name' => 'eliminar dishes']);
        Permission::create(['name' => 'ver dishes']);
        #empleado
        Permission::create(['name' => 'crear empleados']);
        Permission::create(['name' => 'editar empleados']);
        Permission::create(['name' => 'eliminar empleados']);
        Permission::create(['name' => 'ver empleados']);
        #Tipo Empleados
        Permission::create(['name' => 'crear tipos_empleados']);
        Permission::create(['name' => 'editar tipos_empleados']);
        Permission::create(['name' => 'eliminar tipos_empleados']);
        Permission::create(['name' => 'ver tipos_empleados']);
        #mesas
        Permission::create(['name' => 'crear mesas']);
        Permission::create(['name' => 'editar mesas']);
        Permission::create(['name' => 'eliminar mesas']);
        Permission::create(['name' => 'ver mesas']);
        #insumos
        Permission::create(['name' => 'crear insumos']);
        Permission::create(['name' => 'editar insumos']);
        Permission::create(['name' => 'eliminar insumos']);
        Permission::create(['name' => 'ver insumos']);

        #seccion de movimientos
        Permission::create(['name' => 'crear movimientos']);
        Permission::create(['name' => 'editar movimientos']);
        Permission::create(['name' => 'eliminar movimientos']);
        Permission::create(['name' => 'ver movimientos']);

        #Movimientos de insumos
        Permission::create(['name' => 'crear facturas insumos']);
        Permission::create(['name' => 'editar facturas insumos']);
        Permission::create(['name' => 'eliminar facturas insumos']);
        Permission::create(['name' => 'ver facturas insumos']);
        
        #Kardex de insumos
        Permission::create(['name' => 'crear kardex insumos']);
        Permission::create(['name' => 'editar kardex insumos']);
        Permission::create(['name' => 'eliminar kardex insumos']);
        Permission::create(['name' => 'ver kardex insumos']);
    
            #seccion de ordenes
        Permission::create(['name' => 'crear ordenes']);
        Permission::create(['name' => 'editar ordenes']);
        Permission::create(['name' => 'eliminar ordenes']);
        Permission::create(['name' => 'ver ordenes']);
                #seccion de Ventas
        Permission::create(['name' => 'crear ventas']);
        Permission::create(['name' => 'editar ventas']);
        Permission::create(['name' => 'eliminar ventas']);
        Permission::create(['name' => 'ver ventas']);

                # Reporte de Caja
        Permission::create(['name' => 'ver reporte_cajas']);
        Permission::create(['name' => 'editar reporte_cajas']);

    }
}
