<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioSocial extends Model
{
    use SoftDeletes;

    protected $table = 'usuario_social';

    protected $primaryKey = 'usu_scl_id';

    protected $fillable = [
    	'uss_usu_id',
    	'uss_proveedor_usu_id',
    	'uss_proveedor',
        'uss_nombre_completo'
    ];

    public function usuario()
    {
    	return $this->belongsTo(Usuario::class, 'uss_usu_id');
    }

}
