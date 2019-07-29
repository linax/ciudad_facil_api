<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model {

	protected $table = 'region';

	protected $primaryKey = 'reg_id';

    protected $fillable = ["reg_nombre", "reg_orden"];

    /**
     * relations
     */
    public function provincia()
    {
        return $this->hasMany(Provincia::class, 'pro_reg_id');
    }

    public function comuna(){
        return $this->hasManyThrough(Comuna::class, Provincia::class, 'pro_reg_id', 'com_pro_id', 'reg_id')
            ->exclude(['geom', 'shape_le_1', 'shape_area', 'cod_comuna', 'dis_elec', 'cir_sena', 'shape_leng']);
    }
}
