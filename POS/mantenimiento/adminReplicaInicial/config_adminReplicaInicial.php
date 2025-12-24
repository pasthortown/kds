<?php

include_once"../../system/conexion/clase_sql.php";
include_once "../../clases/clase_adminReplicaInicial.php";
require_once "../../config_replica_inicial.php";
$dir = $path;
$lc_config   = new ReplicaInicial();


if(isset($_GET["verificarjson"])){
    $dbname=$_GET['nb'];
    $servername=$_GET['globalsrv'];
    $user=$_GET['globalusr'];
    $password=$_GET['globalpass'];
    $connectionInfo = array( "Database"=>$dbname, "UID"=>$user, "PWD"=>$password);
    $conn = sqlsrv_connect( $servername, $connectionInfo);
    if( $conn === false ) {
        die( print_r( sqlsrv_errors(), 0));
    }

    $sql = "IF EXISTS ( SELECT  1
            FROM    Information_schema.Routines
            WHERE   Specific_schema = 'dbo'
                    AND specific_name = 'JsonNVarChar'
                    AND Routine_Type = 'FUNCTION' ) 
					BEGIN
						SELECT 1 as Respuesta;
					END
					ELSE 
						BEGIN
							Select 0 as Respuesta;
					END";

    $stmt = sqlsrv_query( $conn, $sql);
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        $respuesta=$row['Respuesta'];
    }
    print json_encode($respuesta);

}
else
if(isset($_GET["CreardbVersions"])){
    $srv=$_GET['globalsrv'];
    $user=$_GET['globalusr'];
    $password=$_GET['globalpass'];
    $connectionInfo = array( "UID"=>$user, "PWD"=>$password);
    $conn = sqlsrv_connect( $srv, $connectionInfo);
    if( $conn === false ) {
        die( print_r( sqlsrv_errors(), 0));
    }
    $sql = "if exists(select *  from sys.server_triggers)
begin
select 0 AS Respuesta;
end
else 
begin
EXEC dbo.sp_executesql @statement = N'
CREATE TRIGGER [DB_VERSIONS_TRIGGER] ON ALL SERVER
FOR		CREATE_FUNCTION, ALTER_FUNCTION, DROP_FUNCTION,
		CREATE_PROCEDURE, ALTER_PROCEDURE, DROP_PROCEDURE,
		CREATE_TRIGGER, ALTER_TRIGGER, DROP_TRIGGER,
		CREATE_TABLE, ALTER_TABLE, DROP_TABLE
AS 
 SET NOCOUNT ON;
SET XACT_ABORT OFF;

BEGIN TRY
	DECLARE @data XML = EVENTDATA()
	DECLARE @server VARCHAR(100) = @data.value(''(/EVENT_INSTANCE/ServerName)[1]'',''VARCHAR(100)'')
	DECLARE @database VARCHAR(100) = @data.value(''(/EVENT_INSTANCE/DatabaseName)[1]'',''VARCHAR(100)'')
	DECLARE @user VARCHAR(100) = @data.value(''(/EVENT_INSTANCE/LoginName)[1]'',''VARCHAR(100)'')
	DECLARE @schema varchar(100) = @data.value(''(/EVENT_INSTANCE/SchemaName)[1]'',''VARCHAR(MAX)'')
	DECLARE @object VARCHAR(100) = @data.value(''(/EVENT_INSTANCE/ObjectName)[1]'',''VARCHAR(100)'')
	DECLARE @action VARCHAR(100) = @data.value(''(/EVENT_INSTANCE/EventType)[1]'',''VARCHAR(100)'')
	DECLARE @code VARCHAR(MAX) = @data.value(''(/EVENT_INSTANCE/TSQLCommand)[1]'',''VARCHAR(MAX)'')

	IF OBJECT_ID(''DB_VERSIONS.dbo.VERSIONS'') IS NOT NULL 
	BEGIN
		INSERT INTO DB_VERSIONS.dbo.VERSIONS([SERVER]
										   ,[DATABASENAME]
										   ,[LOGINNAME]
										   ,[SCHEMA]
										   ,[OBJECT]
										   ,[ACTION]
										   ,[CODE]
										   ,[FECHA])
				VALUES						(@server
											,@database
											,@user
											,@schema
											,@object
											,@action
											,ISNULL(@code,''NA'')
											,GETDATE()
											)
	END

END TRY 
BEGIN CATCH 

END CATCH
'
select 1 AS Respuesta
end

";

    $stmt = sqlsrv_query( $conn, $sql);
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        $respuesta=$row['Respuesta'];
    }
    print json_encode($respuesta);

}

else
if(isset($_GET["Conexion"])){
    $server=$_GET['srv'];
    $user=$_GET['usr'];
    $password=$_GET['pass'];
    $connectionTimeoutSeconds=10;
    $connectionInfo = array("UID"=>$user, "PWD"=>$password,"LoginTimeout" => $connectionTimeoutSeconds);
    $conn = sqlsrv_connect( $server, $connectionInfo);
    if( $conn ) {
        print json_encode(1);
    }else{
        print json_encode(0);
    }

}
else
if(isset($_GET["VerificarExistenciaDirectorio"])){
    $servername=$_GET['globalsrv'];
    $user=$_GET['globalusr'];
    $password=$_GET['globalpass'];
    $directorio=$_GET['directorio'];
    $disco=substr($directorio,0,2);
    $connectionInfo = array("UID"=>$user, "PWD"=>$password);
    $conn = sqlsrv_connect( $servername, $connectionInfo);
    if( $conn === false ) {
        die( print_r( sqlsrv_errors(), 0));
    }
    $sql = "exec master.dbo.xp_fileexist '".$disco."'";
    $stmt = sqlsrv_query( $conn, $sql);
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        $respuesta=$row['File is a Directory'];
    }
    print json_encode($respuesta);
}
else
if(isset($_GET["crearDirectorio"])){
    $servername=$_GET['globalsrv'];
    $user=$_GET['globalusr'];
    $password=$_GET['globalpass'];
    $directorio=$_GET['directorio'];
    $connectionInfo = array("UID"=>$user, "PWD"=>$password);
    $conn = sqlsrv_connect( $servername, $connectionInfo);
    if( $conn === false ) {
        die( print_r( sqlsrv_errors(), 0));
    }
    $sql = "EXECUTE master.dbo.xp_create_subdir '".$directorio."'";
    $stmt = sqlsrv_query( $conn, $sql);
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        $respuesta=$row['File is a Directory'];
    }
    print json_encode($respuesta);
}
else
if(isset($_GET["VerificarIns"])){
    $servername=$_GET['globalsrv'];
    $user=$_GET['globalusr'];
    $password=$_GET['globalpass'];
    $connectionInfo = array("UID"=>$user, "PWD"=>$password);
    $conn = sqlsrv_connect( $servername, $connectionInfo);
    if( $conn === false ) {
        die( print_r( sqlsrv_errors(), 0));
    }
    $sql = "if exists(SELECT * FROM sys.databases WHERE name like 'MAXPOINT_%'and name not like 'MAXPOINT_LOG')
            begin 
            select 1 as 'Respuesta'
            end
            else 
            begin
            select 0 as 'Respuesta'
            end
        ";
    $stmt = sqlsrv_query( $conn, $sql);
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        $respuesta=$row['Respuesta'];
    }
    print json_encode($respuesta);
}

else
if(isset($_GET["DbVersionsSqlcmd"])){
    $srv=$_GET['globalsrv'];
    $user=$_GET['globalusr'];
    $password=$_GET['globalpass'];
    shell_exec("sqlcmd -U $user -P $password -S $srv -i $dir\scripts\script2modificado.sql -o $dir\scripts\script2respuesta.txt");
    $ruta=''.$dir.'\scripts\script2respuesta.txt';
    if(file_exists($ruta)){
        $líneas = file($ruta);
        print json_encode($líneas);
    }else{
        print json_encode('Error no se ejecuto el Script');
    }

}
else
if(isset($_GET["Maxpoint_log"])){
    $srv=$_GET['globalsrv'];
    $user=$_GET['globalusr'];
    $password=$_GET['globalpass'];
    shell_exec("sqlcmd -U $user -P $password -S $srv -i $dir\scripts\script3modificado.sql -o $dir\scripts\script3respuesta.txt");
    $ruta=''.$dir.'\scripts\script3respuesta.txt';
    if(file_exists($ruta)){
        $líneas = file($ruta);
        print json_encode($líneas);
    }else{
        print json_encode('Error no se ejecuto el Script');
    }
}
else
if(isset($_GET["CreacionBasePrueba"])){
    $srv=$_GET['globalsrv'];
    $user=$_GET['globalusr'];
    $password=$_GET['globalpass'];
    shell_exec("sqlcmd -U $user -P $password -S $srv -i $dir\\scripts\\4\\CreacionBaseModificado.sql -o $dir\\scripts\\4\\script4.0respuesta.txt");
    print json_encode(1);
}

else
if(isset($_GET["abrirArchivo"])){
    $cadena=$_GET['cadena'];
    $tienda=$_GET['tienda'];
    $nb=$_GET['nb'];
    $rdata=$_GET['rdataVer'];
    $rhistor=$_GET['rhistoVer'];
    $file=''.$dir.'\scripts\4\4.0CreacionBase.sql';
    $lineas = file_get_contents($file);
    $cadena=':setvar idCadena "'.$cadena.'"';
    $tienda=':setvar idTienda "'.$tienda.'"';
    $nombrebase=':setvar DatabaseName "'.$nb.'"';
    $filepre=':setvar DefaultFilePrefix "'.$nb.'"';
    $path=':setvar DefaultDataPath "'.$rdata.'"';
    $log=':setvar DefaultLogPath "'.$rhistor.'"';
    $s=str_replace(':setvar idCadena ""',$cadena,$lineas);
    $a=str_replace(':setvar idTienda ""',$tienda,$s);
    $b=str_replace(':setvar DatabaseName ""',$nombrebase,$a);
    $c=str_replace(':setvar DefaultFilePrefix ""',$filepre,$b);
    $d=str_replace(':setvar DefaultDataPath ""',$path,$c);
    $ultimo=str_replace(':setvar DefaultLogPath ""',$log,$d);
    $borrar=fopen(''.$dir.'\scripts\4\CreacionBaseModificado.sql',"a");
    fwrite($borrar, "");
    fclose($borrar);
    $fp = fopen(''.$dir.'\scripts\4\CreacionBaseModificado.sql',"a");
    fwrite($fp, $ultimo . PHP_EOL);
    fclose($fp);
    print json_encode(1);
}

else
if(isset($_GET["modificarS2"])){
    $rutaData=$_GET['rdataVers'];
    $rutaHistorial=$_GET['rhistoVers'];
    $file=''.$dir.'\scripts\script2.sql';
    $lineas = file_get_contents($file);
    $rutaDat=':setvar DefaultDataPath "'.$rutaData.'"';
    $rutaHist=':setvar DefaultLogPath "'.$rutaHistorial.'"';
    $s=str_replace(':setvar DefaultDataPath ""',$rutaDat,$lineas);
    $a=str_replace(':setvar DefaultLogPath ""',$rutaHist,$s);
    $borrar=fopen(''.$dir.'\scripts\script2modificado.sql',"a");
    fwrite($borrar, "");
    fclose($borrar);
    $fp = fopen(''.$dir.'\scripts\script2modificado.sql',"a");
    fwrite($fp, $a . PHP_EOL);
    fclose($fp);
    print json_encode(1);
}
else
if(isset($_GET["modificarS3"])){
    $rData=$_GET['rdataVer'];
    $rHist=$_GET['rhistoVer'];
    $file=''.$dir.'\scripts\script3.sql';
    $lineas = file_get_contents($file);
    $rutaDat=':setvar DefaultDataPath "'.$rData.'"';
    $rutaHist=':setvar DefaultLogPath "'.$rHist.'"';
    $s=str_replace(':setvar DefaultDataPath ""',$rutaDat,$lineas);
    $a=str_replace(':setvar DefaultLogPath ""',$rutaHist,$s);
    $borrar=fopen(''.$dir.'\scripts\script3modificado.sql',"a");
    fwrite($borrar, "");
    fclose($borrar);
    $fp = fopen(''.$dir.'\scripts\script3modificado.sql',"a");
    fwrite($fp, $a . PHP_EOL);
    fclose($fp);
    print json_encode(1);
}
else
if(isset($_GET["Schemas"])){
    $srv=$_GET['globalsrv'];
    $user=$_GET['globalusr'];
    $password=$_GET['globalpass'];
    $lc_condiciones[2] = $_GET['nb'];
    shell_exec("sqlcmd -U $user -P $password -S $srv -d $lc_condiciones[2] -i $dir\\scripts\\4\\4.1EsquemasYTablas.sql -o $dir\\scripts\\4\\EsquemasYTablas_Resp.txt");
    print json_encode(1);
}

else
if(isset($_GET["Funciones"])){
    $srv=$_GET['globalsrv'];
    $user=$_GET['globalusr'];
    $password=$_GET['globalpass'];
    $lc_condiciones[2] = $_GET['nb'];
    shell_exec("sqlcmd -U $user -P $password -S $srv -d $lc_condiciones[2] -i $dir\\scripts\\4\\4.2Funciones.sql -o $dir\\scripts\\4\\Funciones_Resp.txt");
    print json_encode(1);
}
else
if(isset($_GET["Vistas"])){
    $srv=$_GET['globalsrv'];
    $user=$_GET['globalusr'];
    $password=$_GET['globalpass'];
    $lc_condiciones[2] = $_GET['nb'];
    shell_exec("sqlcmd -U $user -P $password -S $srv -d $lc_condiciones[2] -i $dir\\scripts\\4\\4.3Vistas.sql -o $dir\\scripts\\4\\Vistas_Resp.txt");
    print json_encode(1);
}
else
if(isset($_GET["Sps"])){
    $srv=$_GET['globalsrv'];
    $user=$_GET['globalusr'];
    $password=$_GET['globalpass'];
    $lc_condiciones[2] = $_GET['nb'];
    shell_exec("sqlcmd -U $user -P $password -S $srv -d $lc_condiciones[2] -i $dir\\scripts\\4\\4.4SPS.sql -o $dir\\scripts\\4\\Sps_Resp.txt");
    print json_encode(1);
}
else
if(isset($_GET["Users"])){
    $cadena=$_GET['cadena'];
    $tienda=$_GET['tienda'];
    $nombreb=$_GET['nb'];
    $rdata=$_GET['rdataVer'];
    $rhistor=$_GET['rhistoVer'];
    $file=''.$dir.'\scripts\4\4.5Usuarios.sql';
    $lineas = file_get_contents($file);
    $cadena=':setvar idCadena "'.$cadena.'"';
    $tienda=':setvar idTienda "'.$tienda.'"';
    $nombrebase=':setvar DatabaseName "'.$nombreb.'"';
    $filepre=':setvar DefaultFilePrefix "'.$nombreb.'"';
    $rutaDat=':setvar DefaultDataPath "'.$rdata.'"';
    $rutaHist=':setvar DefaultLogPath "'.$rhistor.'"';
    $borrar=fopen(''.$dir.'\scripts\4\UsuariosmodificadoLocal.sql',"a");
    fwrite($borrar, "");
    fclose($borrar);
    $s=str_replace(':setvar idCadena ""',$cadena,$lineas);
    $a=str_replace(':setvar idTienda ""',$tienda,$s);
    $b=str_replace(':setvar DatabaseName ""',$nombrebase,$a);
    $c=str_replace(':setvar DefaultFilePrefix ""',$filepre,$b);
    $d=str_replace(':setvar DefaultDataPath ""',$rutaDat,$c);
    $ultimo=str_replace(':setvar DefaultLogPath ""',$rutaHist,$d);
    $fp = fopen(''.$dir.'\scripts\4\UsuariosmodificadoLocal.sql',"a");
    fwrite($fp, $ultimo . PHP_EOL);
    fclose($fp);
    print json_encode(1);
}
else
if(isset($_GET["Ejecutar4"])){
    $srv=$_GET['globalsrv'];
    $user=$_GET['globalusr'];
    $password=$_GET['globalpass'];
    $lc_condiciones[2]=$_GET['nb'];
    shell_exec("sqlcmd -U $user -P $password -S $srv -d $lc_condiciones[2]   -i $dir\\scripts\\4\\UsuariosmodificadoLocal.sql -o $dir\\scripts\\4\\Users_Resp.txt");
    print json_encode(1);
}
else
if(isset($_GET["replicaInicial"])){
    $lc_condiciones[0]=$_GET['cadena'];
    $lc_condiciones[1]=$_GET['tienda'];
    $lc_condiciones[2]=$_GET['nb'];
    $file=''.$dir.'\scripts\script5.sql';
    $lineas = file_get_contents($file);
    $storep='EXEC dbo.USP_ReplicacionInicial @idCadena = '.$lc_condiciones[0].', @idRestaurante = '.$lc_condiciones[1].'';
    $a=str_replace('EXEC dbo.USP_ReplicacionInicial @idCadena = 0, @idRestaurante = 0',$storep,$lineas);
    $borrar=fopen(''.$dir.'\scripts\ReplicaBajaDatosLocal.sql',"a");
    fwrite($borrar, "");
    fclose($borrar);
    $fp = fopen(''.$dir.'\scripts\ReplicaBajaDatosLocal.sql',"a");
    fwrite($fp, $a . PHP_EOL);
    fclose($fp);
    print json_encode($a);
}
else
if(isset($_GET["BajarDatos"])){
    $srv=$_GET['globalsrv'];
    $user=$_GET['globalusr'];
    $password=$_GET['globalpass'];
    $lc_condiciones[2]=$_GET['nb'];
    shell_exec("sqlcmd -U $user -P $password -S $srv -d $lc_condiciones[2]   -i $dir\\scripts\\ReplicaBajaDatosLocal.sql -o $dir\\scripts\\script5respuesta.txt");
    print json_encode(1);
}
else
if(isset($_GET["datareportes"])){
    $srv=$_GET['globalsrv'];
    $user=$_GET['globalusr'];
    $password=$_GET['globalpass'];
    $lc_condiciones[2]=$_GET['nb'];
    shell_exec("sqlcmd -U $user -P $password -S $srv -d $lc_condiciones[2]   -i $dir\\scripts\\script6.sql -o $dir\\scripts\\script6respuesta.txt");
    print json_encode(1);
}
else
if(isset($_GET["PeriodoInicial"])){
    $lc_condiciones[1]=$_GET['tienda'];
    $file=''.$dir.'\scripts\script7.sql';
    $lineas = file_get_contents($file);
    $cambio='DECLARE @idTienda INT = '.$lc_condiciones[1].'';
    $a=str_replace('DECLARE @idTienda INT = 0',$cambio,$lineas);
    $borrar=fopen(''.$dir.'\scripts\PeriodoInicialLocal.sql',"a");
    fwrite($borrar, "");
    fclose($borrar);
    $fp = fopen(''.$dir.'\scripts\PeriodoInicialLocal.sql',"a");
    fwrite($fp, $a . PHP_EOL);
    fclose($fp);
    print json_encode(1);
}
else
if(isset($_GET["EjecutaPeriodoIni"])){
    $srv=$_GET['globalsrv'];
    $user=$_GET['globalusr'];
    $password=$_GET['globalpass'];
    $lc_condiciones[2]=$_GET['nb'];
    shell_exec("sqlcmd -U $user -P $password -S $srv -d $lc_condiciones[2]   -i $dir\\scripts\\PeriodoInicialLocal.sql -o $dir\\scripts\\script7respuesta.txt");
    print json_encode(1);
}

else
if(isset($_GET["CreacionUreportes"])){
    $lc_condiciones[2]=$_GET['nb'];
    $file=''.$dir.'\scripts\script9.sql';
    $lineas = file_get_contents($file);
    $cambio=':setvar DatabaseName  "'.$lc_condiciones[2].'"';
    $a=str_replace(':setvar DatabaseName ""',$cambio,$lineas);
    $borrar=fopen(''.$dir.'\scripts\UsuariosReportes.sql',"a");
    fwrite($borrar, "");
    fclose($borrar);
    $fp = fopen(''.$dir.'\scripts\UsuariosReportes.sql',"a");
    fwrite($fp, $a . PHP_EOL);
    fclose($fp);
    print json_encode(1);
}
else
if(isset($_GET["CorrerUreportes"])){
    $lc_condiciones[2]=$_GET['nb'];
    $srv=$_GET['globalsrv'];
    $user=$_GET['globalusr'];
    $password=$_GET['globalpass'];
    shell_exec("sqlcmd -U $user -P $password -S $srv -d $lc_condiciones[2]   -i $dir\\scripts\\UsuariosReportes.sql -o $dir\\scripts\\script9respuesta.txt");
    print json_encode(1);
}
else
if(isset($_GET["CorrerPermisos"])){
    $lc_condiciones[2]=$_GET['nb'];
    $srv=$_GET['globalsrv'];
    $user=$_GET['globalusr'];
    $password=$_GET['globalpass'];
    shell_exec("sqlcmd -U $user -P $password -S $srv -d $lc_condiciones[2]   -i $dir\\scripts\\script10.sql -o $dir\\scripts\\script10respuesta.txt");

    print json_encode(1);
}
else
if(isset($_GET["CorrerEstacion"])){
    $lc_condiciones[2]=$_GET['nb'];
    $srv=$_GET['globalsrv'];
    $user=$_GET['globalusr'];
    $password=$_GET['globalpass'];
    shell_exec("sqlcmd -U $user -P $password -S $srv -d $lc_condiciones[2]   -i $dir\\scripts\\script11.sql -o $dir\\scripts\\script11respuesta.txt");
    print json_encode(1);
}
else
if(isset($_GET["BorrarTodos"])){

    unlink(''.$dir.'\scripts\4\script4.0respuesta.txt');
    unlink(''.$dir.'\scripts\4\CreacionBaseModificado.sql');
    unlink(''.$dir.'\scripts\4\EsquemasYTablas_Resp.txt');
    unlink(''.$dir.'\scripts\4\Funciones_Resp.txt');
    unlink(''.$dir.'\scripts\4\Vistas_Resp.txt');
    unlink(''.$dir.'\scripts\4\Sps_Resp.txt');
    unlink(''.$dir.'\scripts\4\Users_Resp.txt');
    unlink(''.$dir.'\scripts\ReplicaBajaDatosLocal.sql');
    unlink(''.$dir.'\scripts\UsuariosReportes.sql');
    unlink(''.$dir.'\scripts\PeriodoInicialLocal.sql');
    unlink(''.$dir.'\scripts\4\Users_Resp.txt');
    unlink(''.$dir.'\scripts\script5respuesta.txt');
    unlink(''.$dir.'\scripts\script6respuesta.txt');
    unlink(''.$dir.'\scripts\script7respuesta.txt');
    unlink(''.$dir.'\scripts\script9respuesta.txt');
    unlink(''.$dir.'\scripts\script10respuesta.txt');
    unlink(''.$dir.'\scripts\script11respuesta.txt');
    unlink(''.$dir.'\scripts\4\UsuariosmodificadoLocal.sql');
}
else
if(isset($_GET['BorrarTArchivos'])){
    $scriptsDir = trim($dir . '\scripts\ '); // Asegúrate de que $dir termine sin una barra inclinada
    $filesToDelete = [
        '4\script4.0respuesta.txt',
        '4\CreacionBaseModificado.sql',
        '4\UsuariosmodificadoLocal.sql',
        '4\EsquemasYTablas_Resp.txt',
        '4\Funciones_Resp.txt',
        '4\Vistas_Resp.txt',
        '4\Sps_Resp.txt',
        '4\Users_Resp.txt',
        'ReplicaBajaDatosLocal.sql',
        'script2modificado.sql',
        'script3modificado.sql',
        'UsuariosReportes.sql',
        'PeriodoInicialLocal.sql',
        '4\Users_Resp.txt',
        'script5respuesta.txt',
        'script6respuesta.txt',
        'script7respuesta.txt',
        'script9respuesta.txt',
        'script10respuesta.txt',
        'script11respuesta.txt',
        'script1respuesta.txt',
        'script2respuesta.txt',
        'script3respuesta.txt'
    ];
    foreach ($filesToDelete as $file) {
        deleteFileIfExists($scriptsDir . $file);
    }
    unset($scriptsDir,$filesToDelete,$file);

}

else
if(isset($_GET["borrarArchivosInicio"])){
    unlink(''.$dir.'\scripts\script2modificado.sql');
    unlink(''.$dir.'\scripts\script2respuesta.txt');

}
else
if(isset($_GET["BorrarArchivos3"])){
    unlink(''.$dir.'\scripts\script3modificado.sql');
    unlink(''.$dir.'\scripts\script3respuesta.txt');
}
else
if(isset($_GET["cargarRestaurante"])){
    $tienda=$_GET['tienda'];
    $data = '';
    $data_string = json_encode($data);
    $ch = curl_init('http://maxpointservices.kfc.com.ec:8080/GerenteNacional.ServiciosWeb/webresources/restaurantes/cargarrestaurantesporcadena?cadena='.$tienda);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
    );
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $result = curl_exec($ch);
    curl_close($ch);
    $arr = json_decode($result,true);
    usort(
        $arr['restaurantes'],
        function($a,$b){ return $a['codTienda'] < $b['codTienda'] ? -1 : 1;}
    );
    print(json_encode($arr));
}
else
if(isset($_GET["cargarCadena"])){
    $data = '';
    $data_string = json_encode($data);
    $ch = curl_init('http://maxpointservices.kfc.com.ec:8080/GerenteNacional.ServiciosWeb/webresources/cadenas/cargarcadenas/');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
    );
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $result = curl_exec($ch);
    curl_close($ch);
    $arr = json_decode($result,true);
    usort(
        $arr['cadenas'],
        function($a,$b){ return $a['descripcion'] < $b['descripcion'] ? -1 : 1;}
    );
    print(json_encode($arr));
}
else if(isset($_GET["VerificarReplicaEnTranscurso"])){
    $ruta1=''.$dir.'\scripts\4\script4.0respuesta.txt';
    $ruta2=''.$dir.'\scripts\script5respuesta.txt';
    $ruta3=''.$dir.'\scripts\script11respuesta.txt';
    if(file_exists($ruta3)){
        print( json_encode(3));
    }else if(file_exists($ruta2)) {
        print( json_encode(2));
    }else if(file_exists($ruta1)) {
        print( json_encode(4));
    }else{
        print (json_encode(0));
    }
} else
    if (isset($_GET["CrearLinkedServer"])) {
        $srv = $_GET['globalsrv'];
        $user = $_GET['globalusr'];
        $password = $_GET['globalpass'];
        shell_exec("sqlcmd -U $user -P $password -S $srv -i $dir\\scripts\\LinkedServers\\1.creacionLS.sql -o $dir\\scripts\\LinkedServers\\creacionLSresp.txt");
        print(json_encode(1));
    }

    function deleteFileIfExists($filePath) {
        if (file_exists($filePath)) {
            unlink($filePath);
        } else {
            echo "El archivo no existe: " . $filePath . "<br>";
        }
    }