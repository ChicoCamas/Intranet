<?php

/*
 * Creado: 04/12/2014
 *
 * Proyecto BackOffices Charanga
 * Pagina: actualizar_stock_tonline.php
 * version: 0.01
 * Descripcion: Se actuliza el stock de la tienda online.
 *
 * Control Modificaciones
 * ID		fecha		Usuario			Descripcion
 *
 */

//Incluir Fichero Configuracion
include '../includes/php/config.inc.php';

//ConectarBD
$con_db = chg_conect_mssql_db(C_USER_MSSQL, C_PASS_MSSQL, C_HOST_MSSQL, C_BD_MSSQL);

$ArrayDatos = 0;

    
            $sql_stock =  "select ('00000'+rtrim(da.cod_barras) + dbo.digito_control_ean8(da.cod_barras)+';001'+';0865;'+convert(varchar,replace(sum(s.cantidad),'.',','))+';00032797;'+al.ECI_NUMERO_CENTRO+';'+format(getdate(),'yyyyMMddhhmmss')) as dato
                from clientes c , stockdiario s, articulos a , detalles_Articulos da, textil.dbo.almacen al
                where a.referencia = s.idmodelo and s.idmodelo = da.referencia and s.idcolor = da.color and s.idtalla = da.talla 
                and a.TEMPORADA in (108) and s.idalmacen = c.cod and c.zona =6  and s.cantidad > 0
                and c.estado = 'A' and al.IDALMACEN = c.cod 
                group by da.cod_barras , al.ECI_NUMERO_CENTRO";
    

     $aSQLParametros = array(
        'param_temporada' => getGet('idtemporada',getPost('idtemporada'))
    );
    $rsConsulta = chg_mssql_query($con_db, $sql_stock,$aSQLParametros);
    $ArrayDatos = chg_mssql_rs2arr($rsConsulta);

    //Hacer fichero asdfasdasd
    
       
    
    //$fecha = strtotime("-1 day");
    //$fecha1 = date('Ymd',$fecha);
        
    //die;
    
    $file = fopen('InventoryIF_00032797.txt', "w");
    
   for ($x=0;$x<count($ArrayDatos);$x++){
        fwrite($file, $ArrayDatos[$x]['dato']. PHP_EOL);    
   }
    fclose($file);
   
        //Recuperar ficheros ftp
        $ftp_server = "ftpcorp.elcorteingles.com";
        $ftp_user = "Charanga";
        $ftp_pass = "22char18";     
        $local_file ='InventoryIF_00032797.txt';
        $server_file = 'InventoryIF_00032797.txt';
        
        

        // establecer una conexi�n o finalizarla
        $conn_id = ftp_connect($ftp_server) or die("No se pudo conectar a $ftp_server");

        
        if (@ftp_login($conn_id, $ftp_user, $ftp_pass)) {
          if (ftp_chdir($conn_id, "dejar")) {
             echo "El directorio actual es: " . ftp_pwd($conn_id) . "\n";
              if (@ftp_put($conn_id, $server_file, $local_file, FTP_ASCII)) {                
                echo "Subida correcta.".$server_file;
              } else {
               // print_r(error_get_last());
                echo "Ha habido un problema\n";
              }
          } else { 
            echo "No se pudo cambiar al directorio\n";
        }         
          }  else {
            echo "No se pudo conectar como $ftp_user\n";
        }   
        //cerrar la conexi�n ftp
        ftp_close($conn_id);
?>