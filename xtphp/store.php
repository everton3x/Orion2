<?php

class XtStore {
    public static function sort(//ordena o store
            $store//(array) o store
            ,$order//(array) ordenação campo=>ordem (asc|desc)
            ){//return false|store
		global $xterror;
        $sorted = $store;
        foreach($order as $field => $ord){
            $sort = '';
            foreach($sorted as $row => $rowdata){
                $sort[$row] = $rowdata[$field];
            }
            $sorted = '';

            $ord = strtolower($ord);
            if($ord == 'asc'){
                $result = asort($sort);
            }elseif($ord == 'desc'){
                $result = arsort($sort);
            }else{
				$xterror = 5;
                return false;
            }

            if($result == false){
				$xterror = 6;
                return false;
            }

            foreach($sort as $row => $value){
                $sorted[$row] = $store[$row];
            }
        }
        return $sorted;

    }//eof sort()

    public static function find(//procura um valor no store
            $store//(array) o store
            ,$search//(string) o valor procurado
            ,$fields = false//(array) os campos a serem procurados
            ){//return false|store
        if(is_array($search)){
            $search = explode(' ', $search);
        }else{
            $search = array($search);
        }

        if($fields == false){
            $fields = XtStore::fields($store);
        }

        foreach($search as $string){
            foreach($store as $index => $row){
                foreach($fields as $field){
                    //$result = stristr($string, $row[$field]);
                    $result = strpos($row[$field], $string);
                    if($result !== false){
                            $finded[$index] = $row;
                    }
                }
            }
        }

        if($finded == false){
            return false;
        }else{
            return $finded;
        }
    }//eof find()

    public static function fields(
            $store//(array) o store
            ){//retorna os campos do store

        foreach($store as $row){
            foreach($row as $field => $value){
                $fields[] = $field;
            }

            return $fields;
        }
    }//eof fields()

    public static function page(//retorna uma página do store
            $store//(array) o store
            ,$page = 1 //(int) número da página
            ,$size = 10 //(int) número de registros por página
            ){//return array

        $total_pages = XtStore::pages($store, $size);

        $start = ($size * ($page -1)) + 1;
        $end = $start - 1 + $size;

        $i = 1;
        foreach($store as $index => $row){
            if($i >= $start AND $i <= $end){
                $pager[$index] = $row;
            }
            $i++;
        }

        return $pager;

    }//eof page()

    public static function pages(
            $store//(array) o store
            ,$size = 10 //(int) número de registros por página
            ){//retorna o número total de páginas do store
        return ceil(count($store) / $size);
    }//eof pages()

    public static function fromMysql(//converte um resultado de consulta Mysql (SELECT) em array e salva no store
            $result//(resource) resultado
            ){//retorn array
        global $xterror;
        if(!is_resource($result)){
            $xterror = 1;
            return false;
        }

        $no_fld = mysql_num_fields($result);
        $store = array();
        $row = 0;
        while($r = mysql_fetch_array($result)){
            for($i = 0; $i < $no_fld; $i++){
                $fld_name = mysql_field_name($result, $i);
                $store[$row][$fld_name] = $r[$fld_name];
            }
            $row++;
        }

        return $store;
    }//eof fromMysql()
    
    public static function addRow(//adiciona uma linha ao store
            $store//(array) o store
            ,$row//(array) linha a ser adicionada
            ,$ref = 'last' //(string|int) referência (first|last|número da linha)
            ){//return array
		global $xterror;
        if($ref == 'first'){
            $position = 0;
        }elseif($ref == 'last'){
            $store[] = $row;
            return $store;
        }elseif(is_int($ref)){
            $position = $ref;
        }else{
			$xterror = 5;
            return false;
        }

        $i = 0;
        foreach($store as $line){
            if($i == $position){
                $new[] = $row;
                $new[] = $line;
            }else{
				$new[] = $line;
			}
            $i++;
        }

        return $new;

    }//eof addrow()
    
    public static function delRow(//exlui uma determinada linha do store
			$store //(array) o store
            ,$index//(int) o número da linha a ser excluída
            ,$reindex = true//(boolean) refaz o índice do store ou não
            ){//return boolean
            
            foreach($store as $key => $row){
                if($key != $index){
                    if($reindex){
                        $new[] = $row;
                    }else{
                        $new[$key] = $row;
                    }
                }
            }

            return $new;
    }//eof delRow()
    
    public static function addCol(//adiciona uma coluna ao store
			$store //(array) o store
            ,$col//(array) contendo a coluna a ser adicionada
            ,$name//(string) nome da coluna
            ,$ref = 'last' //(string|int) referência de psoição da coluna (first|last|inteiro com a nova posição da coluna)
            ){//return array
            global $xterror;
        if(count($col) != count($store)){
            $xterror = 7;
            return false;
        }

        $fields = XtStore::fields($store);

        if($ref == 'first'){
            array_unshift($fields, $name);
            foreach ($store as $index => $row){
                foreach($fields as $field){
                    if($field == $name){
                        $new[$index][$field] = $col[$index];
                    }else{
                        $new[$index][$field] = $row[$field];
                    }
                }
            }

            return $new;
        }elseif($ref == 'last'){
            array_push($fields, $name);
            foreach ($store as $index => $row){
                foreach($fields as $field){
                    if($field == $name){
                        $new[$index][$field] = $col[$index];
                    }else{
                        $new[$index][$field] = $row[$field];
                    }
                }
            }

            return $new;
        }elseif(is_int($ref)){
            foreach($fields as $key => $field){
                if($key == $ref){
                    $order[] = $name;
                    $order[] = $field;
                }else{
                    $order[] = $field;
                }
            }

            array_push($fields, $name);
            foreach ($store as $index => $row){
                foreach($fields as $field){
                    if($field == $name){
                        $new[$index][$field] = $col[$index];
                    }else{
                        $new[$index][$field] = $row[$field];
                    }
                }
            }

            return XtStore::orderCols($new, $order);
        }else{
			$xterror = 5;
            return false;
        }

    }//eof addCol()
    
    public static function delCol(//exclui uma coluna do store
			$store //(array) os tore
            ,$col//(string) nome da coluna a ser excluída
            ){//return array
            global $xterror;
        foreach($store as $index => $row){
            foreach($row as $field => $value){
                if($field != $col){
                    $new[$index][$field] = $value;
                }
            }
        }
        return $new;

    }//eof delCol()
    
    public static function orderCols(//ordena as colunas do store
			$store //(array) o store
            ,$order//(array) nova ordem dos campos
            ){//return array
            global $xterror;
        if(count(XtStore::fields($store)) != count($order)){
            $xterror = 7;
            return false;
        }

        foreach($store as $index => $row){
            foreach($order as $field){
                $new[$index][$field] = $store[$index][$field];
            }
        }

        return $new;

    }//eof orderCols()
    
    public static function applyFn(//aplica uma função em cada célula do store, porém a função recebe a linha inteira podendo fazer operações com ela
            $store //(array) o store
            ,$fn//(string) nome da função sem "()"
            ,$field//(string) nome do campo objeto da função
            ){//return array
            foreach($store as $index => $row){
                foreach($row as $key => $value){
                    if($key == $field){
                        $value = $fn($row);
                    }
                    $new_store[$index][$key] = $value;
                }
            }
            return $new_store;
    }//eof applyFn()
    
    public static function fromCSV(//lê o conteúdo de um arquivo csv e transforma em store
		$file//(string) nome e caminho do arquivo texto
		,$fields //(array) array contendo os nomes de campos
		,$separator = ';'//(string) o separador dos campos
		,$enclosure = '"'//(string) o caractere de delimitação de texto
		,$escape = '/' //(string) caractere de escape
	){//return array
	global $xterror;
		if(!file_exists($file)){
			$xterror = 2;
			return false;
		}
		
		$content = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		
		foreach($content as $i => $row){
			$data = str_getcsv($row, $separator, $enclosure, $escape);
			foreach($fields as $index => $field){
				$store[$i][$field] = $data[$index];
			}
		}
		
		return $store;
	}//eof fromCSV()
	
	public static function toCSV(//converte o store em array CSV
		$store //(array) o store
		,$fields = true //(mixed) true para colocar na primeira linhas o nome dos campos, ou um array com nomes de campos ou false para não colocar cabeçalho
		,$separator = ';' //(string) o separador dos campos
		,$enclosure = '"' //(string) o delimitador de texto
	){//return array
	
		if($fields === true){
			$f = XtStore::fields($store);
			foreach($f as $name => $value){
				$f[$name] = $enclosure.$value.$enclosure;
			}
			$header = implode($separator, $f);
			$output[0] = $header;
			$i = 1;
		}elseif(is_array($fields)){
			foreach($fields as $name => $value){
				$fields[$name] = $enclosure.$value.$enclosure;
			}
			$header = implode($separator, $fields);
			$output[0] = $header;
			$i = 1;
		}else{
			$i = 0;
		}
		
		foreach($store as $row){
			foreach($row as $name => $value){
				$data[$name] = $enclosure.$value.$enclosure;
			}
			$new = implode($separator, $data);
			
			$output[$i] = $new;
			$i++;
		}
		
		return $output;
	}//eof toCSV()
	
	public static function fromFixed(//cria um store a partir de um arquivo texto com campos de largura fixa
		$file //(string) o arquivo com o caminho se o diretório for diferente
		,$cm //(array) o modelo de coluna (title: titulo da coluna; start: caractere de inicio; size: tamanho em caracteres; fn: nume de uma função a ser aplicada
		,$filter = 0 //(mixed) tamanho, em caracteres da linha válida. Se 0, então não testa o tamanho da linha. se for string, é o nome da função para teste
		,$firstRow = 0 //(int) número da linha que inicia a importação. Zero é a primeira linha
	){
		global $xterror;
		if(!file_exists($file)){
			$xterror = 2;
			return false;
		}
		
		$content = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		
		foreach($content as $line => $row){
			if($line >= $firstRow){
				if($filter != 0 && is_int($filter)){
					if(strlen($row) == $filter){
						$store[$line] = XtStore::splitRow($row, $cm);
					}
				}elseif(is_string($filter)){
					if($filter($row)){
						$store[$line] = XtStore::splitRow($row, $cm);
					}
				}else{
					$store[$line] = XtStore::splitRow($row, $cm);
				}
			}
		}
		
		return $store;
		
	}//eof fromFixed
	
	protected static function splitRow(//função auxiliar para fromFixed()
		$row //(string) a linha a ser dividida
		,$cm //(array) o layout de colunas
	){//return array
		
		foreach($cm as $col){
			//$arr[$col['title']] = substr($row, $col['start'], $col['size']);
			$value = substr($row, $col['start'], $col['size']);
			
			$fn = $col['fn'];
			if($fn != false)
			{
				$value = $fn($value);
			}
			$arr[$col['title']] = $value;
		}
		
		return $arr;
	}//eof splitRow()
	
	public static function toSQL(//transforma o store em um array com INSERT SQL
		$store //(array) o store
		,$table //(string) o nome da tabela
		,$fields = true //(mixed) true, utiliza o nome dos campos como nome dos campos ou array com nomes de campos
	){//return array
		
		if(is_array($fields)){
			foreach($fields as $f){
				$header[] = '`'.$f.'`';
			}
			$fields = $header;
		}else{
			$fields = XtStore::fields($store);
			foreach($fields as $f){
				$header[] = '`'.$f.'`';
			}
			$fields = $header;
		}
		
		$fields = implode(', ', $fields);
		
		foreach($store as $i => $row){
			foreach($row as $r){
				$val[] = '\''.$r.'\'';
			}
			
			$values = implode(', ', $val);
			
			$sql[$i] = 'INSERT INTO '.$table.' ('.$fields.') VALUES ('.$values.');';
			$val = '';
		}
		
		return $sql;
		
		
	}//eof toSQL()
	
	public static function trimStore(//aplica trim() em todo o store
		$store
	){//return array
		foreach($store as $index => $row){
			foreach($row as $field => $value){
				$trim[$index][$field] = trim($value);
			}
		}

		return $trim;
	}//eof trimStore()
	
	public static function prepareToJson(//corrige problemas de caracteres especiais para evitar erros em json_encode
		$store //(string) o store
		,$quote_style = ENT_NOQUOTES //(string) ENT_COMPAT | ENT_QUOTES | ENT_NOQUOTES
		,$charset = 'UTF-8' //(string) o charset
	){//return array
		foreach($store as $index => $row){
			foreach($row as $field => $value){
				$return[$index][$field] = htmlentities($value);
			}
		}

		return $return;
	}//eof prepareToJson()
	
	public static function fromODBC(//converte um resultado de consulta ODBC (SELECT) em array e salva no store
            $result//(resource) resultado
            ){//retorn array
        global $xterror;
        if(!is_resource($result)){
            $xterror = 1;
            return false;
        }

        $no_fld = odbc_num_fields($result);
        $store = array();
        $row = 0;
        while($r = odbc_fetch_array($result)){
            for($i = 1; $i <= $no_fld; $i++){
                $fld_name = odbc_field_name($result, $i);
                $store[$row][$fld_name] = $r[$fld_name];
            }
            $row++;
        }

        return $store;
    }//eof fromODBC()
    
    public static function toXml(//converte um store para uma string xml
		$store// (array) o store
		,$encoding = 'UTF-8' //(string) a codificação do arquivo
		,$pagesize = 10 //(int) o número de linhas por página
	){//return string
		$header = '<?xml version="1.0" encoding="'.$encoding.'"?>';
		$xml = "$header\r\n";
		$lines = count($store);
		$pages = XtStore::pages($store, $pagesize);
		$fields = implode(';',XtStore::fields($store));
		$tag_store = '<store lines="'.$lines.'" pagesize="'.$pagesize.'" pages="'.$pages.'" fields="'.$fields.'">';
		$xml .= "$tag_store\r\n";
		
		foreach($store as $i => $row)
		{
			$tag_row = '<row id="'.$i.'">';
			$xml .= "\t$tag_row\r\n";
			
			foreach($row as $name => $value)
			{
				$tag_field = '<field name="'.$name.'">'.$value.'</field>';
				$xml .= "\t\t$tag_field\r\n";
			}
			
			$xml .= "\t</row>\r\n";
		}
		$xml .= "</store>\r\n";
		
		return $xml;
	}//eof toXml()
	
	public static function fromXml(//converte o conteúdo de um arquivo XML em um store
		$file //(string) o arquivo XML
	){//return array
		$xml = simplexml_load_file($file);
		
		$store = array();
		
		$lines = $xml->count();
		
		for($i = 0; $i < $lines; $i++)
		{
			$id = $xml->row[$i]['id'];
			settype($id, 'integer');
			$fields = $xml->row[$i]->count();
			for($j = 0; $j < $fields; $j++)
			{
				$name = $xml->row[$i]->field[$j]['name'];
				$value = $xml->row[$i]->field[$j];
				settype($name, 'string');
				settype($value, 'string');
				$store[$id][$name] = $value;
			}
		}
		
		return $store;
		
	}//eof fromXml()
}

?>
