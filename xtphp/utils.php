<?php

class XtUtils {
	
	public static function printr(//imprime o valor de $mixed com print_r()
		$mixed//string, número, array, object
	){//return null
		echo '<pre>';
		print_r($mixed);
		echo '</pre>';
	}//eof printr()
	
	public static function inputMask(//formata uma string de acordo com uma máscara
		$str//(string) a string a ser formatada
		,$mask//(string) a máscara sendo que o caractere coringa é #
	){
		$l = 0;
		for($i = 0; $i < strlen($mask);$i++){
			if($mask[$i] == '#'){
				$masked .= $str[$l];
				$l++;
			}else{
				$masked .= $mask[$i];
			}
		}
		
		return $masked;
	}//eof inputMask()
	
	public static function number(//converte número formatado em número para PHP/Mysql e vice-versa
		$number //(number|string) o número ou string
	){//return number
		global $xterror;
		if(is_string($number)){
			$number = str_replace('.', '', $number);
			$number = str_replace(',', '.', $number);
			
			return $number;
		}elseif(is_numeric($number)){
			return number_format($number, 2, ',', '.');
		}else{
			$xterror = 1;
			return false;
		}
	}//eof number()
	
	public static function cpfcnpj(//formata um número como cpf/cnpj
		$doc//(int) o número
		,$test = false //(bool) testa se o cpf ou cnpj é válido
	){
		global $xterror;
		settype($doc, 'string');
		if($test){
			if(strlen($doc) == 11){
				if(XtUtils::cpfValid($doc) == false)
				{return false;}
			}elseif(strlen($doc) == 14){
				if(XtUtils::cnpjValid($doc) == false)
				{return false;}
			}else{
				$xterror = 5;
				return false;
			}	
		}
		
		if(strlen($doc) == 11){
			$doc = XtUtils::inputMask($doc, '###.###.###-##');
		}elseif(strlen($doc) == 14){
			$doc = XtUtils::inputMask($doc, '##.###.###/####-##');
		}else{
			$doc = $doc;
		}
		
		return $doc;
	}//eof cpfcnpj()
	
	public static function clearNumber(//retira todos os caracteres exceto números
		$string//(string) a string a ser limpa
	){
		$num = eregi_replace('[^[:digit:]]', '', $string);
		return $num;
	}//eof clearNumber()
	
	public static function cpfValid(//verifica se o cpf é válido
		$cpf //(int) número cpf
	){
		$cpf = str_pad(ereg_replace('[^0-9]', '', $cpf), 11, '0', STR_PAD_LEFT);
	
		if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999'){
			return false;
		}else{
			for ($t = 9; $t < 11; $t++){
				for ($d = 0, $c = 0; $c < $t; $c++){
					$d += $cpf{$c} * (($t + 1) - $c);
				}

				$d = ((10 * $d) % 11) % 10;

				if ($cpf{$c} != $d){
					return false;
				}
			}
			return true;
		}

	}//eof cpfValid()
	
	public static function cnpjValid(//verifica se o cnpj é válido
		$cnpj //(int) número cnpj
	){
		if (strlen ($cnpj) <> 14 or !is_numeric ($cnpj)){
			return false;
		}
		$j = 5;
		$k = 6;
		$soma1 = "";
		$soma2 = "";

		for ($i = 0; $i < 13; $i++){
			$j = $j == 1 ? 9 : $j;
			$k = $k == 1 ? 9 : $k;
			$soma2 += ($cnpj{$i} * $k);

			if ($i < 12){
				$soma1 += ($cnpj{$i} * $j);
			}
			$k--;
			$j--;
		}

		$digito1 = $soma1 % 11 < 2 ? 0 : 11 - $soma1 % 11;
		$digito2 = $soma2 % 11 < 2 ? 0 : 11 - $soma2 % 11;
		return (($cnpj{12} == $digito1) and ($cnpj{13} == $digito2));
	}//eof cnpjValid()
	
	public static function sqlProtect(//pega um string ou array e prepara para usar em consultas SQL
		$str //(string|array) string ou array com valores a proteger
	){//return string|array
		global $xterror;
		if(is_string($str)){
			$str = quoted_printable_encode($str);
			$str = addslashes($str);
		}elseif(is_array($str)){
			foreach($str as $k => $v){
				$v = quoted_printable_encode($v);
				$str[$k] = addslashes($v);
			}
		}else{
			$xterror = 1;
			return false;
		}
		
		return $str;
	}//eof sqlProtect
	
	public static function sqlUnprotect( //desfaz o efeito de sqlProtect()
		$str //(string|array) string ou array a ser desprotegido
	){//return string|array
		global $xterror;
		if(is_string($str)){
			$str = stripslashes($str);
			$str = quoted_printable_decode($str);
		}elseif(is_array($str)){
			foreach($str as $k => $v){
				$v = stripslashes($v);
				$str[$k] = quoted_printable_encode($v);
			}
		}else{
			$xterror = 1;
			return false;
		}
		
		return $str;
	}//eof sqlUnprotect()
}

?>
