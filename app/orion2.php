<?php
$error = 0;

require XTPHP.'utils.php';
require XTPHP.'store.php';
require XTPHP.'mysql.php';

class Orion
//Classe principal do Orion2
{
	public static function loadProject(//carrega as configurações do projeto
		$file = false//(string) Caminho para o arquivo xml do projeto
	)//return array retorna um array com as configurações do projeto
	{
		global $error;//define $error como variável global
		
		if(!$file){$error = 1; return false;}//testa se $file foi recebida
		if(!is_string($file)){$error = 2; return false;}//testa se $file é uma string
		if(!file_exists($file)){$error = 3; return false;}//testa se o arquivo $file existe
		
		if(!$xml = simplexml_load_file($file)){$error = 4; return false;}//carrega o arquivo xml
		
		$cfg = array();//define a variável de configuração
		
		$project_name = $xml['name'];
		settype($project_name, 'string');
		$cfg['project_name'] = $project_name;
		
		$source_type = $xml->source_type;
		settype($source_type, 'string');
		$cfg['source_type'] = $source_type;
		
		$output = $xml->output;
		settype($output, 'string');
		$cfg['output'] = $output;
		
		$description = $xml->description;
		settype($description, 'string');
		$cfg['description'] = $description;
		
		$filter_type = $xml->filter['type'];
		settype($filter_type, 'string');
		$cfg['filter']['type'] = $filter_type;
		
		$filter_content = $xml->filter;
		settype($filter_content, 'string');
		$cfg['filter']['content'] = $filter_content;
		
		$csv_delimiter = $xml->csv['delimiter'];
		settype($csv_delimiter, 'string');
		$cfg['csv']['delimiter'] = $csv_delimiter;
		
		$csv_text = $xml->csv;
		settype($csv_text, 'string');
		$cfg['csv']['text'] = $csv_text;
		
		$sql_table = $xml->sql['table'];
		settype($sql_table, 'string');
		$cfg['sql']['table'] = $sql_table;
		
		$sql_direct = $xml->sql['direct'];
		settype($sql_direct, 'string');
		$cfg['sql']['direct'] = $sql_direct;
		
		$sql_host = $xml->sql['host'];
		settype($sql_host, 'string');
		$cfg['sql']['host'] = $sql_host;
		
		$sql_user = $xml->sql['user'];
		settype($sql_user, 'string');
		$cfg['sql']['user'] = $sql_user;
		
		$sql_password = $xml->sql['password'];
		settype($sql_password, 'string');
		$cfg['sql']['password'] = $sql_password;
		
		$sql_dbname = $xml->sql['dbname'];
		settype($sql_dbname, 'string');
		$cfg['sql']['dbname'] = $sql_dbname;
		
		$special = $xml->special;
		settype($special, 'string');
		$cfg['special'] = $special;
		
		$i = 0;
		foreach($xml->column as $column)
		{
			$name = $column['name'];
			settype($name, 'string');
			$cfg['columns'][$i]['name'] = $name;
			
			$label = $column['label'];
			settype($label, 'string');
			$cfg['columns'][$i]['label'] = $label;
			
			$start = $column['start'];
			settype($start, 'string');
			$cfg['columns'][$i]['start'] = $start;
			
			$length = $column['length'];
			settype($length, 'string');
			$cfg['columns'][$i]['length'] = $length;
			
			$sql = $column['sql'];
			settype($sql, 'string');
			$cfg['columns'][$i]['sql'] = $sql;
			
			$fn = $column['fn'];
			settype($fn, 'string');
			$cfg['columns'][$i]['fn'] = $fn;
			
			$i++;
		}
		
		return $cfg;//retorna a configuração
		
	}//eof loadProject()
	
	public static function saveProject(//transforma um array com configurações de projeto em uma string xml
		$config //(array) array com as configurações do projeto
	)//return string retorna uma string xml
	{
		global $error;//define $error como variável global
		
		if(!$config){$error = 1; return false;}//testa o parâmetro existem
		
		if(!is_array($config)){$error = 2; return false;}//testa se $config é uma array
		
		$project_name = $config['project_name'];
		$source_type = $config['source_type'];
		$description = $config['description'];
		$filter_type = $config['filter']['type'];
		$filter_content = $config['filter']['content'];
		$csv_delimiter = $config['csv']['delimiter'];
		$csv_text = $config['csv']['text'];
		$sql_table = $config['sql']['table'];
		$sql_direct = $config['sql']['direct'];
		$sql_host = $config['sql']['host'];
		$sql_user = $config['sql']['user'];
		$sql_password = $config['sql']['password'];
		$sql_dbname = $config['sql']['dbname'];
		$special = $config['special'];
		$output = $config['output'];
		
		$xml = "<?xml version='1.0' encoding='UTF-8'?>\r\n";
		$xml .= "<orion name='$project_name'>\r\n";
		$xml .= "\t<source_type>$source_type</source_type>\r\n";
		$xml .= "\t<output>$output</output>\r\n";
		$xml .= "\t<description>$description</description>\r\n";
		foreach($config['columns'] as $column)
		{
			$name = $column['name'];
			$label = $column['label'];
			$start = $column['start'];
			$length = $column['length'];
			$sql = $column['sql'];
			$fn = $column['fn'];
			$xml .="\t<column name='$name' label='$label' start='$start' length='$length' sql='$sql' fn='$fn'></column>\r\n";
		}
		$xml .= "\t<filter type='$filter_type'>$filter_content</filter>\r\n";
		$xml .= "\t<csv delimiter='$csv_delimiter'>$csv_text</csv>\r\n";
		$xml .= "\t<sql table='$sql_table' direct='$sql_direct' host='$sql_host' user='$sql_user' password='$sql_password' dbname='$sql_dbname de dados'></sql>\r\n";
		$xml .= "\t<special>$special</special>\r\n";
		$xml .= "</orion>";
		
		return $xml;
	}//eof saveProject()
	
	public static function parseSource(//converte o arquivo $source de acordo com $project
		$project = false //(string) caminho completo para o arquivo xml do projeto
		,$source = false//(string) caminho completo para o arquivo fonte de dados
	)//return boolean retorna true em caso de sucesso e false em caso de falha
	{
		global $error; //define global para a variável de erro
		
		//testa os parâmetros existem
		if(!$project){$error = 1; return false;}
		if(!$source){$error = 1; return false;}
		
		//testa os tipos dos parâmetros
		if(!is_string($project)){$error = 2; return false;}
		if(!is_string($source)){$error = 2; return false;}
		
		//testa se arquivos existem
		if(!file_exists($project)){$error = 3; return false;}
		if(!file_exists($source)){$error = 3; return false;}
		
		$libs = scandir(LIBS);
		foreach($libs as $lib)
		{
			if(is_file(LIBS.$lib))
			{
				require LIBS.$lib;
			}
		}
		
		$cfg = Orion::loadProject($project);//carrega as configurações
		
		if($cfg['source_type'] == 'csv')
		{
			$store = Orion::parseCSV($source, $cfg);
		}
		elseif($cfg['source_type'] == 'fixed')
		{
			$store = Orion::parseFixed($source, $cfg);
		}
		
		if(strlen($cfg['special']) > 0)
		{
			eval($cfg['special']);
		}
		
		switch($cfg['output'])
		{
			case 'csv':
				foreach($cfg['columns'] as $column)
				{
					if(strlen($column['label']) > 0)
					{
						$fields[] = $column['label'];
					}
					else
					{
						$fields[] = $column['name'];
					}
				}
				$store = XtStore::toCSV($store, $fields);
				break;
			case 'sql':
				$sqlf = array();
				foreach($cfg['columns'] as $column)
				{
					if(strlen($column['sql']) == 0)
					{
						$sqlf[] = $column['name'];
					}
					else
					{
						$sqlf[] = $column['sql'];
					}
				}
				
				if($cfg['sql']['direct'] == 'true')
				{
					$conn = XtMysql::connect($cfg['sql']['host'], $cfg['sql']['user'], $cfg['sql']['password'], $cfg['sql']['dbname']);
					if($conn == true)
					{
						$store = XtMysql::insert($store, $cfg['sql']['table'], $sqlf);
					}
					else
					{
						$error = 5;
						return false;
					}
				}
				else
				{
					$store = XtStore::toSQL($store, $cfg['sql']['table'], $sqlf);
				}
				break;
			case 'xml':
				$store = XtStore::toXml($store);
				break;
			case 'json':
				$store = json_encode($store);
				break;
			default:
				$store = XtStore::toXml($store);
				break;
		}
		
		return $store;
		
	}//eof parseSource()
	
	private static function parseCSV(//processa arquivos csv
		$file // (string) arquivo csv
		,$cfg //(array) a configuração
	)
	{//return array retorna o store
		foreach($cfg['columns'] as $column)
		{
			$fields[] = $column['name'];
		}
		
		$store = XtStore::fromCSV($file, $fields, $cfg['csv']['delimiter'], $cfg['csv']['text']);
		
		return $store;
		
	}//eof parseCSV()
	
	private static function parseFixed(//processa arquivos de colunas de largura fixa
		$file // (string) arquivo
		,$cfg //(array) a configuração
	)
	{//return array retorna o store
	
		$regex = false;
		switch($cfg['filter']['type'])
		{
			case 'none':
				$filter = 0;
				break;
			case 'len':
				$filter = $cfg['filter']['content'];
				settype($filter, 'integer');
				break;
			case 'regex':
				$regex = $cfg['filter']['content'];
				$filter = 0;
				break;
			case 'fn':
				$fn = 'function fnDeclare($row){'.$cfg['filter']['content'].'}';
				eval($fn);
				$filter = 'fnDeclare';
				break;
			default:
				$filter = 0;
				break;
		}
		
		foreach($cfg['columns'] as $id => $col)
		{
			foreach($col as $f => $v)
			{
				switch($f)
				{
					case 'name':
						$cm[$id]['title'] = $v;
						break;
					case 'start':
						$cm[$id]['start'] = $v;
						break;
					case 'length':
						$cm[$id]['size'] = $v;
						break;
					case 'fn':
						$cm[$id]['fn'] = $v;
						break;
				}
			}
		}
		
		$store = XtStore::fromFixed($file, $cm, $filter);
		
		if(is_string($regex))
		{
			foreach($store as $i => $row)
			{
				$line = implode('', $row);
				$accept = eregi($regex,$line);
				if($accept !== false)
				{
					$new[$i] = $row;
				}
			}
		$store = $new;
		}
		
		return $store;
		
	}//eof parseFixed()
	
}//eof Orion
?>
