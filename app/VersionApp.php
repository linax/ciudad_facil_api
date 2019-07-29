<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class VersionApp extends Model 
{
	protected $table = 'version_app';

	protected $primaryKey = 'ver_id';

    protected $fillable = [];

    protected $dates = ['ver_fecha_app'];

}
