<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		//factory(App\Usuario::class, 10)->create(); //crea 10 usuarios
		/*factory(App\Region::class, 8)->create(); //crea 10 regiones
		factory(App\Provincia::class, 8)->create();
		factory(App\Comuna::class, 8)->create();
		factory(App\PuntoEvaluacion::class, 75)->create();*/

		DB::table('item_evaluar')->insert(['iev_descripcion' => 'Evaluación acceso edificación']);
        DB::table('item_evaluar')->insert(['iev_descripcion' => 'Evaluación desplazamiento edificación']);
        DB::table('item_evaluar')->insert(['iev_descripcion' => 'Evaluación baño edificación']);
        DB::table('item_evaluar')->insert(['iev_descripcion' => 'Evaluación seguridad edificación']);
        DB::table('item_evaluar')->insert(['iev_descripcion' => 'Evaluación acceso información edificación']);

    }
}
