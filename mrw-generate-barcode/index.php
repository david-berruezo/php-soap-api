<?php 
// mostrar warnings
//error_reporting(E_ALLL);
//ini_set("display_errors",1);
//mostrar errors
//error_reporting(E_ERROR);   
//ini_set("display_errors",0);

include_once("./vendor/autoload.php");

use classes\WebServiceMRW;
use classes\App;

$webservice = new WebServiceMRW();
$aplicacion = new App($webservice);

$temp = array();
$temp["DIRCLIENVIO"]    = "vallirana";
$temp["CDPCLIENVIO"]    = "08006";
$temp["POBCLIENVIO"]    = "barcelona";
$temp["CONTACTONEW"]    = "David";
$temp["TLFCONTACTONEW"] = "615231533";
$temp["BULTOS"]         = 1;
$temp["PESO"]           = 1;

// set business information to header soap
$webservice->set_ofiprix();

$aplicacion->send_mrw($temp);

?>