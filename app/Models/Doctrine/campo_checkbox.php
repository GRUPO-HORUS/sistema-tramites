<?php
require_once('campo.php');

use Illuminate\Http\Request;
use App\Helpers\Doctrine;

class CampoCheckbox extends Campo
{

    protected function display($modo, $dato, $etapa_id = false)
    {

	if ($etapa_id) {
            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            $regla = new Regla($this->valor_default);
            $valor_default = json_decode($regla->getExpresionParaOutput($etapa->id));
        } else {
            $valor_default = json_decode($this->valor_default);
        }

        $display = '<div class="form-group">';
        $display .= '<label class="control-label">' . $this->etiqueta . (in_array('required', $this->validacion) ? '' : ' (Opcional)') . '</label>';
        if($this->datos){
            foreach ($this->datos as $d) {
                $display .= '<div class="form-check">';
                $display .= '<input class="form-check-input" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' type="checkbox" name="' . $this->nombre . '[]" value="' . $d->valor . '" id="' . $d->valor . '" ' . ((($dato && $dato->valor && in_array($d->valor, $dato->valor))||(!$dato && $valor_default && in_array($d->valor, $valor_default))) ? 'checked' : '') . ' /> ';
                $display .= '<label class="form-check-label" for="' . $d->valor . '">' . $d->etiqueta . '</label>';
                $display .= '</div>';
            }
        }
        if ($this->ayuda)
            $display .= '<span class="text-muted form-text">' . $this->ayuda . '</span>';
        $display .= '</div>';

        return $display;
    }

    public function backendExtraValidate(Request $request)
    {
        /*  $request->validate(['datos' => 'required']);
          $CI =& get_instance();
          $CI->form_validation->set_rules('datos', 'Datos', 'required');*/
    }

}
