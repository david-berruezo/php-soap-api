# PHP SOAP API — Ejemplos de Conexión a WebServices

Colección de ejemplos prácticos de consumo de **WebServices SOAP** con PHP. El repositorio incluye implementaciones reales de integración con APIs de empresas logísticas, comenzando por el sistema **SAGEC de MRW** para la generación automática de etiquetas con código de barras para envíos.

[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![SOAP](https://img.shields.io/badge/SOAP-1.1%20|%201.2-FF6600)](https://www.php.net/manual/en/class.soapclient.php)
[![Composer](https://img.shields.io/badge/Composer-Dependencies-885630?logo=composer&logoColor=white)](https://getcomposer.org/)

---

## Tabla de Contenidos

- [Sobre el Proyecto](#sobre-el-proyecto)
- [¿Qué es SOAP?](#qué-es-soap)
- [Ejemplos Incluidos](#ejemplos-incluidos)
  - [MRW SAGEC — Generación de Códigos de Barras](#mrw-sagec--generación-de-códigos-de-barras)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Cómo Funciona SOAP con PHP](#cómo-funciona-soap-con-php)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Recursos](#recursos)
- [Autor](#autor)

---

## Sobre el Proyecto

SOAP (Simple Object Access Protocol) sigue siendo un estándar ampliamente utilizado en integraciones empresariales, especialmente en el sector logístico, financiero y de e-commerce. Muchas empresas como MRW, SEUR, Correos, bancos y ERPs exponen sus servicios a través de WebServices SOAP con WSDL.

Este repositorio reúne ejemplos funcionales de cómo consumir estos servicios desde PHP utilizando la clase nativa `SoapClient`, con el objetivo de servir como referencia para integraciones reales en proyectos de producción.

---

## ¿Qué es SOAP?

SOAP es un protocolo de comunicación basado en XML para el intercambio de información entre sistemas. A diferencia de REST (que usa JSON y HTTP), SOAP utiliza mensajes XML estructurados y se define mediante un archivo **WSDL** (Web Services Description Language) que describe las operaciones disponibles, los tipos de datos y los endpoints.

```
┌──────────────┐     Petición SOAP (XML)     ┌──────────────────┐
│              │ ──────────────────────────►  │                  │
│  PHP Client  │                              │  SOAP Server     │
│  (SoapClient)│ ◄──────────────────────────  │  (sagec.mrw.es)  │
│              │     Respuesta SOAP (XML)     │                  │
└──────────────┘                              └──────────────────┘
                        │
                        ▼
                 ┌──────────────┐
                 │  WSDL File   │
                 │  (Contrato)  │
                 │  - Métodos   │
                 │  - Tipos     │
                 │  - Endpoint  │
                 └──────────────┘
```

**Ventajas de SOAP:**

- Contrato formal (WSDL) que define toda la interfaz
- Soporte nativo para autenticación en cabeceras (WS-Security)
- Tipado fuerte de datos (XML Schema)
- Protocolo agnóstico al transporte (HTTP, SMTP, etc.)
- Amplio soporte en entornos empresariales (ERP, CRM, logística, banca)

---

## Ejemplos Incluidos

### MRW SAGEC — Generación de Códigos de Barras

| | |
|---|---|
| **Carpeta** | `mrw-generate-barcode/` |
| **Empresa** | [MRW](https://www.mrw.es/) — Operadora logística española |
| **Sistema** | SAGEC (Sistema Automatizado de Gestión de Envíos por Conexión) |
| **Endpoint** | `sagec.mrw.es` |
| **Protocolo** | SOAP 1.1 / 1.2 |

![MRW SAGEC Barcode](mrw-generate-barcode/data_model.gif)

#### ¿Qué es MRW SAGEC?

**SAGEC** es la solución de integración de MRW que permite a tiendas online y empresas gestionar envíos de forma totalmente automatizada vía WebService SOAP. A través de SAGEC se puede:

- Crear envíos de forma telemática (sin intervención manual)
- Generar etiquetas con código de barras para los paquetes
- Obtener números de seguimiento (tracking)
- Consultar el estado de los envíos
- Imprimir etiquetas en formato PDF o ZPL (impresoras térmicas)

#### Flujo de Generación de Etiqueta

```
1. Autenticación SOAP
   ├── CodigoFranquicia
   ├── CodigoAbonado
   ├── CodigoDepartamento
   └── Password
         │
         ▼
2. Llamada a GetEtiquetaEnvio
   ├── NumeroEnvio (nº de expedición)
   ├── SeparadorNumerosEnvio
   ├── FechaInicioEnvio / FechaFinEnvio
   └── TipoEtiquetaEnvio (PDF, ZPL)
         │
         ▼
3. Respuesta SOAP
   ├── EtiquetaFile (fichero Base64 con la etiqueta)
   └── EtiquetaFileZpl (etiqueta en formato ZPL)
         │
         ▼
4. Decodificar Base64 → Guardar/Imprimir PDF/ZPL
```

#### Ejemplo de Uso

```php
<?php
// Crear cliente SOAP con el WSDL de MRW
$client = new SoapClient('http://sagec.mrw.es/MRWEnvio.asmx?WSDL', [
    'trace'      => true,
    'exceptions' => true,
    'soap_version' => SOAP_1_2,
]);

// Cabecera de autenticación
$authHeader = new SoapHeader('http://www.mrw.es/', 'AuthInfo', [
    'CodigoFranquicia'    => 'TU_FRANQUICIA',
    'CodigoAbonado'       => 'TU_ABONADO',
    'CodigoDepartamento'  => 'TU_DEPARTAMENTO',
    'UserName'            => 'TU_USUARIO',
    'Password'            => 'TU_PASSWORD',
]);

$client->__setSoapHeaders($authHeader);

// Solicitar etiqueta
$response = $client->GetEtiquetaEnvio([
    'request' => [
        'NumeroEnvio'       => '123456789',
        'TipoEtiquetaEnvio' => 'PDF',
    ]
]);

// Guardar la etiqueta como PDF
$pdfContent = base64_decode($response->GetEtiquetaEnvioResult->EtiquetaFile);
file_put_contents('etiqueta_envio.pdf', $pdfContent);
```

---

## Estructura del Proyecto

```
php-soap-api/
├── mrw-generate-barcode/       # Ejemplo: MRW SAGEC generación de códigos de barras
│   ├── data_model.gif          # Captura de pantalla / diagrama del sistema
│   └── ...                     # Scripts PHP de ejemplo
├── composer.json                # Dependencias del proyecto
├── .gitignore
└── readme.md
```

> **Nota:** El repositorio está preparado para añadir más ejemplos de SOAP con otras empresas y servicios en carpetas adicionales.

---

## Cómo Funciona SOAP con PHP

PHP incluye la clase nativa `SoapClient` que permite consumir WebServices SOAP de forma sencilla:

### 1. Modo WSDL (Recomendado)

El cliente lee el archivo WSDL y genera automáticamente los métodos disponibles:

```php
$client = new SoapClient('http://ejemplo.com/servicio.asmx?WSDL');

// Ver métodos disponibles
print_r($client->__getFunctions());

// Ver tipos de datos
print_r($client->__getTypes());

// Llamar a un método
$resultado = $client->NombreDelMetodo(['param1' => 'valor1']);
```

### 2. Modo Non-WSDL

Para servicios sin WSDL publicado, se configura manualmente:

```php
$client = new SoapClient(null, [
    'location' => 'http://ejemplo.com/servicio',
    'uri'      => 'http://ejemplo.com/namespace',
    'trace'    => true,
]);
```

### 3. Depuración

```php
// Ver la última petición XML enviada
echo $client->__getLastRequest();

// Ver la última respuesta XML recibida
echo $client->__getLastResponse();
```

---

## Requisitos

- **PHP** >= 7.4 (con extensión `soap` habilitada)
- **Composer** >= 2.x
- Credenciales de acceso al WebService (proporcionadas por MRW u otro proveedor)

### Verificar Extensión SOAP

```bash
# Comprobar que la extensión está habilitada
php -m | grep soap

# Si no está instalada (Ubuntu/Debian)
sudo apt-get install php-soap
sudo systemctl restart apache2

# Si no está instalada (CentOS/RHEL)
sudo yum install php-soap
sudo systemctl restart httpd
```

---

## Instalación

```bash
# Clonar el repositorio
git clone https://github.com/david-berruezo/php-soap-api.git
cd php-soap-api

# Instalar dependencias
composer install
```

### Configurar Credenciales

Editar los archivos de ejemplo dentro de cada carpeta con las credenciales proporcionadas por el proveedor del WebService (MRW, etc.).

### Ejecutar un Ejemplo

```bash
# Ejecutar el script de generación de código de barras MRW
php mrw-generate-barcode/nombre_del_script.php
```

---

## Recursos

### PHP SOAP

- [PHP SoapClient — Documentación oficial](https://www.php.net/manual/en/class.soapclient.php)
- [PHP SoapHeader](https://www.php.net/manual/en/class.soapheader.php)
- [PHP SoapServer](https://www.php.net/manual/en/class.soapserver.php)
- [SOAP Tutorial — W3Schools](https://www.w3schools.com/xml/xml_soap.asp)
- [WSDL Tutorial — W3Schools](https://www.w3schools.com/xml/xml_wsdl.asp)

### MRW SAGEC

- [MRW — Sitio oficial](https://www.mrw.es/)
- [MRW SAGEC WebService — Endpoint](http://sagec.mrw.es/MRWEnvio.asmx)
- [MRW SAGEC — GetEtiquetaEnvio](http://sagec.mrw.es/MRWEnvio.asmx?op=GetEtiquetaEnvio)

### Estándares

- [SOAP 1.2 Specification — W3C](https://www.w3.org/TR/soap12/)
- [WSDL 1.1 Specification — W3C](https://www.w3.org/TR/wsdl.html)
- [XML Schema — W3C](https://www.w3.org/XML/Schema)

### Repositorios Relacionados

- [wprentals-ws-avantio](https://github.com/david-berruezo/wprentals-ws-avantio) — Plugin WordPress con consumo de WebServices SOAP (Avantio)
- [portvil-symfony-router-cms-avantio](https://github.com/david-berruezo/portvil-symfony-router-cms-avantio) — CMS Symfony con integración Avantio

---

## Autor

**David Berruezo** — Software Engineer | Fullstack Developer

- GitHub: [@david-berruezo](https://github.com/david-berruezo)
- Website: [davidberruezo.com](https://www.davidberruezo.com)
	
