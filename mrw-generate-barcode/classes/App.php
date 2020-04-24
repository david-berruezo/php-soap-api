<?php

namespace classes;

class App{
    
    // Mrw
    private $url_final = "";
    private $n_envio   = 0;
    private $webservice = "";

    /*
     * Construimos recibiendo
     * el objeto webservice
     * para conectarnos a MRW 
     */
    public function __construct($webservice){
        $this->webservice = $webservice;
        // mostrar warnings
        // error_reporting(E_ALLL);
        // mostrar errors
        error_reporting(E_ERROR);   
        ini_set("display_errors",1);
    }

    public function send_mrw($temp){
        date_default_timezone_set('CET');
        $hoy = date("d/m/Y");
        $params = array(
            'request' => array(
                'DatosEntrega' => array(
                    ## DATOS DESTINATARIO ##
                    'Direccion' => array(
                        'CodigoDireccion' => '', //Opcional - Se puede omitir. Si se indica sustituira al resto de parametros
                        'CodigoTipoVia' => 'Calle', //Opcional - Se puede omitir aunque es recomendable usarlo
                        'Via' => $temp["DIRCLIENVIO"], //Obligatorio
                        'Numero' => '1', //Obligatorio - Recomendable que sea el dato real. Si no se puede extraer el dato real se pondra 0 (cero)
                        'Resto' => 'cuarto segunda', //Opcional - Se puede omitir.
                        'CodigoPostal' => $temp["CDPCLIENVIO"], //Obligatorio
                        'Poblacion' => $temp["POBCLIENVIO"], //Obligatorio
                        'Provincia' => $temp["POBCLIENVIO"], //Obligatorio
                        //'Estado' => '', //Opcional - Se debe omitir para envios nacionales.
                        'CodigoPais' => 'ES' //Opcional - Se puede omitir para envios nacionales.
                    ),
                    'Nif' => '47649039P', //Opcional - Se puede omitir.
                    'Nombre' => $temp["CONTACTONEW"], //Obligatorio
                    'Telefono' => $temp["TLFCONTACTONEW"], //Obligatorio
                    'Contacto' => $temp["CONTACTONEW"], //Opcional - Muy recomendable
                    'ALaAtencionDe' => $temp["CONTACTONEW"], //Opcional - Se puede omitir.
                    'Observaciones' => 'Observaciones',//Opcional - Se puede omitir.
                ),
                ## DATOS DEL SERVICIO ##
                'DatosServicio' => array(
                    'Fecha' => $hoy,  //Obligatorio. Fecha >= Hoy()
                    'Referencia' => '',  //Obligatorio. ¿numero de pedido/albaran/factura?
                    'CodigoServicio' => '0205', // Obligatorio. Cada servicio deberá ser activado por la franquicia
                    //0800 = Ecommerce
                    ## Desglose de Bultos <--
                    'NumeroBultos' => $temp["BULTOS"], // Obligatorio. Solo puede haber un bulto por envio en eCommerce
                    'Peso' => $temp["PESO"], // Obligatorio. Debe ser igual al valor Peso en BultoRequest si se ha usado
                )
            )
        );   
        $responseCode   = $this->webservice->enviarEtiqueta($params);
        $estado         = $responseCode->TransmEnvioResult->Estado;
        $mensaje        = $responseCode->TransmEnvioResult->Mensaje;
        $n_solicitud    = $responseCode->TransmEnvioResult->NumeroSolicitud;
        $n_envio        = $responseCode->TransmEnvioResult->NumeroEnvio;
        $url            = $responseCode->TransmEnvioResult->Url;
        $url_final = 'http://sagec-test.mrw.es/Panel.aspx?Franq='.$this->webservice->getFranquiciaMRW()."&Ab=".$this->webservice->getAbonadoMRW().'&Dep='.$this->webservice->getDepartamentoMRW().'&Pwd='.$this->webservice->getPasswordMRW().'&Usr='.$this->webservice->getUsuarioMRW().'&NumEnv='.$n_envio;
        $link = "<script>window.open('".$url_final."')</script>";
        echo $link;
        return $responseCode;
    }
}
?>