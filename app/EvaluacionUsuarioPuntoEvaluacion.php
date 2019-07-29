<?php

namespace App;

use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Image;

class EvaluacionUsuarioPuntoEvaluacion extends Model
{
    use SoftDeletes, CascadeSoftDeletes;

    protected $table = 'evaluacion_usuario_punto_evaluacion';

    protected $primaryKey = 'eup_id';

    protected $fillable = [
        'eup_comentario'
    ];

    public static $field_mask = [
        'eup_cmn' => 'eup_comentario'
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $cascadeDeletes = [
        'imagen_evaluacion'
    ];

    /*
     * relaciones
     */
    public function valor_item_punto_evaluacion()
    {
        return $this->belongsToMany(ValorItemPuntoEvaluacion::class, 'valor_evaluacion', 'vae_eup_id', 'vae_vie_id')->withTimestamps();
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'eup_usu_id');
    }

    public function punto_evaluacion()
    {
        return $this->belongsTo(PuntoEvaluacion::class, 'eup_pev_id');
    }

    public function imagen_evaluacion()
    {
        return $this->hasMany(ImagenEvaluacion::Class, 'ima_eup_id');
    }

    /**
     * Asociar una collección de imágenes
     *
     * @param  $imagenes coleccion de input imágenes de la petición en formato base64
     * @return [type] [description]
     */
    public function asociar_imagenes($imagenes)
    {
        $path = storage_path(env('EVALUACION_USUARIO_PUNTO_EVALUACION_IMG_PATH').Carbon::now()->year.'/'.Carbon::now()->month);
        File::isDirectory($path) or File::makeDirectory($path, 0775, true, true);

        $imagenes->map(function($input_base64, $key) use ($path){

            $archivo_imagen = Image::make(file_get_contents($input_base64));
            $path_archivo = $path.time().'-'.$key.'.'.config('extensions')[$archivo_imagen->mime];
            $archivo_imagen->save($path_archivo);

            $imagen_evaluacion = new ImagenEvaluacion();
            $imagen_evaluacion->ima_imagen = $path_archivo;
            $imagen_evaluacion->evaluacion_usuario_punto_evaluacion()->associate($this);
            $imagen_evaluacion->save();

        });
    }

    /**
     * Retornar última imagen
     *
     * @return [string] String en base64 de imagen
     */
    public function ultima_imagen()
    {
        $imagen = $this->imagen_evaluacion->last();
        if (empty($imagen)) return [];
        return [
            (file_exists($imagen->ima_imagen))? 'data:image/' . pathinfo($imagen->ima_imagen, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($imagen->ima_imagen)) : null
        ];
    }
}
