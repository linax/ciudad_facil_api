<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Iatstuti\Database\Support\CascadeSoftDeletes;
use DB;
use JWTAuth;

class PuntoEvaluacion extends Model {

    use SoftDeletes, CascadeSoftDeletes;

    protected $table = 'punto_evaluacion';

    protected $primaryKey = 'pev_id';

    protected $fillable = [
        'pev_titulo',
        'pev_tipo',
        'usu_id',
        'com_id',
        'pev_latitud',
        'pev_longitud',
        'pev_imagen',
        'pev_votos_positivos',
        'pev_votos_negativos',
        'pev_evaluacion_ponderada',
        'pev_evaluacion_items',
        'usuario_id',
        'comuna_id'
     ];

    public static $field_mask = [
        'pev_ttl' => 'pev_titulo',
        'pev_tpo' => 'pev_tipo',
        'usu_id' => '',
        'com_id' => '',
        'pev_ltt' => 'pev_latitud',
        'pev_lng' => 'pev_longitud',
        'pev_img' => 'pev_imagen',
        'pev_vts_pst' => 'pev_votos_positivos',
        'pev_vts_ngt' => 'pev_votos_negativos',
        'pev_evl_pnd' => 'pev_evaluacion_ponderada',
     ];

    protected $dates = [
        'deleted_at'
    ];

    protected $casts = [
        'pev_evaluacion_items' => 'collection'
    ];

    public static $rules = [
        'pev_titulo' => 'required',
        'pev_tipo' => 'required',
        'usu_id' => 'numeric',
        'com_id' => 'numeric',
        'pev_latitud' => 'required',
        'pev_longitud' => 'required',
        'pev_votos_positivos' => 'numeric',
        'pev_votos_negativos' => 'numeric',
        'usuario_id' => 'required|numeric',
        'comuna_id' => 'required|numeric',
    ];

    protected $cascadeDeletes = [
        'estacionamiento',
        'edificacion',
        'espacio_publico',
        'vereda_cruce',
        'evaluacion_usuario_punto_evaluacion'
    ];

    public static $tipo_literal = [
        1 => 'estacionamiento',
        2 => 'edificacion',
        3 => 'espacio_publico',
        4 => 'vereda_cruce'
    ];

    public static $accesibilidad_literal = [
        0 => 'Mala',
        1 => 'Media',
        2 => 'Buena'
    ];

    /*
     * relaciones
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'pev_usu_id');
    }

    public function tipo_punto_evaluacion()
    {
        return $this->belongsTo(TipoPuntoEvaluacion::class, 'pev_tpe_id');
    }

    public function usuario_votacion()
    {
        return $this->belongsToMany(Usuario::class, 'votacion_usuario_peval', 'vot_pev_id', 'vot_usu_id')->withTimestamps()->withPivot('vot_intencion_votacion');
    }

    public function usuario_comentario()
    {
        return $this->belongsToMany(Usuario::class, 'comentario_usuario_peval', 'com_pev_id', 'com_usu_id')->withTimestamps()->withPivot('com_comentario');
    }

    public function comuna()
    {
        return $this->belongsTo(Comuna::class, 'pev_com_id');
    }

    public function estacionamiento()
    {
        return $this->hasOne(Estacionamiento::class, 'est_pev_id');
    }

    public function edificacion()
    {
        return $this->hasOne(Edificacion::class, 'edi_pev_id');
    }

    public function espacio_publico()
    {
        return $this->hasOne(EspacioPublico::class, 'esp_pev_id');
    }

    public function vereda_cruce()
    {
        return $this->hasOne(VeredaCruce::class, 'vcr_pev_id');
    }

    public function evaluacion_usuario_punto_evaluacion()
    {
        return $this->hasMany(EvaluacionUsuarioPuntoEvaluacion::class, 'eup_pev_id');
    }


    /**
     * El usuario autenticado puede evaluar el punto de evaluación
     *
     * @return boolean
     */
    public function editable(){
       return !(EvaluacionUsuarioPuntoEvaluacion::where('eup_usu_id', JWTAuth::toUser()->usu_id)->where('eup_pev_id', $this->pev_id)->count() > 0);
    }

    /**
     * Entrega el nombre de la subcategoría que tiene el punto, sino tiene: nulo
     *
     * @return String nombre de la subcategoría
     */
    public function subCategoria()
    {
        switch ($this->pev_tipo) {
            case 2:
                return $this->edificacion->subcategoria_edificacion->sub_nombre;
            case 3:
                return $this->espacio_publico->subcategoria_espacio_publico->ces_nombre;
            default:
                return null;
        }
    }

    /**
     * Obtiene la ponderación para un ítem en espacífico de acuerdo al tipo entregado
     * y la colección de evaluaciones realizadas sobre él
     *
     * Uso: ponderacionItems()
     *
     * @param $valor_item_punto_evaluacion    Collection de valor de evaluaciones
     *
     * @return Integer
     */
    public function ponderacionItemIndividual($valor_item_punto_evaluacion)
    {
        $numerador  = $valor_item_punto_evaluacion->sum('vie_valor');
        $denominador = $valor_item_punto_evaluacion->count();
        if ($denominador > 0) {
            $ponderacion = floor($numerador / $denominador);
            if (($numerador/$denominador) - $ponderacion > 0.7) {
                $ponderacion++;
            }
            return $ponderacion;
        } else {
            $ponderacion = null;
        }
    }

    /**
     * Pondera las evaluaciones de forma para cada criterio de evaluación de un punto
     * Se entregan los valores en una colección de evaluaciones
     *
     * @return Collection
     */
    public function ponderaItems()
    {
        switch ($this->pev_tpe_id) {
            case 2: $filtro = 'edi';break;
            case 3: $filtro = 'esp';break;
            case 4:
            case 5:
            switch ($this->vereda_cruce->vcr_tipo) {
                case 1: $filtro = 'ver';break;
                case 2: $filtro = 'cru';break;
            }
        }

        $items_evaluar = ItemEvaluar::where('iev_nombre_dato', 'like', $filtro.'_%')->get();

        $valor_item_punto_evaluaciones = $this->evaluacion_usuario_punto_evaluacion
            ->flatMap(function($evaluacion_usuario_punto_evaluacion){
                return $evaluacion_usuario_punto_evaluacion->valor_item_punto_evaluacion;
            })
            ->whereIn('vie_iev_id',$items_evaluar->pluck('iev_id')->toArray())
            ->flatten();

        //pondera los valores para cada item para todas las votaciones
        $this->pev_evaluacion_items = $items_evaluar
            ->flatMap(function($item_evaluar) use ($valor_item_punto_evaluaciones){
                $valor_item_punto_evaluacion = $valor_item_punto_evaluaciones->where('vie_iev_id', $item_evaluar->iev_id);
                return [$item_evaluar->iev_nombre_dato => $this->ponderacionItemIndividual($valor_item_punto_evaluacion)];
            });

        $this->save();

        return true;
    }

    /**
     * Ponderación final sobre el punto teniéndo en cuenta las ponderaciones por item
     * Se aplica distribución/lógica de evaluaciones de acuerdo al tipo de punto
     *
     * @param Collection    Colección de ponderaciones por item
     * @return integer      Valor de la evaluación final ponderada de 0 a 2
     */
    public function ponderaFinal()
    {
        if (empty($this->pev_evaluacion_items)) return null;

        $suma_ponderaciones = $this->pev_evaluacion_items->sum();

        if ($this->pev_evaluacion_items->reject(function($value){ return $value === null;})->count() == 0){
            $this->pev_evaluacion_ponderada = null;
            $this->save();
            return null;
        }

        $ponderacion_final = null;

        switch ($this->pev_tpe_id) {
            case 2://edificacion
                $ponderaciones = $this->pev_evaluacion_items->toArray();
                $acc_mov = $ponderaciones['edi_acc'] + $ponderaciones['edi_mov'];
                switch ($acc_mov) {
                    case 0:
                        $ponderacion_final  = 0;
                        break;
                    case 1:
                    case 2:
                        if (( empty($ponderaciones['edi_bat']) || $ponderaciones['edi_bat'] < 2) && empty($ponderaciones['edi_sec'])){
                            $acc_mov--;
                        }
                        $ponderacion_final = $acc_mov;
                        break;
                }
                break;
            case 3://espacio público
                switch ($suma_ponderaciones) {
                    case 0:
                        $ponderacion_final = 0;
                        break;
                    case 1:
                    case 2:
                    case 3:
                        $ponderacion_final = 1;
                        break;
                    case 4:
                        $ponderacion_final = 2;
                        break;
                }
                break;
            case 4://vereda_cruce
                switch ($suma_ponderaciones) {
                    case 0:
                        $ponderacion_final = 0;
                        break;
                    case 1:
                    case 2:
                        $ponderacion_final = 1;
                        break;
                    case 3:
                    case 4:
                    case 5:
                        $ponderacion_final = 2;
                        break;
                }
                break;
            case 5://cruce
                $ponderaciones = $this->pev_evaluacion_items->toArray();
                //retorna 0 de forma directa para el caso de condición necesaria de existencia de rampa
                if (empty($ponderaciones['cru_rmp'])) {
                    $ponderacion_final = 0;
                } else {
                    switch ($suma_ponderaciones) {
                        case 0:
                            $ponderacion_final = 0;
                            break;
                        case 1:
                        case 2:
                        case 3:
                        case 4:
                            $ponderacion_final = 1;
                            break;
                        case 5:
                        case 6:
                        case 7:
                            $ponderacion_final = 2;
                            break;
                    }
                }
                break;
        }

        $this->pev_evaluacion_ponderada = $ponderacion_final;

        $this->save();

        return $this->pev_evaluacion_ponderada;
    }

    /**
     * Retorna todos los puntos de evaluación agrupados por comuna, filtrando
     * comunas por mínimo de evaluaciones de acuerdo a EVALUACIONES_MINIMAS
     * y ordenados por porcentaje de ponderación
     *
     * Cuenta con selección y exclusión de columnas para una carga más
     * liviana de datos en with y exclude respectivamente
     *
     * GET [plataforma]
     *
     * @param  $puntos boolean Indica si los puntos de evaluación son entregados
     * @param  $minimas integer Numero de evaluaciones mínimo para considerar datos
     * @return mixed
     */
    public static function porComuna($puntos = false, $minimas = true)
    {
        return Comuna::has('punto_evaluacion', '>=', ($minimas === true) ? config('constants.evaluaciones_minimas') : $minimas)
            ->with(['punto_evaluacion' => function($query){
                $query->select('pev_tpe_id', 'pev_com_id', 'pev_evaluacion_ponderada')
                        ->where('pev_evaluacion_ponderada', '!=', null);
            }])
            ->exclude(['geom', 'shape_leng', 'shape_area', 'shape_le_1', 'dis_elec', 'cir_sena', 'cod_comuna'])
            ->get()
            ->map(function($comuna) use ($puntos){
                $comuna['tasa_accs'] = round($comuna->punto_evaluacion->average('pev_evaluacion_ponderada') * 100 / 2, 2);
                $comuna['eval_count'] = $comuna->punto_evaluacion->count();
                if (!$puntos) unset($comuna->punto_evaluacion); //se quitan los puntos de evlauación, puede existir una forma más elegante
                return $comuna;
            })
            ->sortByDesc('tasa_accs')
            ->flatten();
    }

    /**
     * Retorna todos los puntos de evaluación agrupados por región, filtrando
     * comunas por mínimo de evaluaciones de acuerdo a EVALUACIONES_MINIMAS
     * y ordenados por porcentaje de ponderación
     *
     * Cuenta con selección y exclusión de columnas para una carga más
     * liviana de datos en with y exclude respectivamente
     *
     * GET [plataforma]
     *
     * @param  $puntos boolean Indica si los puntos de evaluación son entregados
     * @param  $minimas integer Numero de evaluaciones mínimo para considerar datos
     * @return mixed
     */
    public static function porRegion($puntos = false, $minimas = true)
    {
        return Region::all()->each(function($region){
            unset($region->comuna);
            return $region['puntos_evaluacion'] = $region->comuna->flatMap(function($comuna){
                return $comuna->punto_evaluacion;
            });
        })
        ->filter(function ($region) use ($minimas) {
            if ($minimas) {
                return $region->puntos_evaluacion->count() > config('constants.evaluaciones_minimas');
            } else {
                return true;
            }
        })
        ->map(function($region) use ($puntos){
            $region['tasa_accs'] = round($region->puntos_evaluacion->average('pev_evaluacion_ponderada') * 100 / 2, 2);
            $region['eval_count'] = $region->puntos_evaluacion->count();
            if (!$puntos) unset($region->puntos_evaluacion); //se quitan los puntos de evlauación, puede existir una forma más elegante
            return $region;
        })
        ->sortByDesc('tasa_accs')
        ->flatten();
    }

    /**
     * Retorna los puntos según tipo, tipo sector, sector e item de evaluación
     *
     * GET [plataforma]
     *
     * @param  $tipo_punto_literal
     * @param  $sector
     * @param  $categoria
     * @param  $tipo_sector
     * @param  $item
     * @return json 			Datos del estacionamiento
     */
    public static function porTipoFiltro($tipo_punto_literal, $sector = 0, $categoria = 0, $tipo_sector, $item = null)
    {
        return static::whereHas($tipo_punto_literal, function($query) use ($tipo_punto_literal, $categoria){
            switch ($tipo_punto_literal){
                case 'edificacion':
                    $query->join('subcategoria_edificacion', function ($join) use ($categoria){
                        $join->on('edificacion.edi_sub_id', '=', 'subcategoria_edificacion.sub_id')
                            ->when($categoria, function($query) use ($categoria) {
                                $query->where('subcategoria_edificacion.sub_cat_id', '=', $categoria);
                            });
                    });
                    break;
                case 'espacio_publico':
                    $query->join('subcategoria_espacio_publico', function ($join) use ($categoria){
                        $join->on('espacio_publico.esp_ces_id', '=', 'subcategoria_espacio_publico.ces_id')
                            ->when($categoria, function($query) use ($categoria) {
                            $query->where('subcategoria_espacio_publico.ces_cep_id', '=', $categoria);
                        });
                    });
                    break;
                case 'vereda_cruce':
                    $query->when($categoria, function($query) use ($categoria) {
                        $query->where('vereda_cruce.vcr_tipo', '=', $categoria);
                    });
                    break;
            }
        })
        ->when($sector, function ($query) use ($sector, $categoria, $tipo_sector, $item) {
            switch ($tipo_sector) {
                case 1:
                    $query->whereIn('pev_com_id', Region::find($sector)->comuna->pluck('com_id')->toArray());
                    break;
                case 2:
                    $query->where('pev_com_id', $sector);
                    break;
                case 3:
                    $data_sector = json_decode($sector)->coordinates[0];
                    $poligono = '';
                    for ($i = 0; $i < sizeof($data_sector); $i++) {
                        $poligono = $poligono . (string)$data_sector[$i][0] . ' ' . (string)$data_sector[$i][1] . ', ';
                    }
                    $query->leftJoin('comuna', 'punto_evaluacion.pev_com_id', '=', 'comuna.com_id')
                        ->whereRaw("ST_Intersects(comuna.geom,ST_Transform(ST_GeomFromText('POLYGON((" . substr($poligono, 0, -2) . "))' , 4326), 32719))");
                    break;
            }
            if (!empty($item)){
                $query->join('evaluacion_usuario_punto_evaluacion', 'punto_evaluacion.pev_id', 'IN', ValorItemPuntoEvaluacion::find($item)->evaluacion_usuario_punto_evaluacion()->get(['eup_id'])->pluck('eup_id'));
            }
        })
        ->get();
    }

    public static function paraFiltroExcel($tipo_punto_literal, $sector = 0, $categoria = 0, $tipo_sector){
      return static::whereHas($tipo_punto_literal, function($query) use ($tipo_punto_literal, $categoria){
          switch ($tipo_punto_literal){
              case 'edificacion':
                  $query->join('subcategoria_edificacion', function ($join) use ($categoria){
                      $join->on('edificacion.edi_sub_id', '=', 'subcategoria_edificacion.sub_id')
                          ->when($categoria, function($query) use ($categoria) {
                              $query->where('subcategoria_edificacion.sub_cat_id', '=', $categoria);
                          });
                  });
                  break;
              case 'espacio_publico':
                  $query->join('subcategoria_espacio_publico', function ($join) use ($categoria){
                      $join->on('espacio_publico.esp_ces_id', '=', 'subcategoria_espacio_publico.ces_id')
                          ->when($categoria, function($query) use ($categoria) {
                          $query->where('subcategoria_espacio_publico.ces_cep_id', '=', $categoria);
                      });
                  });
                  break;
              case 'vereda_cruce':
                  $query->when($categoria, function($query) use ($categoria) {
                      $query->where('vereda_cruce.vcr_tipo', '=', $categoria);
                  });
                  break;
          }
      })
      ->when($sector, function ($query) use ($sector, $categoria, $tipo_sector) {
          switch ($tipo_sector) {
              case 1:
                  $query->whereIn('pev_com_id', Region::find($sector)->comuna->pluck('com_id')->toArray());
                  break;
              case 2:
                  $query->where('pev_com_id', $sector);
                  break;
              case 3:
                  $data_sector = json_decode($sector)->coordinates[0];
                  $poligono = '';
                  for ($i = 0; $i < sizeof($data_sector); $i++) {
                      $poligono = $poligono . (string) $data_sector[$i][0] . ' ' . (string)$data_sector[$i][1] . ', ';
                  }
                  $query->leftJoin('comuna', 'punto_evaluacion.pev_com_id', '=', 'comuna.com_id')
                      ->whereRaw("ST_Intersects(comuna.geom,ST_Transform(ST_GeomFromText('POLYGON((" . substr($poligono, 0, -2) . "))' , 4326), 32719))");
                  break;
          }
      })
        ->leftJoin('comuna', 'pev_com_id', 'com_id')
        ->leftJoin('provincia', 'com_pro_id', 'pro_id')
        ->leftJoin('region', 'pro_reg_id', 'reg_id')
        ->leftJoin('evaluacion_usuario_punto_evaluacion', 'eup_pev_id', 'pev_id')
        ->select('punto_evaluacion.*', 'com_nombre', 'pro_nombre', 'reg_nombre', 'eup_comentario')
        ->get();
    }

    /**
     * Retorna todos los puntos de evaluación agrupados por comuna, filtrando
     * comunas por mínimo de evaluaciones de acuerdo a EVALUACIONES_MINIMAS
     * y ordenados por porcentaje de ponderación
     *
     *
     * GET [plataforma]
     *
     * @param  $puntos boolean Indica si los puntos de evaluación son entregados
     * @param  $minimas integer Numero de evaluaciones mínimo para considerar datos
     * @return mixed
     */
    public static function porComunaEvaluacion($tipo_punto_literal, $tipo_sector,  $sector = 0, $categoria = 0)
    {
        return Comuna::has('punto_evaluacion', '>=', config('constants.evaluaciones_minimas') )
            ->with(['punto_evaluacion' => function($query) use($tipo_punto_literal, $categoria){
              $query->whereHas($tipo_punto_literal, function($query) use ($tipo_punto_literal, $categoria){
                  switch ($tipo_punto_literal) {
                    case 'edificacion':
                      $query->join('subcategoria_edificacion', function ($join) use ($categoria){
                          $join->on('edificacion.edi_sub_id', '=', 'subcategoria_edificacion.sub_id')
                              ->when($categoria, function($query) use ($categoria) {
                                  $query->where('subcategoria_edificacion.sub_cat_id', '=', $categoria);
                              });
                      });
                      break;

                    case 'espacio_publico':
                      $query->join('subcategoria_espacio_publico', function ($join) use ($categoria){
                          $join->on('espacio_publico.esp_ces_id', '=', 'subcategoria_espacio_publico.ces_id')
                              ->when($categoria, function($query) use ($categoria) {
                              $query->where('subcategoria_espacio_publico.ces_cep_id', '=', $categoria);
                          });
                      });
                      break;
                    case 'vereda_cruce':
                      $query->when($categoria, function($query) use ($categoria) {
                          $query->where('vereda_cruce.vcr_tipo', '=', $categoria);
                      });
                      break;
                  }
              $query->select('pev_tpe_id', 'pev_com_id', 'pev_evaluacion_ponderada')
                      ->where('pev_evaluacion_ponderada', '!=', null);
              });
            }])
            ->when($sector, function($query) use ($tipo_sector, $sector){
              switch ($tipo_sector) {
                case 1:
                  $query->join('provincia', 'com_pro_id', 'pro_id')
                      ->where('pro_reg_id', '=', $sector);
                  break;

                case 2:
                  $query->where('com_id', $sector);
                  break;
              }
            })
            ->exclude(['geom', 'shape_leng', 'shape_area', 'shape_le_1', 'dis_elec', 'cir_sena', 'cod_comuna'])
            ->get();
    }

    public static function porRegionEvaluacion($tipo_punto_literal, $tipo_sector,  $sector = 0, $categoria = 0){
      return Region::with('comuna.punto_evaluacion')
              ->when($sector, function($query) use ($sector){
                $query->where('reg_id', '=', $sector);
              })
              ->with(['comuna.punto_evaluacion' => function($query) use($tipo_punto_literal, $categoria){
                $query->whereHas($tipo_punto_literal, function($query) use ($tipo_punto_literal, $categoria){
                    switch ($tipo_punto_literal) {
                      case 'edificacion':
                        $query->join('subcategoria_edificacion', function ($join) use ($categoria){
                            $join->on('edificacion.edi_sub_id', '=', 'subcategoria_edificacion.sub_id')
                                ->when($categoria, function($query) use ($categoria) {
                                    $query->where('subcategoria_edificacion.sub_cat_id', '=', $categoria);
                                });
                        });
                        break;

                      case 'espacio_publico':
                        $query->join('subcategoria_espacio_publico', function ($join) use ($categoria){
                            $join->on('espacio_publico.esp_ces_id', '=', 'subcategoria_espacio_publico.ces_id')
                                ->when($categoria, function($query) use ($categoria) {
                                $query->where('subcategoria_espacio_publico.ces_cep_id', '=', $categoria);
                            });
                        });
                        break;
                      case 'vereda_cruce':
                        $query->when($categoria, function($query) use ($categoria) {
                            $query->where('vereda_cruce.vcr_tipo', '=', $categoria);
                        });
                        break;
                    }
                $query->select('pev_tpe_id', 'pev_com_id', 'pev_evaluacion_ponderada')
                        ->where('pev_evaluacion_ponderada', '!=', null);
                });
              }])
              ->get();
    }
}
