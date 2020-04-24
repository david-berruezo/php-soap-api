<?php
namespace classes;

use SoapHeader;
use SoapClient;
use Soap;
use SimpleXml;

class WebServiceMRW
{
    // Por defecto Ofiprix
    private $dbh;
    private $FranquiciaMRW      = '';
    private $AbonadoMRW         = '';
    private $DepartamentoMRW    = '';
    private $usuarioMRW         = '';
    private $PasswordMRW        = '';
    

    public function __construct()
    {
        // Capturamos errores
        ini_set("display_errors",1);
        error_reporting(E_ALL);
        $this->wsdl_url = "http://sagec-test.mrw.es/MRWEnvio.asmx?WSDL";
        $this->cabeceras = array(
            'CodigoFranquicia'   => $this->FranquiciaMRW, //Obligatorio
            'CodigoAbonado'      => $this->AbonadoMRW, //Obligatorio
            'CodigoDepartamento' => $this->DepartamentoMRW, //Opcional - Se puede omitir si no se han creado departamentos en sistemas de MRW.
            'UserName'           => $this->usuarioMRW, //Obligatorio
            'Password'           => $this->PasswordMRW //Obligatorio
        );
        $this->init();
    }

    private function init()
    {
        try {
            $this->clientMRW = new SoapClient(
                $this->wsdl_url,
                array(
                    'trace' => TRUE
                )
            );
        } catch (SoapFault $e) {
            printf("Error creando cliente SOAP: %s<br />\n", $e->__toString());
            return false;
        }
    }

    public function enviarEtiqueta($request)
    {
        $responseCode = '';
        date_default_timezone_set('CET');
        $hoy = date("d/m/Y");
        // Cargamos los headers sobre el objeto cliente SOAP
        $header = new SoapHeader('http://www.mrw.es/', 'AuthInfo', $this->cabeceras);
        $this->clientMRW->__setSoapHeaders($header);
        // Invocamos el metodo TransmEnvio pasandole los parametros del envio
        try {
            $responseCode = $this->clientMRW->TransmEnvio($request);
            if ($responseCode->TransmEnvioResult->Estado == 1) {
                /*
                $estado      = $responseCode->TransmEnvioResult->Estado;
                $mensaje     = $responseCode->TransmEnvioResult->Mensaje;
                $n_solicitud = $responseCode->TransmEnvioResult->NumeroSolicitud;
                $n_envio     = $responseCode->TransmEnvioResult->NumeroEnvio;
                $url         = $responseCode->TransmEnvioResult->Url;    
                $responseCode => Nos devuelve un Object.
                $responseCode->TransmEnvioResult->Estado, $responseCode->TransmEnvioResult->Mensaje, $responseCode->TransmEnvioResult->NumeroSolicitud, $responseCode->TransmEnvioResult->NumeroEnvio, $responseCode->TransmEnvioResult->Url
                Con estos datos podemos introducirlos en BD y luego rescatarlos para obtener el número de envío.
                */
            } else {
                // Error, introducir en un log.
            }
        } catch (SoapFault $exception) {
            // Error, mostramos la excepción del SOAP.
        }
        return $responseCode;
    }

    
    // setters and getters
    
    /**
     * Get the value of FranquiciaMRW
     */ 
    public function getFranquiciaMRW()
    {
        return $this->FranquiciaMRW;
    }

    /**
     * Set the value of FranquiciaMRW
     *
     * @return  self
     */ 
    public function setFranquiciaMRW($FranquiciaMRW)
    {
        $this->FranquiciaMRW = $FranquiciaMRW;

        return $this;
    }

    

    /**
     * Get the value of AbonadoMRW
     */ 
    public function getAbonadoMRW()
    {
        return $this->AbonadoMRW;
    }

    /**
     * Set the value of AbonadoMRW
     *
     * @return  self
     */ 
    public function setAbonadoMRW($AbonadoMRW)
    {
        $this->AbonadoMRW = $AbonadoMRW;

        return $this;
    }

    /**
     * Get the value of DepartamentoMRW
     */ 
    public function getDepartamentoMRW()
    {
        return $this->DepartamentoMRW;
    }

    /**
     * Set the value of DepartamentoMRW
     *
     * @return  self
     */ 
    public function setDepartamentoMRW($DepartamentoMRW)
    {
        $this->DepartamentoMRW = $DepartamentoMRW;

        return $this;
    }

    /**
     * Get the value of usuarioMRW
     */ 
    public function getUsuarioMRW()
    {
        return $this->usuarioMRW;
    }

    /**
     * Set the value of usuarioMRW
     *
     * @return  self
     */ 
    public function setUsuarioMRW($usuarioMRW)
    {
        $this->usuarioMRW = $usuarioMRW;

        return $this;
    }

    /**
     * Get the value of PasswordMRW
     */ 
    public function getPasswordMRW()
    {
        return $this->PasswordMRW;
    }

    /**
     * Set the value of PasswordMRW
     *
     * @return  self
     */ 
    public function setPasswordMRW($PasswordMRW)
    {
        $this->PasswordMRW = $PasswordMRW;

        return $this;
    }
}
?>
