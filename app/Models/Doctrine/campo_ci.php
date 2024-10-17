<?php
require_once('campo.php');

use App\Helpers\Doctrine;
use Illuminate\Http\Request;

class CampoCI extends Campo
{

    public $requiere_datos = false;

    protected function display($modo, $dato, $etapa_id = false)
    {
        if ($etapa_id) {
            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            $regla = new Regla($this->valor_default);
            $valor_default = $regla->getExpresionParaOutput($etapa->id);
        } else {
            $valor_default = $this->valor_default;
        }
		
		$valor_nombres_default='';
		$valor_apellidos_default='';
		$valor_fechaNacimiento_default='';

        $display = '<div class="form-group">';
        $display .= '<label class="control-label" for="' . $this->nombre . '_cedula_' .$this->id . '">Número de Cédula del ' . $this->etiqueta . (!in_array('required', $this->validacion) ? ' (Opcional)' : '') . '</label>';
        if ($this->ayuda)
            $display .= '<span class="help-block"> (' . $this->ayuda . ')</span>';
        $display .= '<input id="' . $this->nombre . '_cedula_' .$this->id . '" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' type="text" class="form-control has-error" name="'.$this->nombre.'_cedula_' . $this->id . '" value="' . ($dato ? htmlspecialchars($dato->valor->cedula) : htmlspecialchars($valor_default)) . '" data-modo="' . $modo . '" onKeyPress="return identificaciones_'.$this->nombre.'_'.$this->id.'(event)" />';
        $display .= '</div>';
		
		$display .= '<div class="form-group">';
        $display .= '<label class="control-label" for="'. $this->nombre . '_nombres_' .$this->id .'">Nombres del  ' . $this->etiqueta . (!in_array('required', $this->validacion) ? ' (Opcional)' : '') .'</label>';
        $display .= '<input id="'. $this->nombre . '_nombres_' .$this->id .'" type="text" class="form-control has-error" name="'.$this->nombre.'_nombres_' . $this->id . '" value="' . ($dato ? htmlspecialchars($dato->valor->nombres) : htmlspecialchars($valor_nombres_default)) . '" data-modo="' . $modo . '" />';
        $display .= '</div>';
		
		$display .= '<div class="form-group">';
        $display .= '<label class="control-label" for="'. $this->nombre . '_apellidos_' .$this->id .'">Apellidos del ' . $this->etiqueta . (!in_array('required', $this->validacion) ? ' (Opcional)' : '') .'</label>';
        $display .= '<input id="'. $this->nombre . '_apellidos_' .$this->id .'" type="text" class="form-control has-error" name="'.$this->nombre.'_apellidos_' . $this->id . '" value="' . ($dato ? htmlspecialchars($dato->valor->apellidos) : htmlspecialchars($valor_apellidos_default)) . '" data-modo="' . $modo . '" />';
        $display .= '</div>';
		
		$display .= '<div class="form-group">';
        $display .= '<label class="control-label" for="' . $this->nombre . '_fechaNacimiento_' .$this->id . '">Fecha de Nacimiento del ' . $this->etiqueta . (!in_array('required', $this->validacion) ? ' (Opcional)' : '') . '</label>';
        $display .= '<input id="' . $this->nombre . '_fechaNacimiento_' . $this->id . '" class="datetimepicker form-control" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' type="text" name="' . $this->nombre . '_fechaNacimiento_' . $this->id . '" value="' . ($dato && $dato->valor ? $dato->valor->fechaNacimiento : ($valor_fechaNacimiento_default ? $valor_fechaNacimiento_default : '')) . '" placeholder="dd-mm-aaaa" />';
		$display .= '</div>';
		
		$display .= '<label class="control-label">Nacionalidad del ' . $this->etiqueta . (in_array('required', $this->validacion) ? '' : ' (Opcional)') . '</label>';
        $display.='<div class="controls">';
        $display.='<select class="select-semi-large paises form-control" id="'.$this->nombre . '_nacionalidad_' .$this->id.'" data-id="'.$this->id.'" name="' . $this->nombre . '_nacionalidad_' .$this->id . '" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' style="width:100%">';
        $display.='<option value="">Seleccione Pa&iacute;s</option>';
        $display.='</select>';
        $display.='</div>';
		
		$minDate = null;
        $maxDate = null;
		
		$display .= "
                    <script>
                        $(document).ready(function(){
                            const maxDate = \"$maxDate\";
                            const minDate = \"$minDate\";
                            const idDateTime = \"".$this->nombre . '_fechaNacimiento_' . $this->id."\";
                            let config = {
                                format: 'DD-MM-YYYY',
                                useCurrent: false,
                                icons: {
                                    previous: 'glyphicon glyphicon-chevron-left',
                                    next: 'glyphicon glyphicon-chevron-right'
                                },
                                locale: 'es'
                            };

                            $('#'+idDateTime).datetimepicker(config).on('dp.show', function(event) {
                                if (minDate) {
                                    $('#'+idDateTime).data('DateTimePicker').minDate(moment(minDate));
                                }
                                if (maxDate) {
                                    $('#'+idDateTime).data('DateTimePicker').maxDate(moment(maxDate));
                                }
                                if (minDate == maxDate) {
                                    $('#'+idDateTime).data('DateTimePicker').defaultDate(moment(minDate));
                                }
                            }).on('dp.error', function(e) {
                                console.error(e);
                                if (moment(e.date._d).format('YYYY-MM-DD') == maxDate) {
                                    let fecha = String(moment(e.date._d).format('YYYY-MM-DD'));
                                    $('#'+idDateTime).data('DateTimePicker').defaultDate(moment(fecha));
                                }
                            });
                         });
                    </script>
                ";
		
		$display.='
            <script>
                $(document).ready(function(){
                    var justLoadedPais=true;
                    var defaultPais="'.($dato && $dato->valor?$dato->valor->nacionalidad:'').'";
                    
                    $("#'.$this->nombre . '_nacionalidad_' .$this->id.'").chosen({placeholder_text: "Selecciona Pa\u00cds"});
                    
                    function updatePaises_'.$this->nombre.'_'.$this->id.'(){
                        var paises_obj = $("#'.$this->nombre . '_nacionalidad_' .$this->id.'");
                        var data={"PY":"Paraguay","AF":"Afganist\u00e1n","AL":"Albania","DE":"Alemania","AD":"Andorra","AO":"Angola","AI":"Anguila","AG":"Antigua y Barbuda","AN":"Antillas Holandesas","AQ":"Ant\u00e1rtida","SA":"Arabia Saudita","DZ":"Argelia","AR":"Argentina","AM":"Armenia","AW":"Aruba","AU":"Australia","AT":"Austria","AZ":"Azerbay\u00e1n","BS":"Bahamas","BH":"Bahr\u00e9in","BD":"Bangladesh","BB":"Barbados","BZ":"Belice","BJ":"Ben\u00edn","BM":"Bermudas","BY":"Bielorrusia","BO":"Bolivia","BA":"Bosnia-Herzegovina","BW":"Botsuana","BR":"Brasil","BN":"Brun\u00e9i","BG":"Bulgaria","BF":"Burkina Faso","BI":"Burundi","BT":"But\u00e1n","BE":"B\u00e9lgica","CV":"Cabo Verde","KH":"Camboya","CM":"Camer\u00fan","CA":"Canad\u00e1","TD":"Chad","CL":"Chile","CN":"China","CY":"Chipre","VA":"Ciudad del Vaticano","CO":"Colombia","KM":"Comoras","CG":"Congo","KP":"Corea del Norte","KR":"Corea del Sur","CR":"Costa Rica","CI":"Costa de Marfil","HR":"Croacia","CU":"Cuba","DK":"Dinamarca","DM":"Dominica","EC":"Ecuador","EG":"Egipto","SV":"El Salvador","AE":"Emiratos \u00c1rabes Unidos","ER":"Eritrea","SK":"Eslovaquia","SI":"Eslovenia","ES":"Espa\u00f1a","US":"Estados Unidos","EE":"Estonia","ET":"Etiop\u00eda","PH":"Filipinas","FI":"Finlandia","FJ":"Fiyi","FR":"Francia","GA":"Gab\u00f3n","GM":"Gambia","GE":"Georgia","GH":"Ghana","GI":"Gibraltar","GD":"Granada","GR":"Grecia","GL":"Groenlandia","GP":"Guadalupe","GU":"Guam","GT":"Guatemala","GF":"Guayana Francesa","GG":"Guernsey","GN":"Guinea","GQ":"Guinea Ecuatorial","GW":"Guinea-Bissau","GY":"Guyana","HT":"Hait\u00ed","HN":"Honduras","HU":"Hungr\u00eda","IN":"India","ID":"Indonesia","IQ":"Iraq","IE":"Irlanda","IR":"Ir\u00e1n","BV":"Isla Bouvet","CX":"Isla Christmas","NU":"Isla Niue","NF":"Isla Norfolk","IM":"Isla de Man","IS":"Islandia","KY":"Islas Caim\u00e1n","CC":"Islas Cocos","CK":"Islas Cook","FO":"Islas Feroe","GS":"Islas Georgia del Sur y Sandwich del Sur","HM":"Islas Heard y McDonald","FK":"Islas Malvinas","MP":"Islas Marianas del Norte","MH":"Islas Marshall","SB":"Islas Salom\u00f3n","TC":"Islas Turcas y Caicos","VG":"Islas V\u00edrgenes Brit\u00e1nicas","VI":"Islas V\u00edrgenes de los Estados Unidos","UM":"Islas menores alejadas de los Estados Unidos","AX":"Islas \u00c5land","IL":"Israel","IT":"Italia","JM":"Jamaica","JP":"Jap\u00f3n","JE":"Jersey","JO":"Jordania","KZ":"Kazajist\u00e1n","KE":"Kenia","KG":"Kirguist\u00e1n","KI":"Kiribati","KW":"Kuwait","LA":"Laos","LS":"Lesoto","LV":"Letonia","LR":"Liberia","LY":"Libia","LI":"Liechtenstein","LT":"Lituania","LU":"Luxemburgo","LB":"L\u00edbano","MK":"Macedonia","MG":"Madagascar","MY":"Malasia","MW":"Malaui","MV":"Maldivas","ML":"Mali","MT":"Malta","MA":"Marruecos","MQ":"Martinica","MU":"Mauricio","MR":"Mauritania","YT":"Mayotte","FM":"Micronesia","MD":"Moldavia","MN":"Mongolia","ME":"Montenegro","MS":"Montserrat","MZ":"Mozambique","MM":"Myanmar","MX":"M\u00e9xico","MC":"M\u00f3naco","NA":"Namibia","NR":"Nauru","NP":"Nepal","NI":"Nicaragua","NG":"Nigeria","NO":"Noruega","NC":"Nueva Caledonia","NZ":"Nueva Zelanda","NE":"N\u00edger","OM":"Om\u00e1n","PK":"Pakist\u00e1n","PW":"Palau","PA":"Panam\u00e1","PG":"Pap\u00faa Nueva Guinea","PY":"Paraguay","NL":"Pa\u00edses Bajos","PE":"Per\u00fa","PN":"Pitcairn","PF":"Polinesia Francesa","PL":"Polonia","PT":"Portugal","PR":"Puerto Rico","QA":"Qatar","HK":"Regi\u00f3n Administrativa Especial de Hong Kong de la Rep\u00fablica Popular China","MO":"Regi\u00f3n Administrativa Especial de Macao de la Rep\u00fablica Popular China","ZZ":"Regi\u00f3n desconocida o no v\u00e1lida","GB":"Reino Unido","CF":"Rep\u00fablica Centroafricana","CZ":"Rep\u00fablica Checa","CD":"Rep\u00fablica Democr\u00e1tica del Congo","DO":"Rep\u00fablica Dominicana","RE":"Reuni\u00f3n","RW":"Ruanda","RO":"Rumania","RU":"Rusia","EH":"Sahara Occidental","WS":"Samoa","AS":"Samoa Americana","BL":"San Bartolom\u00e9","KN":"San Crist\u00f3bal y Nieves","SM":"San Marino","MF":"San Mart\u00edn","PM":"San Pedro y Miquel\u00f3n","VC":"San Vicente y las Granadinas","SH":"Santa Elena","LC":"Santa Luc\u00eda","ST":"Santo Tom\u00e9 y Pr\u00edncipe","SN":"Senegal","RS":"Serbia","CS":"Serbia y Montenegro","SC":"Seychelles","SL":"Sierra Leona","SG":"Singapur","SY":"Siria","SO":"Somalia","LK":"Sri Lanka","SZ":"Suazilandia","ZA":"Sud\u00e1frica","SD":"Sud\u00e1n","SE":"Suecia","CH":"Suiza","SR":"Surinam","SJ":"Svalbard y Jan Mayen","TH":"Tailandia","TW":"Taiw\u00e1n","TZ":"Tanzania","TJ":"Tayikist\u00e1n","IO":"Territorio Brit\u00e1nico del Oc\u00e9ano \u00cdndico","PS":"Territorio Palestino","TF":"Territorios Australes Franceses","TL":"Timor Oriental","TG":"Togo","TK":"Tokelau","TO":"Tonga","TT":"Trinidad y Tobago","TM":"Turkmenist\u00e1n","TR":"Turqu\u00eda","TV":"Tuvalu","TN":"T\u00fanez","UA":"Ucrania","UG":"Uganda","UY":"Uruguay","UZ":"Uzbekist\u00e1n","VU":"Vanuatu","VE":"Venezuela","VN":"Vietnam","WF":"Wallis y Futuna","YE":"Yemen","DJ":"Yibuti","ZM":"Zambia","ZW":"Zimbabue"};
                        
                        $.each(data,function(i,el){
                            paises_obj.append("<option value=\""+el+"\">"+el+"</option>");
                        });
                        
                        if(justLoadedPais){
                            paises_obj.val(defaultPais).change();
                            justLoadedPais=false;
                        }
                        paises_obj.trigger("chosen:updated");
                    }
                    
                    updatePaises_'.$this->nombre.'_'.$this->id.'();
                });
                

                
            </script>';
		
		$display .= '<script type="text/javascript">function requestListenerCedula_'.$this->nombre.'_'.$this->id.' () {
  var identificaciones=JSON.parse(this.responseText);
  document.getElementById("' . $this->nombre.'_nombres_'.$this->id . '").value=identificaciones.obtenerPersonaPorNroCedulaResponse.return.nombres;
  document.getElementById("' . $this->nombre.'_apellidos_'.$this->id . '").value=identificaciones.obtenerPersonaPorNroCedulaResponse.return.apellido;
  document.getElementById("' . $this->nombre.'_fechaNacimiento_'.$this->id . '").value=identificaciones.obtenerPersonaPorNroCedulaResponse.return.fechNacim.substring(8,10) +"-"+identificaciones.obtenerPersonaPorNroCedulaResponse.return.fechNacim.substring(5,7)+"-"+ identificaciones.obtenerPersonaPorNroCedulaResponse.return.fechNacim.substring(0,4);
  if(identificaciones.obtenerPersonaPorNroCedulaResponse.return.nacionalidadBean=="PARAGUAYA")
  {
	document.getElementById("' . $this->nombre.'_nacionalidad_'.$this->id . '").value="Paraguay";
	 $("#'.$this->nombre . '_nacionalidad_' .$this->id.'").trigger("chosen:updated");
  }
}
 function identificaciones_'.$this->nombre.'_'.$this->id.' (event) {
  if(event.keyCode==13){
  var xmlHttpRequestCedula = new XMLHttpRequest();
  xmlHttpRequestCedula.addEventListener("load", requestListenerCedula_'.$this->nombre.'_'.$this->id.');
  xmlHttpRequestCedula.open("POST", "/identidadelectronica-backend/api/persona/datosIdentificaciones");
  xmlHttpRequestCedula.setRequestHeader("Content-Type", "application/json");
  xmlHttpRequestCedula.send(document.getElementById("' . $this->nombre.'_cedula_'.$this->id . '").value);
  return false;
  }
}

 </script>';

        $searchword = 'max';
        $matches = array_filter($this->validacion, function($var) use ($searchword) { return preg_match("/\b$searchword\b/i", $var); });
        if(count($matches)){
            $indice = max(array_keys($matches));
            $limite = str_replace("max:","",$matches[$indice]);
            $display .= '
            <script>
                $(document).ready(function(){
                    $("#' . $this->id . '").EnsureMaxLength({
                        limit: '.$limite.'
                    });
                });
            </script>';
        }
            

        return $display;
    }
	
	public function formValidate(Request $request, $etapa_id = null)
    {
        $request->validate([
            $this->nombre . '_cedula_' . $this->id => implode('|', $this->validacion),
            $this->nombre . '_nombres_' . $this->id => implode('|', $this->validacion),
			$this->nombre . '_apellidos_' . $this->id => implode('|', $this->validacion),
			$this->nombre . '_fechaNacimiento_' . $this->id => implode('|', array_merge(array('date_prep'), $this->validacion)),
			$this->nombre . '_nacionalidad_' . $this->id => implode('|', $this->validacion),
        ], [], [
            $this->nombre . '_cedula_' . $this->id => "<b>Cédula de $this->etiqueta</b>",
            $this->nombre . '_nombres_' . $this->id => "<b>Nombres de $this->etiqueta</b>",
			$this->nombre . '_apellidos_' . $this->id => "<b>Apellidos de $this->etiqueta</b>",
			$this->nombre . '_fechaNacimiento_' . $this->id => "<b>Fecha Nacimiento de $this->etiqueta</b>",
			$this->nombre . '_nacionalidad_' . $this->id => "<b>Nacionalidad de $this->etiqueta</b>"
        ]);
    }

}
