<?php

class SyntaxAnalyzer
{

	/** Funkce kontroluje zda vstup obsahuje hlavičku .IPPcode19 **/
	public static function checkHeader($inputString)
	{
		$header =  strtok($inputString, "\n");
		
		/** Odebrani duplicitnich mezer **/
		$header = preg_replace('/\s+/', ' ', $header);
					
		$header_part = '';

		for ($i = 0; $i < strlen($header); $i++)
		{
			$char = strtoupper($header[$i]);

			if($char != ' ')
			{
				if($header_part == ".IPPCODE19" && $char == '#')
				{
					break;
				}

				$header_part .= $char;
			}
		}

		if($header_part != ".IPPCODE19")
		{
			throw new HandlingErrors("Missing file header", 21);
			return 21;
		}
		
		return;
	}

	/**
	 * Funkce kontroluje typ tokenu
	 * @param $keyword typ jaky je vyzadovan
	 * @param $token_type typ skutecneho tokenu
	 */
	private static function checkToken($keyword, $token_type)
	{

		if($token_type == 'T_LABEL' || $token_type == 'T_TYPE')
		{
			$token_type = TokenType::$others[$token_type];
		}
		else
		{
			$token_type = TokenType::$values[$token_type];
		}

		$keyword = trim(strtolower($keyword));
		$token_type = trim(strtolower($token_type));



		/** Symb může být buď konstanta nebo proměnná **/
		if(($keyword === "symb") && (in_array($token_type, TokenType::$values))) 
		{
			return true;
		}

		if($keyword === $token_type)
		{	
			return true;
		}
		else
		{
			throw new HandlingErrors("Incorrect code", 22);
			return false;
		}	
	}

	/**
	 * Funkce kontroluje počet operandů pro instrukci
	 * @param $number_of_args načtený celý program jako string
	 * @param $row daný řádek
	 * @return bool|true na základě toho, jestli odpovídá počet argumentů
	 */
	private static function checkOperands($number_of_args, $row)
	{

		if(count($row) != $number_of_args)
		{
			throw new HandlingErrors("Syntax error", 23);
			return false; 
		}
		else
		{
			return true;
		}
	}

	/**
	 * Funkce kontroluje syntaxi
	 * @param $rows_buffer vstup ze stdin
	 */
	public static function checkSyntax($rows_buffer)
	{
		/** Rozebrání vstupu na jednotlivé řádky **/
		$rows_buffer = explode("\n", $rows_buffer);
		

		foreach ($rows_buffer as $i => $row)
		{
			/** Odebrani duplicitnich mezer **/
			$rows_buffer = preg_replace('/\s+/', ' ', $rows_buffer);
			
			/** Rozebrani radku na jednotlive elementy **/
			$row = preg_split('/\s+/', $rows_buffer[$i]);

			/* Volani lexikalni analyzy */
			$token_array = LexicalAnalyzer::getTokens($row);
			
			$token = $token_array[0];

			switch (strtoupper($token->token_atribute))
			{      
	            case "MOVE":
	                SyntaxAnalyzer::m_MOVE($token_array);
	                break;
	            case "CREATEFRAME":
	                SyntaxAnalyzer::m_CREATEFRAME($token_array);
	                break;
	            case "PUSHFRAME":
	                SyntaxAnalyzer::m_PUSHFRAME($token_array);
	                break;
	            case "POPFRAME":
	                SyntaxAnalyzer::m_POPFRAME($token_array);
	                break;
	            case "DEFVAR":
	                SyntaxAnalyzer::m_DEFVAR($token_array);
	                break;
	            case "CALL":
	                SyntaxAnalyzer::m_CALL($token_array);
	                break;
	            case "RETURN":
	                SyntaxAnalyzer::m_RETURN($token_array);
	                break;
	            case "PUSHS":
	                SyntaxAnalyzer::m_PUSHS($token_array);
	                break;
	            case "POPS":
	                SyntaxAnalyzer::m_POPS($token_array);
	                break;
	            case "ADD":
	                SyntaxAnalyzer::m_ADD($token_array);
	                break;
	            case "SUB":
	                SyntaxAnalyzer::m_SUB($token_array);
	                break;
	            case "MUL":
	                SyntaxAnalyzer::m_MUL($token_array);
	                break;
	            case "IDIV":
	                SyntaxAnalyzer::m_IDIV($token_array);
	                break;
	            case "LT":
	                SyntaxAnalyzer::m_LT($token_array);
	                break;
	            case "GT":
	                SyntaxAnalyzer::m_GT($token_array);
	                break;
	            case "EQ":
	                SyntaxAnalyzer::m_EQ($token_array);
	                break;
	            case "AND":
	                SyntaxAnalyzer::m_AND($token_array);
	                break;
	            case "OR":
	                SyntaxAnalyzer::m_OR($token_array);
	                break;
	            case "NOT":
	                SyntaxAnalyzer::m_NOT($token_array);
	                break;
	            case "INT2CHAR":
	                SyntaxAnalyzer::m_INT2CHAR($token_array);
	                break;
	            case "STRI2INT":
	                SyntaxAnalyzer::m_STRI2INT($token_array);
	                break;
	            case "READ":
	                SyntaxAnalyzer::m_READ($token_array);
	                break;
	            case "WRITE":
	                SyntaxAnalyzer::m_WRITE($token_array);
	                break;
	            case "CONCAT":
	                SyntaxAnalyzer::m_CONCAT($token_array);
	                break;
	            case "STRLEN":
	                SyntaxAnalyzer::m_STRLEN($token_array);
	                break;
	            case "GETCHAR":
	                SyntaxAnalyzer::m_GETCHAR($token_array);
	                break;
	            case "SETCHAR":
	                SyntaxAnalyzer::m_SETCHAR($token_array);
	                break;
	            case "TYPE":
	                SyntaxAnalyzer::m_TYPE($token_array);
	                break;
	            case "LABEL":
	                SyntaxAnalyzer::m_LABEL($token_array);
	                break;
	            case "JUMP":
	                SyntaxAnalyzer::m_JUMP($token_array);
	                break;
	            case "JUMPIFEQ":
	                SyntaxAnalyzer::m_JUMPIFEQ($token_array);
	                break;
	            case "JUMPIFNEQ":
	                SyntaxAnalyzer::m_JUMPIFNEQ($token_array);
	                break;
	            case "EXIT":
	            	SyntaxAnalyzer::m_EXIT($token_array);
	                break;
	            case "DPRINT":
	                SyntaxAnalyzer::m_DPRINT($token_array);
	                break;
	            case "BREAK":
	                SyntaxAnalyzer::m_BREAK($token_array);
	                break;
	            default:
	            	break;
        	}

        }
	}

 // 01	MOVE <var> <symb> 
	private static function m_MOVE($token_array)
	{
		SyntaxAnalyzer::checkOperands(3, $token_array);
		
		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2);
	}

 // 02	CREATEFRAME 
	private static function m_CREATEFRAME($token_array)
	{
		SyntaxAnalyzer::checkOperands(1, $token_array);

		GlobalClass::writeToXML($token_array[0]);
	}

 // 03	PUSHFRAME 
	private static function m_PUSHFRAME($token_array)
	{
		SyntaxAnalyzer::checkOperands(1, $token_array);

		GlobalClass::writeToXML($token_array[0]);
	}

 // 04	POPFRAME 
	private static function m_POPFRAME($token_array)
	{
		SyntaxAnalyzer::checkOperands(1, $token_array);

		GlobalClass::writeToXML($token_array[0]);
	}

 // 05	DEFVAR <var> 
	private static function m_DEFVAR($token_array)
	{
		SyntaxAnalyzer::checkOperands(2, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1);
	}

 // 06	CALL <label> 
	private static function m_CALL($token_array)
	{
		SyntaxAnalyzer::checkOperands(2, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("label_desc", $arg1->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1);
	}

 // 07	RETURN 
	private static function m_RETURN($token_array)
	{
		SyntaxAnalyzer::checkOperands(1, $token_array);

		GlobalClass::writeToXML($token_array[0]);
	}

 // 08	PUSHS <symb> 
	private static function m_PUSHS($token_array)
	{
		SyntaxAnalyzer::checkOperands(2, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("symb", $arg1->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1);
	}

 // 09	POPS <var> 
	private static function m_POPS($token_array)
	{
		SyntaxAnalyzer::checkOperands(2, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1);
	}

 // 10	ADD <var> <symb1> <symb2> 
	private static function m_ADD($token_array)
	{
		SyntaxAnalyzer::checkOperands(4, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		$arg3 = $token_array[3];
		SyntaxAnalyzer::checkToken("symb", $arg3->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2, $arg3);
	}

 // 11	SUB <var> <symb1> <symb2> 
	private static function m_SUB($token_array)
	{
		SyntaxAnalyzer::checkOperands(4, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		$arg3 = $token_array[3];
		SyntaxAnalyzer::checkToken("symb", $arg3->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2, $arg3);
	}

 // 12	MUL <var> <symb1> <symb2> 
	private static function m_MUL($token_array)
	{
		SyntaxAnalyzer::checkOperands(4, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		$arg3 = $token_array[3];
		SyntaxAnalyzer::checkToken("symb", $arg3->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2, $arg3);
	}

 // 13	IDIV <var> <symb1> <symb2> 
	private static function m_IDIV($token_array)
	{
		SyntaxAnalyzer::checkOperands(4, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		$arg3 = $token_array[3];
		SyntaxAnalyzer::checkToken("symb", $arg3->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2, $arg3);
	}

 // 14	LT <var> <symb1> <symb2> 
	private static function m_LT($token_array)
	{
		SyntaxAnalyzer::checkOperands(4, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		$arg3 = $token_array[3];
		SyntaxAnalyzer::checkToken("symb", $arg3->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2, $arg3);
	}

 // 15	GT <var> <symb1> <symb2> 
	private static function m_GT($token_array)
	{
		SyntaxAnalyzer::checkOperands(4, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		$arg3 = $token_array[3];
		SyntaxAnalyzer::checkToken("symb", $arg3->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2, $arg3);
	}

 // 16	EQ <var> <symb1> <symb2> 
	private static function m_EQ($token_array)
	{
		SyntaxAnalyzer::checkOperands(4, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		$arg3 = $token_array[3];
		SyntaxAnalyzer::checkToken("symb", $arg3->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2, $arg3);
	}

 // 17	AND <var> <symb1> <symb2> 
	private static function m_AND($token_array)
	{

		SyntaxAnalyzer::checkOperands(4, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		$arg3 = $token_array[3];
		SyntaxAnalyzer::checkToken("symb", $arg3->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2, $arg3);
	}

 // 18	OR <var> <symb1> <symb2> 
	private static function m_OR($token_array)
	{
		SyntaxAnalyzer::checkOperands(4, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		$arg3 = $token_array[3];
		SyntaxAnalyzer::checkToken("symb", $arg3->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2, $arg3);
	}

 // 19	NOT <var> <symb1>
	private static function m_NOT($token_array)
	{
		SyntaxAnalyzer::checkOperands(3, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2);
	}

 // 20	INT2CHAR <var> <symb> 
	private static function m_INT2CHAR($token_array)
	{
		SyntaxAnalyzer::checkOperands(3, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2);
	}

 // 21	STRI2INT <var> <symb1> <symb2> 
	private static function m_STRI2INT($token_array)
	{
		SyntaxAnalyzer::checkOperands(4, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		$arg3 = $token_array[3];
		SyntaxAnalyzer::checkToken("symb", $arg3->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2, $arg3);
	}

 // 22	READ <var> <type> 
	private static function m_READ($token_array)
	{
		SyntaxAnalyzer::checkOperands(3, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);

		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("type", $arg2->token_type);		

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2);
	}

 // 23	WRITE <symb> 
	private static function m_WRITE($token_array)
	{
		SyntaxAnalyzer::checkOperands(2, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("symb", $arg1->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1);
	}

 // 24	CONCAT <var> <symb1> <symb2> 
	private static function m_CONCAT($token_array)
	{
		SyntaxAnalyzer::checkOperands(4, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		$arg3 = $token_array[3];
		SyntaxAnalyzer::checkToken("symb", $arg3->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2, $arg3);
	}

 // 25	STRLEN <var> <symb> 
	private static function m_STRLEN($token_array)
	{
		SyntaxAnalyzer::checkOperands(3, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2);
	}

 // 26	GETCHAR <var> <symb1> <symb2> 
	private static function m_GETCHAR($token_array)
	{
		SyntaxAnalyzer::checkOperands(4, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		$arg3 = $token_array[3];
		SyntaxAnalyzer::checkToken("symb", $arg3->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2, $arg3);
	}

 // 27	SETCHAR <var> <symb1> <symb2> 
	private static function m_SETCHAR($token_array)
	{
		SyntaxAnalyzer::checkOperands(4, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		$arg3 = $token_array[3];
		SyntaxAnalyzer::checkToken("symb", $arg3->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2, $arg3);
	}

 // 28	TYPE <var> <symb> 
	private static function m_TYPE($token_array)
	{
		SyntaxAnalyzer::checkOperands(3, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("var", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2);
	}

 // 29	LABEL <label> 
	private static function m_LABEL($token_array)
	{
		SyntaxAnalyzer::checkOperands(2, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("label_desc", $arg1->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1);
	}

 // 30	JUMP <label> 
	private static function m_JUMP($token_array)
	{
		SyntaxAnalyzer::checkOperands(2, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("label_desc", $arg1->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1);
	}

 // 31	JUMPIFEQ <label> <symb1> <symb2> 
	private static function m_JUMPIFEQ($token_array)
	{
		SyntaxAnalyzer::checkOperands(4, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("label_desc", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		$arg3 = $token_array[3];
		SyntaxAnalyzer::checkToken("symb", $arg3->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2, $arg3);
	}

 // 32	JUMPIFNEQ <label> <symb1> <symb2> 
	private static function m_JUMPIFNEQ($token_array)
	{
		SyntaxAnalyzer::checkOperands(4, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("label_desc", $arg1->token_type);
		
		$arg2 = $token_array[2];
		SyntaxAnalyzer::checkToken("symb", $arg2->token_type);

		$arg3 = $token_array[3];
		SyntaxAnalyzer::checkToken("symb", $arg3->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1, $arg2, $arg3);
	}

 // 33	EXIT <symb> 
	private static function m_EXIT($token_array)
	{
		SyntaxAnalyzer::checkOperands(2, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("symb", $arg1->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1);
	}

 // 34	DPRINT <symb> 
	private static function m_DPRINT($token_array)
	{
		SyntaxAnalyzer::checkOperands(2, $token_array);

		$arg1 = $token_array[1];
		SyntaxAnalyzer::checkToken("symb", $arg1->token_type);

		GlobalClass::writeToXML($token_array[0], $arg1);
	}

 // 35	BREAK 
	private static function m_BREAK($token_array)
	{
		SyntaxAnalyzer::checkOperands(1, $token_array);

		GlobalClass::writeToXML($token_array[0]);
	}

}

?>