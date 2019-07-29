<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImagenEstacionamiento extends Model
{
    use SoftDeletes;

    protected $table = 'imagen_estacionamiento';
    
    protected $primaryKey = 'ime_id';

    protected $fillable = [
    	'ime_imagen'
    ];

    public static $field_mask = [
    ];

    protected $dates = [
    	'deleted_at'
    ];

    public function estacionamiento()
    {
    	return $this->belongsTo(Estacionamiento::class, 'ime_est_id');
    }
}
