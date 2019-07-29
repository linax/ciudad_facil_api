<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model {

    protected $table = 'provincia';

    protected $primaryKey = 'pro_id';

    protected $fillable = ["reg_id", "pro_nombre", "pro_orden", "region_id"];

    /*protected $dates = [];

    public static $rules = [
        "reg_id" => "numeric",
        "pro_nombre" => "required",
        "pro_orden" => "required",
        "region_id" => "required|numeric",
    ];*/

    public function region()
    {
        return $this->belongsTo(Region::class, 'pro_reg_id');
    }

    public function comuna()
    {
        return $this->hasMany(Comuna::class, 'com_pro_id', 'pro_id');
    }


}
