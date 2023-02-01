<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ApiController extends Controller
{
    public function cantidad_concepto_certificado(Request $request)
    {   
        if (!isset($request->cui) || !isset($request->nues)) {       
            return array('success' => false, 'message' => 'Debe ingresar los argumentos cui y nues');       
        }
        else if (!intval($request->cui) || !intval($request->nues)) {
            return array('success' => false, 'message' => 'Los argumentos cui y nues deben ser números');
        }
        else if (strlen($request->cui) != 8 || strlen($request->nues) != 3) {        
            return array('success' => false, 'message' => 'El argumento cui debe tener 8 dígitos y el argumento nues debe tener 3 dígitos');        
        }
        else {       
        
            $cantidad_pagos_por_concepto = array();
            $codi_usua = $request->cui;
            $codi_depe = $request->nues;        
            
            if($codi_depe==406){
                $arra_anno_sele=array(1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1);
                $sql_anhos = " AND substring(a.casi,4,1) in (1,2,3,4,5,6,7)"; //si se elige sacar un certificado de sus 5 años de estudios
            } elseif ($codi_depe==474 || $codi_depe==469) {
                $arra_anno_sele=array(1=>1,2=>1,3=>1,4=>1,5=>1,6=>1);
                $sql_anhos = " AND substring(a.casi,4,1) in (1,2,3,4,5,6)"; //si se elige sacar un certificado de sus 5 años de estudios
            } else {
                $arra_anno_sele=array(1=>1,2=>1,3=>1,4=>1,5=>1);
                $sql_anhos = " AND substring(a.casi,4,1) in (1,2,3,4,5)"; //si se elige sacar un certificado de sus 5 años de estudios
            }        
            
            $anno = date('Y');

            if(isset($arra_anno_sele)){
                foreach ($arra_anno_sele as $key => $value):
                    $anos_seleccionados[]=$key;
                endforeach;
            }
            
            $sql = "SELECT substring(a.casi,4,1) xx,IF($anno-anoh>10,1,2) yy,count(*),sum(cred) FROM acdl$codi_depe=a,actasig=b WHERE nues=$codi_depe and a.casi=b.casi AND cui='$codi_usua' AND Find_in_Set(core, 'A,J,S,C') AND nota>10 $sql_anhos group by xx,yy;";
            $result = app('db')->select($sql);        
            $_result = json_decode(json_encode($result), true);      
            $arra_rpta = array(1=>0, 2=>0);
            $row_max = array('xx'=>0, 'yy'=>0, 'count(*)'=>0, 'sum(cred)'=>0);

            foreach($_result as $key => $row) {
                if(in_array($row['xx'], $anos_seleccionados)) {
                    if($row['xx']==$row_max['xx']){
                        if ($row_max['sum(cred)']<$row['sum(cred)']) {
                            $row_max=$row;
                        }
                    } else {
                        if($row_max['yy']==1){
                            $arra_rpta[2]++;
                        }
                        if($row_max['yy']==2){
                            $arra_rpta[1]++;
                        }
                        $row_max=$row;
                    }
                }
            }
            if($row_max['yy']==1){
                $arra_rpta[2]++;
            }
            if($row_max['yy']==2){
                $arra_rpta[1]++;
            }

            $cantidad_pagos_por_concepto['cantidad_menor_10_anios'] = $arra_rpta[1] !== null ? $arra_rpta[1] : 0;
            $cantidad_pagos_por_concepto['cantidad_mayor_10_anios'] = $arra_rpta[2] !== null ? $arra_rpta[2] : 0;

            return array('success' => true, 'data' => $cantidad_pagos_por_concepto);
        }
    }
}