<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Doctrine_Query;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function get(Request $request, $inline, $filename='', $usuario_backend = null)
    {
        $id = $request->input('id');
        $token = $request->input('token');

        //Chequeamos permisos del frontend
        $file = Doctrine_Query::create()
            ->from('File f, f.Tramite t, t.Etapas e, e.Usuario u')
            ->where('f.id = ? AND f.llave = ? AND u.id = ?', array($id, $token, Auth::user()->id))
            ->fetchOne();

        if (!$file) {
            //Chequeamos permisos en el backend
            $file = Doctrine_Query::create()
                ->from('File f, f.Tramite.Proceso.Cuenta.UsuariosBackend u')
                ->where('f.id = ? AND f.llave = ? AND u.id = ? AND (u.rol like "%super%" OR u.rol like "%operacion%" OR u.rol like "%seguimiento%")', array($id, $token, $usuario_backend))
                ->fetchOne();

            if (!$file) {
                echo 'Usuario no tiene permisos para ver este archivo.';
                exit;
            }
        }


        $path = 'uploads/documentos/' . $file->filename;

        if (preg_match('/^\.\./', $file->filename)) {
            echo 'Archivo invalido';
            exit;
        }

        if (!file_exists($path)) {
            echo 'Archivo no existe';
            exit;
        }

        if($inline == '0') {
            $friendlyName = str_replace(' ', '-', str_slug(mb_convert_case($file->Tramite->Proceso->Cuenta->nombre . ' ' . $file->Tramite->Proceso->nombre, MB_CASE_LOWER) . '-' . $file->id)) . '.' . pathinfo($path, PATHINFO_EXTENSION);
            return response()->download($path, $friendlyName);
        }else{
            header('Content-Disposition: inline; filename="'.$filename.'"');
            header("Cache-Control: no-cache, must-revalidate");
            header("Content-type:application/pdf");
            readfile($path);
        }
    }

    //Acceso que utiliza applet de firma con token
   public function firma_get(Request $request){
        $id=$request->input('id');
        $llave_firma=$request->input('token');
        $usuarioId=$request->input('idSession');

        if(!$id || !$llave_firma){
            $resultado=array();
            $resultado=array("status"=>1,
            "error"=>"Faltan parametros");
            echo json_encode($resultado);
            exit;
        }

        $file=Doctrine_Query::create()
                ->from('File f, f.Tramite.Etapas.Usuario u')
                ->where('f.id = ? AND f.tipo = ? AND f.llave_firma = ? AND u.id = ?',array($id,'documento',$llave_firma,$usuarioId))
                ->fetchOne();

        $resultado=array();
        if(!$file){
            $resultado=array("status"=>1,
            "error"=>"Token no corresponde");
        }else{
            $resultado=array("status"=>0,
            "tipo"=>"pdf",
            "documento"=>base64_encode(file_get_contents('uploads/documentos/'.$file->filename)));
        }

        echo json_encode($resultado);
    }

   public function firma_post(Request $request){
        $id=$request->input('id');
        $llave_firma=$request->input('token');
        $documento=$request->input('documento');
        $usuarioId=$request->input('idSession');

        if(!$id || !$llave_firma || !$documento){
             $resultado=array("status"=>1,
             "error"=>"Faltan parametros");
             echo json_encode($resultado);
             exit;
	}

        $file=Doctrine_Query::create()
                ->from('File f, f.Tramite.Etapas.Usuario u')
                ->where('f.id = ? AND f.tipo = ? AND f.llave_firma = ? AND u.id = ?',array($id,'documento',$llave_firma,$usuarioId))
                ->fetchOne();

        if(!$file){
            $resultado=array("status"=>1,
            "error"=>"Token no corresponde");
        }else{
            $resultado=array("status"=>0);
            file_put_contents('uploads/documentos/'.$file->filename, base64_decode($documento));
        }

        echo json_encode($resultado);
    }

}
