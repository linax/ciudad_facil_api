<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Auth\Authenticatable;
//use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
//use Tymon\JWTAuth\Contracts\JWTSubject;
use Jrean\UserVerification\Traits\UserVerification;
use Illuminate\Notifications\Notifiable;


class Usuario extends Model implements /*JWTSubject,*/ AuthenticatableContract, /*AuthorizableContract,*/ CanResetPasswordContract
{
    use Authenticatable,/* Authorizable,*/UserVerification, SoftDeletes, CascadeSoftDeletes, CanResetPassword, Notifiable;

    protected $table = 'usuario';

    protected $primaryKey = 'usu_id';

    protected $fillable = [	
		'email',
		'usu_nombre_usuario',
		'password',
		'usu_salt',
		'usu_facebook_id',
		'usu_facebook_token',
		'usu_twitter_id',
		'usu_twitter_token',
		'usu_nombres',
		'usu_apellido_paterno',
		'usu_apellido_materno',
		'usu_discapacidad',
		'usu_tipo_discapacidad',
		'verified'
	];

	public static $field_mask = [
		'usu_eml' => 'email', 
		'usu_nbr_usr' => 'usu_nombre_usuario',
		'usu_pss' => 'password',
		'usu_pss_confirmation' => 'password_confirmation',
		'usu_fcb_id' => 'usu_facebook_id',
		'usu_fcb_tkn' => 'usu_facebook_token',
		'usu_twt_id' => 'usu_twitter_id',
		'usu_twt_tkn' => 'usu_twitter_token',
		'usu_nmb' => 'usu_nombres',
		'usu_apl_ptr' => 'usu_apellido_paterno',
		'usu_apl_mtr' => 'usu_apellido_materno',
		'usu_dsc' => 'usu_discapacidad',
		'usu_tpo_dsc' => 'usu_tipo_discapacidad'
	];

    protected $dates = ['deleted_at'];

    protected $cascadeDeletes = [
        'usuario_social'
    ];

    public $timestamps = true;

    public function scopeByEmail($query, $email)
    {
    	return $query->where('email', $email)->firstOrFail();
    }

    /*
     * mutators
     */
    public function setUsuEmailAttribute($value)
    {
    	return $this->attributes['email'] = $value;
    }

    public function setUsuPasswordAttribute($value)
    {
    	return $this->attributes['password'] = $value;
    }

    /*
     * accessors
     */
    public function getUsuEmailAttribute()
    {
    	return $this->email;
    }

    public function getUsuNombreCompletoAttribute()
    {
        if ($this->usu_nombres && $this->usu_apellido_paterno) {
            return $this->usu_nombres.' '.$this->usu_apellido_paterno.' '.$this->usu_apellido_materno;
        } elseif (!empty($this->usuario_social()->first())) {
            return $this->usuario_social()->first()->uss_nombre_completo;
        } else {
            return null;
        }
    }

    public function getUsuPasswordAttribute()
    {
    	return $this->password;
    }

    /**
     * [getJWTIdentifier description]
     * @return [type] [description]
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /*
     * relaciones
     */
    public function punto_evaluacion()
    {
        return $this->hasMany(PuntoEvaluacion::class, 'pev_usu_id');
    }

    public function usuario_social()
    {
        return $this->hasMany(UsuarioSocial::class, 'uss_usu_id');
    }

    public function punto_evaluacion_votacion()
    {
        return $this->belongsToMany(PuntoEvaluacion::class, 'votacion_usuario_peval', 'vot_usu_id', 'vot_pev_id')->withTimestamps()->withPivot('vot_intencion_votacion');
    }

    public function punto_evaluacion_comentario()
    {
        return $this->belongsToMany(PuntoEvaluacion::class, 'comentario_usuario_peval', 'com_usu_id', 'com_pev_id')->withTimestamps()->withPivot('com_comentario');
    }
}
