<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Asistencias_Departamentos;
use App\Models\Asistencia_Eventos;
use App\Models\Evento;
use App\Models\Tipo_Asistencia;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    private $ubicacionCtrl;
    private $eventnCtrl;
    private $geoDepCtrl;

    public function __construct()
    {
        $this->ubicacionCtrl = new UbicacionController();
        $this->eventnCtrl = new EventoController();
        $this->geoDepCtrl = new Geolocalizacion_DepartamentoController();
    }

    public function cargarTipoAsistencia()
    {
        $response = [];
        $tipo_asistencia = Tipo_Asistencia::all();

        if ($tipo_asistencia->count() > 0) {
            $response = ['status' => true, 'message' => 'Tipos de asistencia cargadas con éxito.', 'data' => $tipo_asistencia];
        } else {
            $response = ['status' => false, 'message' => 'No hay tipos de Asistencia.', 'data' => null];
        }

        return response()->json($response, 200);
    }

    public function buscarUltimoTipo($user_id)
    {
        $asistenciaUltimo = Asistencia::where('user_id', $user_id)->where('fecha', date('Y-m-d'))->get();

        if ($asistenciaUltimo->count() > 0) {
            foreach ($asistenciaUltimo as $item) {
                $ultimoTipo = $item->tipo_registro_id;
            }
        } else {
            $ultimoTipo = '';
        }
        return response()->json($ultimoTipo);
    }

    public function buscarUltimoTipoAsistencia($user_id)
    {
        //preguntar si existe un evento
        $evento = Evento::where('fecha', date('Y-m-d'))->get()->first();
        if ($evento) {
            $response = [
                'status' => true,
                'message' => 'Existe el evento ' . $evento->nombre,
                'data' => $evento,
                'tipo_asistencia_id' => 2,
            ];
        } else {
            //verificar si existe el usuario en la tabla asistencia
            $existeUserAsistencia = Asistencia::where('user_id', $user_id)->where('fecha', date('Y-m-d'))->get()->first();

            if ($existeUserAsistencia) {

                $asistenciaUltimo = Asistencia::where('user_id', $user_id)->where('fecha', date('Y-m-d'))->get();

                if ($asistenciaUltimo->count() > 0) {
                    foreach ($asistenciaUltimo as $item) {
                        $ultimoTipoAsistencia = $item->tipo_asistencia_id;
                    }

                    $response = [
                        'status' => true,
                        'message' => 'existe datos',
                        'tipo_asistencia_id' => $ultimoTipoAsistencia,
                    ];

                } else {
                    $response = [
                        'status' => false,
                        'message' => 'No hay datos para procesar',
                        'tipo_asistencia_id' => 0,
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Se va hacer el primer registro para el usuario',
                    'tipo_asistencia_id' => 1,
                ];
            }
        }
        return response()->json($response);
    }

    public function registrarAsistencia(Request $request)
    {
        $requestAsistencia = (object) $request->asistencia;
        $requestUbicaciones = (object) $request->ubicacion;
        $response = [];

        if ($requestAsistencia) {

            if ($requestAsistencia->tipo_asistencia_id === 1) { //asistencia

                $requUbicacion = $this->geoDepCtrl->validarGeolocalizacion($requestUbicaciones);

                if ($requUbicacion['status'] == true) { //Validar con las ubicaciones  de la table geolocalizacion
                    $departamento_id = $requUbicacion['ubicacion']->departamento_id;

                    $newAsistencia = $this->saveAsistencia($requestAsistencia);

                    $existeTipo = Asistencia::where('user_id', $requestAsistencia->user_id)
                        ->where('tipo_asistencia_id', '=', 1)
                        ->where('fecha', date('Y-m-d'))
                        ->get()->count();

                    if ($existeTipo === 4) {
                        $response = [
                            'status' => false,
                            'message' => 'Cumplio sus horas laborables el dia : ' . date('Y-m-d'),
                        ];
                    } else {
                        if ($newAsistencia->save()) {
                            $respUbicacion = $this->ubicacionCtrl->registrarUbicaciones($newAsistencia->id, $requestUbicaciones);

                            $registraAsitenciaDepartamento = new Asistencias_Departamentos();
                            $registraAsitenciaDepartamento->asistencia_id = $newAsistencia->id;
                            $registraAsitenciaDepartamento->departamento_id = $departamento_id;
                            $registraAsitenciaDepartamento->save();

                            $response = [
                                'status' => true,
                                'message' => 'La asistencia se registro correctamente',
                                'data' => [
                                    'asistencia' => $newAsistencia,
                                    'ubicacion' => $respUbicacion,
                                    'asistencia_departamento' => $registraAsitenciaDepartamento,
                                ],
                            ];
                        } else {
                            $response = [
                                'status' => false,
                                'message' => 'No se puede registrar la asistencia',
                                'data' => null,
                            ];
                        }
                    }
                } else {
                    $response = [
                        'status' => false,
                        'message' => $requUbicacion['message'],
                    ];
                }
            } else { //evento
                $returnEvento = $this->eventnCtrl->buscarEventos(date('Y-m-d'));

                if ($returnEvento['status'] === true) { //si hay eventos
                    $newAsistencia = $this->saveAsistencia($requestAsistencia);
                    $evento_id = $returnEvento['evento_id'];

                    $existeTipo = Asistencia::where('user_id', $requestAsistencia->user_id)
                        ->where('tipo_asistencia_id', '=', 2)
                        ->where('fecha', date('Y-m-d'))
                        ->get()->count();

                    if ($existeTipo === 4) {
                        $response = [
                            'status' => false,
                            'message' => 'Cumplio sus horas laborables el dia : ' . date('Y-m-d'),
                        ];
                    } else {
                        if ($newAsistencia->save()) {
                            $newAsistenciaEventos = new Asistencia_Eventos();
                            $newAsistenciaEventos->asistencia_id = $newAsistencia->id;
                            $newAsistenciaEventos->evento_id = $evento_id;
                            $newAsistenciaEventos->save();

                            $response = [
                                'status' => true,
                                'message' => 'La asistencia se registro correctamente',
                            ];
                        } else {
                            $response = [
                                'status' => false,
                                'message' => 'No se pudo registrar la asistencia',
                            ];
                        }
                    }
                } else { //no hay eventos
                    $response = [
                        'status' => false,
                        'message' => $returnEvento['message'],
                    ];
                }
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'No hay datos para procesar',
                'data' => null,
            ];
        }
        return response()->json($response);
    }

    public function getDateTime()
    {
        $response = [];
        $response = ['fecha' => date('Y-m-d'), 'hora' => date('H:i:s')];

        return response()->json($response);
    }

    private function saveAsistencia($requestAsistencia)
    {
        $newAsistencia = new Asistencia();
        $newAsistencia->user_id = $requestAsistencia->user_id;
        $newAsistencia->tipo_asistencia_id = $requestAsistencia->tipo_asistencia_id; //Tipo de Asistencia
        $newAsistencia->tipo_registro_id = $requestAsistencia->tipo_registro_id; //1 entrada
        $newAsistencia->fecha = date('Y-m-d');
        $newAsistencia->hora = date('H:i:s');
        $newAsistencia->estado = 'A';
        return $newAsistencia;
    }

    public function reporteTrabajador($user_id, $f_inicio, $f_fin, $tipo_asistencia_id)
    { //solo trabajador
        $response = [];

        $asistencias = Asistencia::where('estado', 'A')->where('user_id', intval($user_id))
            ->where('fecha', '>=', $f_inicio)->where('fecha', '<=', $f_fin)
            ->where('tipo_asistencia_id', intval($tipo_asistencia_id))->get();

        if (count($asistencias) > 0) {

            if (intval($tipo_asistencia_id) == 1) {
                foreach ($asistencias as $item) {
                    $item->user->persona;
                    $item->tipo_asistencia;
                    $item->tipo_registro;

                    foreach ($item->ubicacion as $ubi) {
                        $ubi;
                    }

                    foreach ($item->asistencias_departamento as $ad) {
                        $ad->departamento;
                    }
                }
                $response = [
                    'status' => true,
                    'message' => 'Existen registros de ' . $item->tipo_asistencia->type,
                    'data' => $asistencias,
                    'datos_personales' => [
                        'user' => $item->user,
                    ],
                ];
            } else {
                foreach ($asistencias as $item) {
                    $item->user->persona;
                    $item->tipo_asistencia;
                    $item->tipo_registro;

                    foreach ($item->asistencia_evento as $asev) {
                        $asev->evento;
                    }
                }

                $response = [
                    'status' => true,
                    'message' => 'Existen registro de ' . $item->tipo_asistencia->type,
                    'data' => $asistencias,
                    'datos_personales' => [
                        'user' => $item->user,
                    ],
                ];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'No existen registro',
                'data' => null,
            ];
        }
        return response()->json($response);
    }

    public function reporteSuperAdminAndAdministrador($f_inicio, $f_fin, $tipo_asistencia_id)
    {
        $returnResponse = [];

        $asistencias = Asistencia::where('estado', 'A')
            ->where('fecha', '>=', $f_inicio)->where('fecha', '<=', $f_fin)
            ->where('tipo_asistencia_id', intval($tipo_asistencia_id))->get();

        $returnResponse = $this->consultarTipoAsistencias($asistencias, intval($tipo_asistencia_id));

        return response()->json($returnResponse);
    }

    private function consultarTipoAsistencias($asistencias, $tipo_asistencia_id)
    {
        if (count($asistencias) > 0) {

            if (intval($tipo_asistencia_id) == 1) {
                foreach ($asistencias as $item) {
                    $item->user->persona;
                    $item->tipo_asistencia;
                    $item->tipo_registro;

                    foreach ($item->ubicacion as $ubi) {
                        $ubi;
                    }

                    foreach ($item->asistencias_departamento as $ad) {
                        $ad->departamento;
                    }
                }
                $response = [
                    'status' => true,
                    'message' => 'Existen registro de ' . $item->tipo_asistencia->type,
                    'data' => $asistencias,
                ];
            } else {
                foreach ($asistencias as $item) {
                    $item->user->persona;
                    $item->tipo_asistencia;
                    $item->tipo_registro;

                    foreach ($item->asistencia_evento as $asev) {
                        $asev->evento;
                    }
                }

                $response = [
                    'status' => true,
                    'message' => 'Existen registro de' . $item->tipo_asistencia->type,
                    'data' => $asistencias,
                ];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'No existen datos',
                'data' => null,
            ];
        }
        return $response;
    }

    public function tendeciasAsistenciasGlobal()
    {
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        for ($i = 0; $i < count($meses); $i++) {
            $asistencias = Asistencia::where('tipo_asistencia_id', 1)->whereMonth('fecha', '=', ($i + 1))->where('estado', 'A')->get();

            $dataAsistencia[] = $asistencias = ($asistencias->count()) ? $asistencias->count() : 0;

            $response = [
                'data' => [
                    'labels' => $meses,
                    'asistencia' => $dataAsistencia,
                ],
            ];
        }
        return response()->json($response);
    }

    public function regresionLinealAsistencias($temporalidad_id, $tipo_asistencia_id, $fechaInicio, $fechaFin)
    {
        $response = [];

        // if (intval($temporalidad_id) === 1) { //(día)
        //     $asistencia = Asistencia::whereDate('fecha', '>=', $fechaInicio)->whereDate('fecha', '<=', $fechaFin)->where('tipo_asistencia_id', $tipo_asistencia_id)->get();
        // } else if (intval($temporalidad_id) === 2) { // (mes)
        //     $mesInicio = intval(\DateTime::createFromFormat('Y-m-d', $fechaInicio)->format('m'));
        //     $mesFin = intval(\DateTime::createFromFormat('Y-m-d', $fechaFin)->format('m'));

        //     $asistencia = Asistencia::whereMonth('fecha', '>=', $mesInicio)->whereMonth('fecha', '<=', $mesFin)->where('tipo_asistencia_id', $tipo_asistencia_id)->get();
        // } else if (intval($temporalidad_id) === 3){ //(año)
        //     $añoInicio = intval(\DateTime::createFromFormat('Y-m-d', $fechaInicio)->format('Y'));
        //     $añoFin = intval(\DateTime::createFromFormat('Y-m-d', $fechaFin)->format('Y'));

        //     $asistencia = Asistencia::whereYear('fecha', '>=', $añoInicio)->whereYear('fecha', '<=', $añoFin)->where('tipo_asistencia_id', $tipo_asistencia_id)->get();

        // } else {
        //     return $response = [
        //         'status' => false,
        //         'message' => 'no existe temporalidad',
        //         'data' => null
        //     ];
        // }

        if (intval($temporalidad_id) === 1) { //(día)
            $groupByFormat = 'Y-m-d';
        } else if (intval($temporalidad_id) === 2) { // (mes)
            $groupByFormat = 'Y-m';
        } else if (intval($temporalidad_id) === 3){ //(año)
            $groupByFormat = 'Y';
        } else {
            return $response = [
                'status' => false,
                'message' => 'No existe temporalidad',
                'data' => null
            ];
        }

        $asistencias = Asistencia::whereBetween('fecha', [$fechaInicio, $fechaFin])
                                    ->where('tipo_asistencia_id', $tipo_asistencia_id)
                                    ->get();

        $asistenciasGrouped = $asistencias->groupBy(function ($item) use ($groupByFormat) {
            return date($groupByFormat, strtotime($item->fecha));
        });

        $asistenciasData = $asistenciasGrouped->map(function ($items) {
            $countData = $items->groupBy('tipo_registro_id')->map(function ($items) {
                    $sum = $items->count();
                    $firstItem = $items->first();
        
                    return [
                        'tipo_registro_id' => $firstItem->tipo_registro_id,
                        'cantidad' => $sum
                    ];
                });
            return $countData->values();
        });

        // $asistenciasData2 = $asistenciasGrouped->map(function ($items) {
        //     $countData = $items->countBy('tipo_registro_id');
        
        //     return $countData->values()->toArray();
        // })->values()->toArray();

        // $asistenciasData3 = $asistenciasGrouped->flatMap(function ($items) {
        //     $countData = $items->countBy('tipo_registro_id');
        
        //     return $countData->values();
        // })->toArray();

        $sumas = [];
        foreach ($asistenciasData as $subarreglo) {
            $suma = 0;
            foreach ($subarreglo as $item) {
                $suma += $item['cantidad'];
            }
            $sumas[] = $suma;
        }


        if (count($sumas) > 0) {
            //Data lineal
            $xProm = 0;
            $yProm = 0;
            $n = count($sumas);
            $i = 1;
            $sumXY = 0;
            $sumX2 = 0;
            $margen = 0.5;

            foreach ($sumas as $asi) {
                $xProm += $i;
                $yProm += $asi;

                $xy = $i * $asi;
                $x2 = pow($i, 2);

                $sumXY += $xy;
                $sumX2 += $x2;
                $i++;
            }

            $xProm = ($xProm / $n);
            $yProm = ($yProm / $n);

            $b = (($sumXY) - ($n * $xProm * $yProm)) / (($sumX2) - ($n * $xProm * $xProm));
            $a = $yProm - ($b * $xProm);

            //Evaluar mínimo y máximo
            $fx1 = $a + $b * 1;
            $fxn = $a + $b * count($sumas);
            $proyeccion = $a + $b * (count($sumas) + 1); 



            $response = [
                'status' => true,
                'data' => [
                    'datos' => $sumas,
                    'fecha' => $asistenciasGrouped->keys(),
                    'puntos' => [
                        'inicio' => [
                            'x' => 1, 'y' => round($fx1,2),
                        ],
                        'fin' => [
                            'x' => count($sumas), 'y' => round($fxn,2),
                        ],
                        'proyeccion' => [
                            'x' => count($sumas) + 1,
                            'y' => round(($proyeccion),2),
                        ],
                    ],
                    'constantes' => [
                        'a' => round($a,2),
                        'b' => $b,
                    ],
                    'promedios' => [
                        'x' => $xProm,
                        'y' => round($yProm,2),
                    ],
                    'ecuacion' => [
                        'f(x)' => round($a,2) . ' + (' . $b . ')(' . $xProm . ')',
                        'signo' => ($b > 0) ? '+' : '-',
                        'margen' => [
                            'x' => [
                                'minimo' => 1 - $margen,
                                'maximo' => count($sumas) + $margen,
                            ],
                            'y' => [
                                'minimo' => 0,
                            ],
                        ],
                    ],

                ]
            ];
            
        } else {
            $response = [
                'status' => false,
                'data' => [],
                'constantes' => [],
                'promedios' => [],
                'ecuacion' => [],
            ];
        }
        return response()->json($response);
    }

}
