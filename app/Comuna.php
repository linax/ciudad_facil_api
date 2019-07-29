<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Excludable;
use DB;

class Comuna extends Model {

    use Excludable;

    protected $table = 'comuna';

    protected $primaryKey = 'com_id';

    protected $fillable = ["com_nombre", "com_orden"];

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'com_pro_id');
    }

    public function punto_evaluacion()
    {
        return $this->hasMany(PuntoEvaluacion::class, 'pev_com_id');
    }

    public static function fromLongLat($longitud, $latitud)
    {
        return Comuna::whereRaw("ST_Contains(comuna.geom,ST_Transform(ST_GeomFromText('POINT($longitud $latitud)', 4326), 32719))")->first();
    }

    /**
     * Retorna todos los puntos de evaluación agrupados por comuna
     */
    public static function puntosPorComuna()
    {
        return Comuna::has('punto_evaluacion')->exclude(['geom', 'shape_leng', 'shape_area', 'shape_le_1'])->get();
    }
}
