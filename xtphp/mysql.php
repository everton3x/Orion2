<?php

class XtMysql {
	
	public static function connect(//conecta ao sgbd
		$host //(string) o host
		,$user //(string) usuário
		,$pass //(string) senha
		,$db //(string) nome do banco de dados
	){
		global $xterror;
		$conn = mysql_connect($host, $user, $pass);
		if($conn == false){
			$xterror = 4;
			return false;
		}else{
			$select = mysql_select_db($db);
			if($select == false){
				$xterror = 4;
				return false;
			}else{
				return true;
			}
		}
		
	}//eof connect()
	
	public static function qry(//executa consultas no sgbd
		$sql //(string) consulta sql
	){//return resource
		
		return mysql_query($sql);
		
	}//eof qry()
	
	public static function insert(//insere dados na tabela
		$table //(string) nome da tabela a eceber os dados
		,$data //(array) os dados a serem inseridos (linha => nomedo campo => valor a inserir)
		,$fields = false //(array) lista com nomes de campos
	){//return bool
	
		if(is_array($fields)){
			foreach($data as $line => $row){
				$i = 0;
				foreach($row as $f => $v){
					$row1[$fields[$i]] = $v;
					$i++;
				}
				$data1[$line] = $row1;
			}
			$data = $data1;
			$data1 = '';
			$row1 = '';
			$row = '';
		}
		
		foreach($data as $line => $row){
			foreach($row as $f => $v){
				$row1['`'.$f.'`'] = '\''.$v.'\'';
			}
			$data1[$line] = $row1;
		}
		
		foreach($data1 as $line => $row){
			$sql[$line] = 'INSERT INTO '.$table.'('.implode(', ', array_keys($row)).') VALUES ('.implode(', ', $row).');';
		}
		
		foreach($sql as $str){
			$result = XtMysql::qry($str);
			$return[] = $result;
		}
		
		return $return;
		
	}//eof insert()
	
	public static function update(//executa um update
		$table //(string) o nome da tabela
		,$data //(array) um store com os dados a serem atualizados
		,$ref //(string) nome do campo de referência
	){//return array
		
		foreach($data as $line => $row){
			foreach($row as $f => $v){
				if($f != $ref){
					$tmp[] = '`'.$f.'` = \''.$v.'\'';
				}
			}
			$set = implode(', ', $tmp);
			$tmp = '';
			
			$sql[$line] = 'UPDATE '.$table.' SET '.$set.' WHERE `'.$ref.'` = \''.$row[$ref].'\';';
		}
		
		foreach($sql as $str){
			$result[] = XtMysql::qry($str);
		}
		
		return $result;
	}//eof update()
	
	public static function delete(//deleta todos ou alguns registros
		$table //(string) nome da tabela
		,$ref = false //(string) nome do campo para referência do where ou false para apagar tudo
		,$delete = false //(array) array com os valores de referência para apagar
	){
		
		if($ref != false){
			foreach($delete as $v){
				$sql[] = 'DELETE FROM '.$table.' WHERE `'.$ref.'` = \''.$v.'\';';
			}
		}else{
			$sql[] = 'DELETE FROM '.$table.';';
		}
		
		foreach($sql as $str){
			$result[] = XtMysql::qry($str);
		}
		
		return $result;
		
	}//eof delete()
	
	public static function truncate(//apaga toda a tabela, reiniciando auto-numeração
		$table //(string) nome de tabela
	){//return bool
		$result = XtMysql::qry("TRUNCATE TABLE `$table`");
		
		return $result;
	}//eof truncate()
	
	public static function dumpTableFromODBC(//"copia" uma tabela para mysql
		$conn //(resource) conexão odbc
		,$db //(string) nome do banco de dados
		,$schema //(string) nome do schema
		,$table //(string) nome da tabela
		,$data = true //(bool) true para importar os dados junto
	){//return string
		$col = odbc_columns($conn, $db, $schema, $table, "%");

		$i = 0;

		$size = false;
		while($r = odbc_fetch_array($col))
		{
			switch($r['TYPE_NAME'])
			{
				case 'nvarchar':
					$type = 'varchar';
					break;
				case 'money':
					$type = 'float';
					break;
				case 'decimal':
					$type = 'float';
					break;
				case 'numeric':
					$type = 'float';
					break;
				case 'char':
					$type = 'text';
					break;
				case 'ntext':
					$type = 'longtext';
					$size = '';
					break;
				case 'datetime':
					$type = $r['TYPE_NAME'];
					$size = '';
					break;
				case 'time':
					$type = $r['TYPE_NAME'];
					$size = '';
					break;
				case 'date':
					$type = $r['TYPE_NAME'];
					$size = '';
					break;
				case 'image':
					$type = 'longblob';
					$size = '';
					break;
				default:
					$type = $r['TYPE_NAME'];
					break;
			}
			
			if($size === false)
			{
				$size = '('.$r['COLUMN_SIZE'].')';
			}
			
			$fields[$i] = '`'.$r['COLUMN_NAME'].'` '.$type.$size;
			$i++;
			$size = false;
			$type = '';
			$colunas[] = $r['COLUMN_NAME'];
		}
		
		$fields = implode(', ', $fields);

		$pk = odbc_primarykeys($conn, "GESPAM", $schema, $table);

		while($k = odbc_fetch_array($pk))
		{
			$pkey = $k['COLUMN_NAME'];
		}
		
		if($pkey)
		{
			$fields .= ', PRIMARY KEY ('.$pkey.')';
		}
		
		$sql = 'CREATE TABLE `'.$table.'` ('.$fields.') ENGINE=MyISAM';
		
		if($data == true)
		{
			$result = odbc_exec($conn, "SELECT * FROM $table");
			
			$no_fld = odbc_num_fields($result);
			
			while($r = odbc_fetch_array($result)){
				for($i = 1; $i <= $no_fld; $i++){
					$fld_name = odbc_field_name($result, $i);
					$valores[$i] = '"'.addslashes($r[$fld_name]).'"';
				}
				
				$insert[] = 'INSERT INTO '.$table.'('.implode(', ', $colunas).') VALUES('.implode(', ', $valores).');';
			}
			
			$sql .= '; '.implode(' ', $insert);
		}
		
		return $sql;
		
	}//eof dumpFromODBC()
	
	public static function dumpDbFromODBC(//"copia" um banco de dados para mysql
		$conn //(resource) conexão odbc
		,$db //(string) nome do banco de dados
		,$schema //(string) nome do schema
		,$data = true //(bool) true para importar os dados junto
	){//return string
		$result = odbc_tables($conn, $db, $schema);
		while($r = odbc_fetch_array($result))
		{
			$tables[] = $r['TABLE_NAME'];
		}
		
		foreach($tables as $tabela)
		{
			$sql[] = XtMysql::dumpTableFromODBC($conn, $db, $schema, $tabela, $data);
		}
		
		$sql = implode("; ", $sql);
		
		return $sql;
	}//eof dumpDbFromODBC()
}

?>
