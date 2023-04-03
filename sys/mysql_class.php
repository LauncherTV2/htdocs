<?php
class db
{
	var $db_id = false;
	var $connected = false;
	var $query_num = 0;
	var $query_list = array();
	var $mysql_error = '';
	var $mysql_version = '';
	var $mysql_error_num = 0;
	var $mysql_extend = "MySQL";
	var $MySQL_time_taken = 0;
	var $query_id = false;
	
	function connect($db_user, $db_pass, $db_name, $db_location = 'localhost', $show_error=false)
	{
		if(!$this->db_id = @mysql_connect($db_location, $db_user, $db_pass)) {
			if($show_error) {
				$this->display_error(mysql_error(), mysql_errno());
			} else {
				return false;
			}
		} 

		if(!@mysql_select_db($db_name, $this->db_id)) {
			if($show_error) {
				$this->display_error(mysql_error(), mysql_errno());
			} else {
				return false;
			}
		}

		$this->mysql_version = mysql_get_server_info();

		if(!defined('COLLATE'))
		{ 
			define ("COLLATE", "cp1251");
		}

		if (version_compare($this->mysql_version, '4.1', ">=")) mysql_query("/*!40101 SET NAMES '" . COLLATE . "' */");

		$this->connected = true;

		return true;
	}
	
	function query($query, $show_error=false)
	{
		$time_before = $this->get_real_time();

		if(!$this->connected) $this->connect(DBUSER, DBPASS, DBNAME, DBHOST);
		
		if(!($this->query_id = mysql_query($query, $this->db_id) )) {

			$this->mysql_error = mysql_error();
			$this->mysql_error_num = mysql_errno();

			if($show_error) {
				$this->display_error($this->mysql_error, $this->mysql_error_num, $query);
			}
		}

		$this->MySQL_time_taken += $this->get_real_time() - $time_before;


			$this->query_list[] = array( 'time'  => ($this->get_real_time() - $time_before),
										 'query' => $query,
										 'num'   => (count($this->query_list) + 1));

		$this->query_num ++;

		return $this->query_id;
	}
	
	function get_row($query_id = '')
	{
		if ($query_id == '') $query_id = $this->query_id;

		return @mysql_fetch_assoc($query_id);
	}

	function get_array($query_id = '')
	{
		if ($query_id == '') $query_id = $this->query_id;

		return mysql_fetch_array($query_id);
	}
	
	
	function super_query($query, $multi = false)
	{

		if(!$multi) {

			$this->query($query);
			$data = $this->get_row();
			$this->free();			
			return $data;

		} else {
			$this->query($query);
			
			$rows = array();
			while($row = $this->get_row()) {
				$rows[] = $row;
			}

			$this->free();			

			return $rows;
		}
	}
	
	function num_rows($query_id = '')
	{

		if ($query_id == '') $query_id = $this->query_id;

		return mysql_num_rows($query_id);
	}
	
	function insert_id()
	{
		return mysql_insert_id($this->db_id);
	}

	function get_result_fields($query_id = '') {

		if ($query_id == '') $query_id = $this->query_id;

		while ($field = mysql_fetch_field($query_id))
		{
            $fields[] = $field;
		}
		
		return $fields;
   	}

	function safesql( $source )
	{
		if ($this->db_id) return mysql_real_escape_string ($source, $this->db_id);
		else return mysql_escape_string($source);
	}

	function free( $query_id = '' )
	{

		if ($query_id == '') $query_id = $this->query_id;

		@mysql_free_result($query_id);
	}

	function close()
	{
		@mysql_close($this->db_id);
	}

	function get_real_time()
	{
		list($seconds, $microSeconds) = explode(' ', microtime());
		return ((float)$seconds + (float)$microSeconds);
	}	
    
    function system_error($message){

      $errortomail = FALSE;
      $errortofile = FALSE;

      $system_operator_mail = 'wolf@promake.me';
      $system_from_mail = '[WARNING]@promake.me';

      $serror=
      "[ Сервер: ".$_SERVER['SERVER_NAME']." ]\r\n" .
      "[ IP Клиента: ".$_SERVER["REMOTE_ADDR"]." ]\r\n" .
      "[ Дата: ".Date('d-m-Y H:i:s')." ]\r\n" .
      "[ Скрипт: http://".$_SERVER["HTTP_HOST"].$_SERVER['PHP_SELF']."?".$_SERVER["QUERY_STRING"]." ]\r\n" .
      "\r\n" . $message ."\r\n\r\n";

      if($errortofile){
        $fhandle = fopen('logs/DateBase.'.date('Y-m-d').'.txt','a+');
        if($fhandle){
          fwrite($fhandle, $serror);
          fclose(($fhandle));
         }
      }
      if($errortomail) mail($system_operator_mail, 'ERROR '.$_SERVER['PHP_SELF']." ".Date('d-m-Y H:i:s'), $serror, 'From: ' . $system_from_mail);
    }

	function display_error($error, $error_num, $query = '')
	{
        $this->system_error("Ошибка запроса (код: ".$error_num."): \"".$error."\"\r\n\r\nЗапрос: \"".$query."\"\r\n\r\n");
		if($query) {
			// Safify query
			$query = preg_replace("/([0-9a-f]){32}/", "********************************", $query); // Hides all hashes
			$query_str = "$query";
		}

		echo '<?xml version="1.0" encoding="iso-8859-1"?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<title>Ошибка сервера Базы Данных MySQL!</title>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
		<style type="text/css">
		<!--
		body {
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 10px;
			font-style: normal;
			color: #000000;
		}
		-->
		</style>
		</head>
		<body>
			<font size="4">Ошибка сервера Базы Данных MySQL!</font>
			<br />------------------------<br />
			<br />

			<u>Ошибка:</u>
			<br />
				<strong>'.$error.'</strong>

			<br /><br />
			</strong><u>Номер ошибки:</u>
			<br />
				<strong>'.$error_num.'</strong>
			<br />
				<br />

			<textarea name="" rows="10" cols="52" wrap="virtual">'.$query_str.'</textarea><br />

            <br />
            <b><a href="mailto:wolf@promake.me">Send report for wolf@promake.me</a></b>

            <p style="font-size:11px;border:1px solid #990000;padding:5px;margin:5px;width:99%;">';
            ob_start();
            debug_print_backtrace();
            $trace = ob_get_contents();
            ob_end_clean();
            echo str_replace('called at','<br>called at',implode('<br><br>',explode("\n",$trace)));
            echo '
            </p>
		</body>
		</html>';
		
		exit();
	}
	
	function query_prepare($array) {
	    return call_user_func_array('sprintf', $array);
	}
 
	// Return results of the query as 2D array
	function query_multi() {
	    $query = $this->query_prepare(func_get_args());
	    $result = mysql_query($query, $this->db_id);
	    $rows = array();
	    while($row = mysql_fetch_array($result)) {
	        $rows[] = $row;
	    }
	    mysql_free_result($result);
	    return $rows;
	}
	
	function query_single() {
	    $query = $this->query_prepare(func_get_args());
	    $result = mysql_query($query, $this->db_id);
	    $row = mysql_fetch_array($result);
	    mysql_free_result($result);
	    return $row;
	}
	
function query_value() {
        $query = $this->query_prepare(func_get_args());
        $result = mysql_query($query, $this->db_id);
        if (!$result) {
            return false;
        }
        $row = mysql_fetch_row($result);
        mysql_free_result($result);
        return $row[0];
    }
	
	function query_affected() {
	    $query = $this->query_prepare(func_get_args());
	    mysql_query($query, $this->db_id);
	    return mysql_affected_rows();
	}

}


?>