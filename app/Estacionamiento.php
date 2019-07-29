<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Iatstuti\Database\Support\CascadeSoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Image;

class Estacionamiento extends Model
{
	use SoftDeletes, CascadeSoftDeletes;

    protected $table = 'estacionamiento';

    protected $primaryKey = 'est_id';

    protected $fillable = [
    	'est_cantidad_total',
    	'est_cantidad_reservados',
    	'est_cumple_ordenanza',
    	'est_comentario'
    ];

    public static $field_mask = [
    	'est_cnt_ttl' => 'est_cantidad_total',
    	'est_cnt_rsv' => 'est_cantidad_reservados',
    	'est_cmp_ord' => 'est_cumple_ordenanza',
    	'est_cmn' => 'est_comentario'
    ];

    protected $dates = [
    	'deleted_at'
    ];

    protected $cascadeDeletes = [
        'imagen_estacionamiento'
    ];

    public function punto_evaluacion()
    {
    	return $this->belongsTo(PuntoEvaluacion::class, 'est_pev_id');
    }

    public function imagen_estacionamiento()
    {
    	return $this->hasMany(ImagenEstacionamiento::class, 'ime_est_id');
    }

    /**
     * Asociar una collección de imágenes al estacionamiento
     *
     * @param  $imagenes coleccion de input imágenes de la petición en formato base64
     * @return [type] [description]
     */
    public function asociar_imagenes($imagenes)
    {
        $path = storage_path(env('ESTACIONAMIENTO_IMG_PATH').Carbon::now()->year.'/'.Carbon::now()->month);
        File::isDirectory($path) or File::makeDirectory($path, 0775, true, true);

        $imagenes->map(function($input_base64, $key) use ($path){
            $archivo_imagen = Image::make(file_get_contents($input_base64));
            $path_archivo = $path.time().'-'.$key.'.'.config('extensions')[$archivo_imagen->mime];
            $archivo_imagen->save($path_archivo);

            $imagen_estacionamiento = new ImagenEstacionamiento();

            $imagen_estacionamiento->ime_imagen = $path_archivo;
            $imagen_estacionamiento->estacionamiento()->associate($this);
            $imagen_estacionamiento->save();

        });
    }

    /**
     * Retornar última imagen
     *
     * @return [string] String en base64 de imagen
     */
    public function ultima_imagen()
    {
        $imagen = $this->imagen_estacionamiento->last();
        return [
            (file_exists($imagen->ime_imagen))? 'data:image/' . pathinfo($imagen->ime_imagen, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($imagen->ime_imagen)) : null
        ];
    }

}
