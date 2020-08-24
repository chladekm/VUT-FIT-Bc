<?php 

Class LexicalAnalyzer
{

	/** Funkce porovnava zda je token instrukce **/
	private static function isInstruction($word)
	{
  		foreach(TokenType::$instructions as $keyword)
		{
			if(strcasecmp($word, $keyword) == 0)
			{
				GlobalClass::$instruction_counter++;

				$token_key = LexicalAnalyzer::getArrayKey(strtoupper($word), TokenType::$instructions);
				$token = new Token($token_key, $keyword);

				return $token;
			}
		}

		return false;	
	}

	/**
	 * Funkce zjistí klíč položky v poli podle hodnoty
	 * @param $value hodnota, která ma nějaký klíč
	 * @param $array pole ve kterém se má vyhledávat
	 * @return hledaný klíč
	 */ 
	private static function getArrayKey($value, $array)
	{
		$key = array_search($value, $array);

		if($key == false)
		{
			throw new HandlingErrors("Cannot find the key of array element", 99);
		}
		else
		{
			return $key;
		}
	}

	/**
	 * Funkce transformuje neplatne XML znaky na adekvatni format
	 * @param $input vstupní řetězec
	 * @return $string_buffer řetězec v adekvátním formátu pro XML
	 */
	public static function transformXMLchars($input)
	{
		$string_buffer = '';
		
		$string_length = strlen($input);

		for( $i = 0; $i < $string_length; $i++ )
		{
    		$char = substr( $input, $i, 1 );

    		switch ($char)
    		{
    			case '<':
    				$string_buffer .= "&lt;";
    				break;
    			case '>':
    				$string_buffer .= "&gt;";
    				break;
    			case '"':
    				$string_buffer .= "&quot;";
    				break;
    			case '\'':
    				$string_buffer .= "&apos;";
    				break;
    			case '&':
    				$string_buffer .= "&amp;";
    				break;
    			default:
    				$string_buffer .= $char;
    				break;
    		}

    	}
    	
    	return $string_buffer;
	}

	/**
	 * Funkce prvadi lexikalni analyzu
	 * @param $row jeden řádek programu
	 * @return pole tokenů, které odpovídá jednomu řádku programu
	 */
	public static function getTokens($row)
	{		
		$token_array = array();

		$pos_of_ins = -1;

		$comment_in_string = false;

		foreach($row as $i => $row_token)
		{
			/***** PREG_MATCH nápověda *****
			 **
			 ** / ... / značí rozdělovače
			 ** ^ 		značí začátek stringu
			 ** $ 		značí konec stringu
			 ** {5}		přesný počet znaků
			 ** +		1...n kvantifikátorů
			 ** *  		0...n kvantifikátorů
			 ** ?		0...1 kvantifikátorů
			 ** (...)	značí podvzor

			 ** více viz: http://php.net/manual/en/regexp.reference.meta.php
			 **/

			/** Ověření, jestli string neobsahuje #(komentář) bez mezery **/

			for ($j = 0; $j < strlen($row_token); $j++)
			{
    			$char = $row_token[$j];

    			if($char === '#' && $j == 0)
    			{
    				break;
    			}
    			else if($char === '#')
    			{
    				$comment_in_string = true;
    				$row_token = substr($row_token, 0, $j);

    				break;
    			}
			}

			/** Mezera **/
		    if(!($row_token))
		    {
		    	$token = false;
		    }
		    /** Instrukce **/
		    elseif ((($token = LexicalAnalyzer::isInstruction($row_token)) != false) && ($pos_of_ins == -1))
		    {
		    	$pos_of_ins = $i;
		    	
		    	$row[$i] = strtoupper($row_token);
		    }

		    /** Komentář **/
		    elseif($row_token[0] == '#')
		    {
		    	break;
		    }

		    /** Není to ani mezera, ani instrukce, ani komentář a zároveň nebyla ještě načtena žádná instrukce **/
		    elseif($pos_of_ins == -1)
		    {
		    	throw new HandlingErrors("Unknown operation code", 22);
		    }

		    /** int@cislo **/
		    elseif (preg_match('/^int@[+-]?[0-9]+$/', $row_token) == 1)
		    {
		    	$value = substr($row_token, 4);
		    	$token_key = LexicalAnalyzer::getArrayKey("int", TokenType::$values);
                $token = new Token($token_key, $value);	
		    }

		    /** bool@true **/
		    elseif(preg_match('/^bool@(true|false)$/', $row_token) == 1)
		    {
		    	$value = substr($row_token, 5);
		    	$token_key = LexicalAnalyzer::getArrayKey("bool", TokenType::$values);
                $token = new Token($token_key, $value);

            }

            /** nil@nil **/
		    elseif(preg_match('/^nil@nil$/', $row_token) == 1)
		    {
		    	$value = substr($row_token, 4);
                $token_key = LexicalAnalyzer::getArrayKey("nil", TokenType::$values);
                $token = new Token($token_key, $value);
            }

            /** string@ **/								/** Escape sekvence **/
            elseif(preg_match('/^string@([a-z]|[A-Z]|[0-9]|\\\\[0-9]{3}|[\_\-\$\#\&\%\*\!\?\>\<\"\'\@\/])*$/', $row_token) == 1)
            {
		    	$value = substr($row_token, 7);
		    	$value = LexicalAnalyzer::transformXMLchars($value);
                $token_key = LexicalAnalyzer::getArrayKey("string", TokenType::$values);
                $token = new Token($token_key, $value);
            }

            /** GF|LF|TF @variable **/
            elseif(preg_match('/^(GF|LF|TF)@([a-z]|[A-Z]|[\_\-\$\&\%\*\!\?\>\<\"\'\/])([a-z]|[A-Z]|[0-9]|[\_\-\$\&\%\*\!\?\>\<\"\'\/])*$/', $row_token) == 1)
            {
                $token_key = LexicalAnalyzer::getArrayKey("var", TokenType::$values);
                $row_token = LexicalAnalyzer::transformXMLchars($row_token);
                $token = new Token($token_key, $row_token);

            }
            /** Label za instrukcí, nebo typ za read **/
            elseif(preg_match('/^([a-z]|[A-Z]|[\_\-\$\&\%\*\!\?\>\<\"\'\/])([a-z]|[A-Z]|[0-9]|[\_\-\$\&\%\*\!\?])*$/', $row_token) == 1)
            {
            	if($row[$i-1] == "LABEL" || ($row[$i-1] == "JUMP") || ($row[$i-1] == "JUMPIFEQ" ) || ($row[$i-1] == "JUMPIFNEQ") || ($row[$i-1] == "CALL"))
            	{
                    $token_key = LexicalAnalyzer::getArrayKey("label_desc", TokenType::$others);
                	$token = new Token($token_key, $row_token);
            	}
            	elseif($row_token == "int" || $row_token == "string" || $row_token == "bool")
            	{
            		$token_key = LexicalAnalyzer::getArrayKey("type", TokenType::$others);
            		$token = new Token($token_key, $row_token);
            	}
            	else
            	{
        			throw new HandlingErrors("Lexical error", 23);
            	}
			}
			/** Cokoliv jiného**/
            else
            {
            	throw new HandlingErrors("Lexical error", 23);
            }

            /** Pokud se nejedna o prazdny token (mezery), vloz do pole **/
			if($token)
			{
				array_push($token_array, $token);
			}

			if($comment_in_string)
			{
				break;
			}
		}

        return $token_array;      

	}
}

?>