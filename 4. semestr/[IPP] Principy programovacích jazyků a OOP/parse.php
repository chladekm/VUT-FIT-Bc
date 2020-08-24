<?php 

require 'scanner.php';
require 'parser.php';
require 'token.php';

/*********************** Main ***********************/

try
{
    /** Načtení a kontrola argumentů **/
	if(($argc === 2) && ($argv[1] === '--help'))
	{
		GlobalClass::displayHelp();
		exit(0);
	}
	else if($argc > 1)
	{
		throw new HandlingErrors("Wrong number of arguments", 10);
	}

	$inputContent = GlobalClass::readInput();

	GlobalClass::$xml_to_stdin = fopen('php://stdin', 'w');
	GlobalClass::initOutputXML();

	/** Vystrizeni hlavicky ze stringu a jeji kontrola **/
	SyntaxAnalyzer::checkHeader($inputContent);

    /** Resi problem kdy v nasledujici operaci byl spatne rozparsovan string kvuli chybejici EOL **/
    if(!strstr($string, PHP_EOL))
    {
        $inputContent .= "\n";
    }

	/** Program bez hlavičky **/
    $inputContent = substr($inputContent, strpos($inputContent, "\n")+1 );

	SyntaxAnalyzer::checkSyntax($inputContent);

	/** Naformatuje vystupni XML **/
	GlobalClass::$xml->formatOutput = true;

	/** Vypise XML na stdout **/
	echo GlobalClass::$xml->saveXML();

	// GlobalClass::$xml->save('output.xml');
	
	fclose(GlobalClass::$xml_to_stdin);
}
/**************** Calling error message ***************/
catch(HandlingErrors $exception)
{
	$exception->endProgram();
}

/** Trida pro vypis erroru **/
class HandlingErrors extends Exception
{
	public $when;
	public $exit_code;

	public function __construct($when, $exit_code)
	{
		$this->when = $when;
		$this->exit_code = $exit_code;		
	}

	public function endProgram()
	{
		fwrite(STDERR, "Error occured: " . $this->when . ". Program ended with exit code: " . $this->exit_code . ".\n");

		exit($this->exit_code);
	}
}

/** Trida obsahujici obecne funkce **/
class GlobalClass
{
	public static $instruction_counter = 0;
	public static $xml;
	public static $xml_program;

	public static $xml_to_stdin;

	/** Funkce vypise napovedu na stdout **/
	public static function displayHelp()
	{
		print("Skript typu filtr (parse.php v jazyce PHP 7.3)\n"); 
		print("načte ze standardního vstupu zdrojový kód v IPPcode19,\n");
		print("zkontroluje lexikální a syntaktickou správnost kódu\n");
		print("a vypíše na standardní výstup XML reprezentaci programu.\n");
	}
	
	/** Načtení ze standartního vstupu **/
	public static function readInput()
	{
		$inputContent = file_get_contents('php://stdin');
		
		return $inputContent;
	}

	/** Inicializace XML **/
	public static function initOutputXML()
	{
        GlobalClass::$xml = new DomDocument("1.0", "UTF-8");

        GlobalClass::$xml_program = GlobalClass::$xml->createElement('program');
        GlobalClass::$xml_program->setAttribute('language', 'IPPcode19');

        GlobalClass::$xml->appendChild(GlobalClass::$xml_program);

    }

    /**
     * Funkce přidává instrukce do XML
     * @param funkce pracuje s dynamickým počtem argumentů
     */
    public static function writeToXML()
    {
    	if(!(is_object(GlobalClass::$xml))) 
    	{
    		throw new HandlingErrors("Internal XML error", 99);
    	}

    	$token = func_get_arg(0);

    	$xml_instruction = GlobalClass::$xml->createElement('instruction');

        $xml_instruction->setAttribute('order', GlobalClass::$instruction_counter);
        $xml_instruction->setAttribute('opcode', $token->token_atribute);

        for($i = 1; $i < func_num_args(); $i++)
        {
        	$token = func_get_arg($i);

        	switch ($token->token_type)
        	{
        		case "T_SYMB_INT":
        			$xml_argument = GlobalClass::$xml->createElement("arg$i", $token->token_atribute);
                    $xml_argument->setAttribute('type', 'int');
        			break;
        		case "T_SYMB_BOOL":
        			$xml_argument = GlobalClass::$xml->createElement("arg$i", $token->token_atribute);
                    $xml_argument->setAttribute('type', 'bool');
        			break;
        		case "T_SYMB_STRING":
        			$xml_argument = GlobalClass::$xml->createElement("arg$i", $token->token_atribute);
                    $xml_argument->setAttribute('type', 'string');
        			break;
        		case "T_SYMB_NIL":
        			$xml_argument = GlobalClass::$xml->createElement("arg$i", $token->token_atribute);
                    $xml_argument->setAttribute('type', 'nil');
        			break;
        		case "T_VAR":
        			$xml_argument = GlobalClass::$xml->createElement("arg$i", $token->token_atribute);
                    $xml_argument->setAttribute('type', 'var');
        			break;
        		case "T_LABEL":
        			$xml_argument = GlobalClass::$xml->createElement("arg$i", $token->token_atribute);
                    $xml_argument->setAttribute('type', 'label');
        			break;
        		case "T_TYPE":
        			$xml_argument = GlobalClass::$xml->createElement("arg$i", $token->token_atribute);
                    $xml_argument->setAttribute('type', 'type');
        			break;
        		default:
        			throw new HandlingErrors("Internal XML error", 99);
        			break;
        	}

        	$xml_instruction->appendChild($xml_argument);
        }

        GlobalClass::$xml_program->appendChild($xml_instruction);
    }
}

?>