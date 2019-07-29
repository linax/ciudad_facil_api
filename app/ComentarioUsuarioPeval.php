<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComentarioUsuarioPeval extends Model
{
    use SoftDeletes;

    protected $table = 'comentario_usuario_peval';

    protected $primaryKey = 'com_id';

    protected $fillable = [
        'com_comentario'
    ];

    public static $field_mask = [
        'com_usu_pev' => 'com_comentario'
    ];

    protected $dates = [
        'delete_at'
    ];

    /*
     * relaciones
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'com_usu_id');
    }
}
