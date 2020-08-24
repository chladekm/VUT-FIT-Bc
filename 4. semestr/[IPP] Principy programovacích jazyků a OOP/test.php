<?php

try
{
	Arguments::ParseArguments($argv);

	if(Arguments::$help)
		GlobalClass::displayHelp();

	Tests::prepareTest(Arguments::$directory);

	HTML::WriteHTML();

	GlobalClass::cleanTemporaryFiles();
}
catch (Exception $exception)
{
	$exception->endProgram();	
}

   
class HTML
{
	static public function WriteHTML()
	{
		echo "<!DOCTYPE html>
			<html lang=\"en\">
			<head>
				<meta charset=\"utf-8\">
				<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
				<title>IPP - Tests summary</title>
			</head>";

		HTML::WriteCSS();

		echo "<body>
				<div class=\"body_container\">
					<div class=\"header_div shade\">
					<h1>Tests summary</h1>
					<h3>IPP 2018/2019</h3>
					<span>Martin Chládek</span>
				</div>";

		HTML::WriteTestResults();
	}

	static public function WriteCSS()
	{
		echo"<style>
			body{background-color: #DFDFDF;}

		h1,h2,h3,h4,p,a,span{font-family: Verdana, Geneva, sans-serif;}

		a:link{text-decoration: none;}
		a{text-decoration: none;}

		a:visited 
		{
		    text-decoration: none; 
		    decoration: none;
		}

		p
		{
			margin-top: -25px;
			margin-left: 10px;
		}
		
		.header_div{
			background-color: #EEE; 
			padding: 15px; 
			margin: auto; 
			margin-top: 40px; 
			border-radius: 15px; 
			text-align: center;
		}

		.shade{
			-webkit-box-shadow: 6px 6px 22px 0px rgba(0,0,0,0.4);
			-moz-box-shadow: 6px 6px 22px 0px rgba(0,0,0,0.4);
			box-shadow: 6px 6px 22px 0px rgba(0,0,0,0.4);
			border-radius: 15px; 
		}

		.body_container{
			width: 80%;
			margin: auto;
			display: block;
		}

		.container{
			display: inline-block;
			width: 100%;
			margin: auto;
			margin-top: 30px;
		}


		.left, .middle, .right{
			width: 29%;
			color: white;
			padding: 15px;
			text-align: center;
		}

		.left{
			float: left;
			background-color: #565656;		
		}

		.middle{
			margin: 0 auto;
			background-color: #529600;
		}

		.right{
			float: right;
			background-color: #8c0400;
		}

		.test_header_fail{border-bottom: 3px solid #8c0400;}
		.test_header_succ{border-bottom: 3px solid #529600;}

		.test_container
		{
			display: block;
			background-color: #EEE;
			padding: 15px;
			margin-top: 30px;
		}

		.separator{border-bottom: 1px solid #BBB;}

		#failed_header:hover
		{
			background-color: #590300;
		}

		#success_header:hover
		{
			background-color: #366300;
		}

		</style>";
	}

	static public function WriteTestResults()
	{
		echo "<div class=\"container\">
				<div class=\"left shade\">
					<h3>Tests runned: " . GlobalClass::$testCounter . "</h3>
				</div>
				<a href=\"#test_failed\">
				<div id=\"failed_header\" class=\"right shade\">
					<h3>Tests failed: " . GlobalClass::$testFailed . "</h3>
				</div>
				</a>
				<a href=\"#test_passed\">
				<div id=\"success_header\" class=\"middle shade\">
					<h3>Successful tests: " . GlobalClass::$testPassed ."</h3>
				</div>
				</a>
			</div>";

		if(GlobalClass::$testFailed != 0)
		{
			echo "<div id=\"test_failed\" class=\"shade test_container\">";
			echo "<h2 class=\"test_header_fail\">Failed:</h2>";
				
			foreach (Tests::$FailTestsArray as $test) {
				
				echo "<div class=\"separator\">
						<h4>Test #" . $test->counter ."</h4><br>
						<p><strong>file:</strong> " . $test->obj_filename . "<br>" . "<strong>path:</strong> " . $test->obj_directory . "<br>" .
						"<strong>reason:</strong> " . $test->obj_reason ."</p>	
					</div>";
			}

			echo "</div>";
		}


		if(GlobalClass::$testPassed != 0)
		{
			echo "<div id=\"test_passed\" class=\"shade test_container\">";
			echo "<h2 class=\"test_header_succ\">Successful:</h2>";
				
			foreach (Tests::$SuccTestsArray as $test) {
				
				echo "<div class=\"separator\">
						<h4>Test #" . $test->counter ."</h4><br>
						<p><strong>file:</strong> " . $test->obj_filename . "<br>" . "<strong>path:</strong> " . $test->obj_directory . "</p>
					</div>";
			}

			echo "</div>";
		}

		echo "</div>
			</body>
		</html>";
	}
}

class Tests
{

	public $obj_directory;
	public $obj_filename;
	public $obj_reason;
	public static $counter = 1;

	public static $SuccTestsArray = array();
	public static $FailTestsArray = array();

	// Konstruktor pro kazdy test
	public function __construct($directory, $filename, $reason = 0)
	{
		$this->obj_directory = $directory;
		$this->obj_filename = $filename;
		$this->obj_reason = $reason;
		$this->counter = Tests::$counter++;
	}

	// Funkce najde vsechny testovaci soubory a zavola metodu pro jeden konkretni soubor
	public static function prepareTest($directory)
	{
		// Pokud je zadaná cesta s posledním znakem '/' tak ho odebereme 
		if(substr($directory, -1) === '/')
			$directory = rtrim($directory,'/');

		if(!($actual_dir = opendir($directory)))
			throw new HandlingErrors("Directory does not exists", 11);
			

		while(($file = readdir($actual_dir)) !== FALSE)
		{
			if($file === '.' || $file === '..')
			{
				// Nežádoucí soubory -> SKIP 
			}
			else if(is_dir($directory . "/" . $file))
			{
				// Nalezen adresář -> zajímá mě jen pokud byl zadán parametr recursive 
				if(Arguments::$recursive)
				{
					Tests::prepareTest($directory . "/" . $file);
				}
			}
			else 
			{
				$file_extension = pathinfo($file, PATHINFO_EXTENSION);
				$file = pathinfo($file, PATHINFO_FILENAME);

				if($file_extension === 'src')
				{
					Tests::doOneTest($directory, $file);
				}
			}
		}
	}


	// Funkce najde prislusne .in .out .rc soubory, nebo je vytvori a zavola analyzator/interpret
	public static function doOneTest($directory, $src_filename)
	{
		$in = FALSE;
		$out = FALSE;
		$rc = FALSE;

		if(!(is_dir($directory)))
			throw new HandlingErrors("Directory does not exists", 11);

		if($actual_dir = opendir($directory))
		{
			while(($file = readdir($actual_dir)) !== FALSE)
			{
				if($file === '.' || $file === '..')
				{
					// Nežádoucí soubory (složky) -> SKIP
				}
				else if(is_dir($directory . "/" . $file))
				{
					// Existuje složka se stejným jménem jako soubor -> SKIP
				}
				else
				{
					$file_extension = pathinfo($file, PATHINFO_EXTENSION);
					$file = pathinfo($file, PATHINFO_FILENAME);

					if($src_filename === $file && $file_extension === 'rc')
						$rc = TRUE;
					
					else if($src_filename === $file && $file_extension === 'in')
						$in = TRUE;

					else if($src_filename === $file && $file_extension === 'out')
						$out = TRUE;
				}
			}
		}
		else
		{
			throw new Hand("Cannot reach the folder", 11);
		}
		
		$dir_file_path = $directory . "/" . $src_filename;

		if($rc === FALSE)
		{
			shell_exec('touch ' . $dir_file_path . '.rc');
			shell_exec('echo "0" >> ' . $dir_file_path . '.rc');
		}
		if ($in === FALSE)
		{
			shell_exec('touch ' . $dir_file_path . '.in');
		}
		if ($out === FALSE)
		{
			shell_exec('touch ' . $dir_file_path . '.out');
		}

		$test = new Tests($directory,$src_filename);  

		GlobalClass::$testCounter++;

		// Varianta interpret only 
		if(Arguments::$int_only)
		{
			Tests::testInterpret($test);
		}
		// Varianta parse only nebo oboji -> rozhodne se až v testParser
		else
		{
			Tests::testParser($test);
		}

		closedir($actual_dir);
	}

	// Testovani analyzatoru
	public static function testParser($test)
	{

		shell_exec('touch ./temporary_file1');

		// Vstupní soubor pro testy
		$dir_file_path = $test->obj_directory . "/" . $test->obj_filename;

		exec('php7.3 ' . Arguments::$parse_script . ' < ' . $dir_file_path . '.src' . ' > temporary_file1' . ' 2>/dev/null', $output, $return_code);

		$expected_rc = shell_exec('cat ' . $dir_file_path . '.rc');
		
		// VARIANTA PARSE-ONLY
		if(Arguments::$parse_only)
		{
			if($expected_rc == $return_code)
			{
				// Navratove kody 0 -> potreba porovnat XML
				if($expected_rc == 0 && $return_code == 0)
				{
					exec('java -jar /pub/courses/ipp/jexamxml/jexamxml.jar ' . 'temporary_file1' . ' ' . $dir_file_path . '.out ' . 'temporary_comp' . ' /D ' . '/pub/courses/ipp/jexamxml/options', $jexam_output, $jexam_return);

					// Odebrani docasnych souboru
					if(is_file('temporary_comp'))
						exec('rm temporary_comp');

					// Odebrani docasnych souboru
					if(is_file('temporary_file1.log'))
						exec('rm temporary_file1.log');

					if($jexam_return != 0 && $jexam_return != 1)
					throw new HandlingErrors("JExamXML error", 11);
					
					// XML jsou shodne -> uspech
					if($jexam_return == 0)
					{
						if(Arguments::$parse_only)
						{
							Tests::$SuccTestsArray[] = $test;
							GlobalClass::$testPassed++;
							return;
						}
						else
						{
							Tests::testInterpret($test);
						}
					}
					// XML jsou odlisne -> neuspech
					else
					{
						$test->obj_reason = "Parse error: XML outputs are not the same."; 
						Tests::$FailTestsArray[] = $test;
						GlobalClass::$testFailed++;
						return;
					}
				}
				// Navratove kody se shoduji a nejsou 0, netreba dale testovat -> uspech
				else
				{
					Tests::$SuccTestsArray[] = $test;
					GlobalClass::$testPassed++;
					return;
				}
			// Navratove kody jsou odlisne, testujeme pouze parse -> neuspech
			}
			else
			{
			$test->obj_reason = "Parse error: Return codes are not the same. Program returned: " . $return_code . ", expected: " . $expected_rc; 
			Tests::$FailTestsArray[] = $test;
			GlobalClass::$testFailed++;
			return;
			}
		}
		// VARIANTA BOTH
		else
		{
			if($expected_rc == $return_code)
			{
				// Ocekavany navratovy kod (0) znaci, ze ma probehnout i analyza i interpretace v poradku a nase analyza vratila 0, tzn. ze parse probehl v poradku -> volam interpret  
				if($expected_rc == 0 && $return_code == 0)
				{
					Tests::testInterpret($test);
					return;
				}
				// V analyzatoru byla ocekavana chyba a nastala -> uspech
				else
				{
					Tests::$SuccTestsArray[] = $test;
					GlobalClass::$testPassed++;
					return;
				}
			}
			// Navratove kody jsou rozdilne -> bud nastane chyba az v interpretu nebo nastala v analyzatoru a nemela
			else
			{
				// Analyza i interpretace mela probehnout v poradku, ale nas analyzator vratil chybu -> neuspech
				if($expected_rc == 0 && $return_code !=0)
				{
					$test->obj_reason = "Parse error: Cannot continue to interpret, the output XML would be broken. (return code: " . $return_code .")"; 
					Tests::$FailTestsArray[] = $test;
					GlobalClass::$testFailed++;
					return;
				}
				// Chybove navratove kody specificke pro analyzator, analyzator vratil jiny kod (nerovnost kodu je zajistena ve vyssi podmince) -> neuspech 
				elseif(($expected_rc == 21 or $expected_rc == 22 or $expected_rc == 23))
				{
					$test->obj_reason = "Parse error: Return codes are not the same. Program returned: " . $return_code . ", expected: " . $expected_rc; 
					Tests::$FailTestsArray[] = $test;
					GlobalClass::$testFailed++;
					return;
				}
				// Ocekavany navratovy kod je nenulovy (chyba by mela nastat v interpretu) -> volam interpret 
				else
				{
					Tests::testInterpret($test);
					return;
				}
			}
		}
	}

	// Testovani interpretu
	public static function testInterpret($test)
	{
		shell_exec('touch ./temporary_file2');;

		$dir_file_path = $test->obj_directory . "/" . $test->obj_filename;

		if(Arguments::$int_only)
		{
			// --int-only -> Source bude testovaci soubor .src
			$source = $dir_file_path . ".src";
		}
		else
		{	
			// both -> Source je soubor z analyzy parse.php
			$source = 'temporary_file1';
		}


		exec('python3.6 ' . Arguments::$int_script . ' --source=' . $source . ' --input=' . $dir_file_path . '.in > temporary_file2 2>/dev/null' , $py_output, $return_code);

		$expected_rc = shell_exec('cat ' . $dir_file_path . '.rc');

		// Pokud se navratove kody nerovnaji -> neuspech
		if ($expected_rc != $return_code)
		{
			$test->obj_reason = "Interpret error: Return codes are not the same. Program returned: " . $return_code . ", expected: " . $expected_rc; 
			Tests::$FailTestsArray[] = $test;
			GlobalClass::$testFailed++;
			return;
		}
		else if ($expected_rc == 0) 
		{
			exec('diff ' . 'temporary_file2' .  ' ' . $dir_file_path . '.out', $diff_output, $return_code);

			if (!empty($diff_output))
			{
				print("MOJE\n" . shell_exec('cat temporary_file2') . "\n");
				print("REFERENCNI\n" . shell_exec('cat ' . $dir_file_path . '.out') . "\n");


				$test->obj_reason = "Interpret error: Output of interpret and file " . $test->obj_filename . ".out" . " are not the same"; 
				Tests::$FailTestsArray[] = $test;
				GlobalClass::$testFailed++;
				return;
			}
		}

		Tests::$SuccTestsArray[] = $test;
		GlobalClass::$testPassed++;
		return;
	}
}

class Arguments
{
	static $help = FALSE;
	static $recursive = FALSE;

	static $directory_set = FALSE;
	static $parse_script_set = FALSE;
	static $int_script_set = FALSE;

	static $directory = '';
	static $parse_script = '';
	static $int_script = ''; 

	static $parse_only = FALSE;
	static $int_only = FALSE;

	// Funkce nacita argumenty ze standardniho vstupu 
	public static function ParseArguments(array $argv)
	{
		unset($argv[0]);

		foreach ($argv as $parameter)
		{
			
			if($parameter === '--help')
			{
				if(self::$help)
					throw new HandlingErrors("Some parameter was already set", 10);

				self::$help = TRUE;
			}

			else if(substr($parameter, 0, 12) === '--directory=')
			{
				if(self::$directory_set)
					throw new HandlingErrors("Some parameter was already set", 10);

				self::$directory_set = TRUE;
				self::$directory = substr($parameter, 12);
				self::$directory = utf8_encode(self::$directory);

				if(!is_dir(self::$directory))
					throw new HandlingErrors("Directory " . self::$directory . " does not exist", 11);
			}

			else if($parameter === '--recursive')
			{
				if(self::$recursive)
					throw new HandlingErrors("Some parameter was already set", 10);

				self::$recursive = TRUE;
			}

			else if(substr($parameter, 0, 15) === '--parse-script=')
			{
				if(self::$parse_script_set)
					throw new HandlingErrors("Some parameter was already set", 10);

				self::$parse_script_set = TRUE;
				self::$parse_script = substr($parameter, 15);
				self::$parse_script = utf8_encode(self::$parse_script);

				if(!is_file(self::$parse_script))
					throw new HandlingErrors("File " . self::$parse_script . " does not exist", 11);
			}

			else if(substr($parameter, 0, 13) === '--int-script=')
			{
				if(self::$int_script_set)
					throw new HandlingErrors("Some parameter was already set", 10);

				self::$int_script_set = TRUE;
				self::$int_script = substr($parameter, 13);
				self::$int_script = utf8_encode(self::$int_script);

				if(!is_file(self::$int_script))
					throw new HandlingErrors("File " . self::$int_script . " does not exist", 11);
			}

			else if($parameter === '--parse-only')
			{
				if(self::$parse_only)
					throw new HandlingErrors("Some parameter was already set", 10);

				self::$parse_only = TRUE;
			}

			else if($parameter === '--int-only') 
			{
				if(self::$int_only)
					throw new HandlingErrors("Some parameter was already set", 10);

				self::$int_only = TRUE;
			}
			else
			{
				throw new HandlingErrors("Uknown parameter. For help use argument --help", 10);				
			}
			
			if(self::$parse_only && self::$int_script_set)
				throw new HandlingErrors("Parameters --parse-only and --int-script cannot be combined together", 10);

			else if(self::$int_only && self::$parse_script_set)
				throw new HandlingErrors("Parameters --int-only and --parse-script cannot be combined together", 10);

			else if(self::$int_only && self::$parse_only)
				throw new HandlingErrors("Parameters --int_only and --parse-only cannot be combined", 10);
		}

		if (self::$directory_set === FALSE)
		{
			self::$directory = getcwd();
		}
		if (self::$parse_script_set === FALSE)
		{
			self::$parse_script = getcwd();
			self::$parse_script .= '/parse.php';
			self::$parse_script = utf8_encode(self::$parse_script);
		}
		if (self::$int_script_set === FALSE)
		{
			self::$int_script = getcwd();
			self::$int_script .= '/interpret.py';
			self::$int_script = utf8_encode(self::$int_script);
		}

		if(self::$help && (self::$directory_set || self::$recursive || self::$parse_script_set || self::$int_script_set || self::$parse_only || self::$int_only))
			throw new HandlingErrors("Too many arguments. For help use --help only", 10);
	}	
}

class GlobalClass
{
	public static $testCounter = 0;
	public static $testPassed = 0;
	public static $testFailed = 0;

	// Funkce vypise napovedu na stdout 
	public static function displayHelp()
	{
		print("--help \t\t\tZobrazi napovedu\n\n"); 
		print("--directory=path\tTesty ze zadaného adresáře (chybí-li parametr, tak skript prochází aktuální adresář)\n\n");
		print("--recursive\t\tTesty ze zadaného adresáře i rekurzivně ve všech jeho podadresářích\n\n");
		print("--parse-script=file\tSoubor se skriptem v PHP 7.3 pro analýzu zdrojového kódu v IPP-code19 (chybí-li parametr, implicitní je parse.php)\n\n");
		print("--int-script=file\tSoubor se skriptem v Python 3.6 pro interpret XML reprezentace kódu v IPPcode19 (chybí-li  parametr, implicitní je interpret.py)\n\n");
		print("--parse-only\t\tBude testován pouze skript pro analýzu zdrojového kódu v IPPcode19 (nesmí být zároveň s --int-script)\n\n");
		print("--int-only\t\tBude testován pouze skript pro interpret XML reprezentace kódu v IPPcode19 (nesmí být zároveň s --parse-script)\n\n");

		exit(0);
	}

	// Smazani docasnych souboru
	public static function cleanTemporaryFiles()
	{
		if(is_file('temporary_file1'))
			exec('rm temporary_file1');

		if(is_file('temporary_file1.log'))
			exec('rm temporary_file1.log');

		if(is_file('temporary_file2'))
			exec('rm temporary_file2');

		if(is_file('temporary_comp'))
			exec('rm temporary_comp');
	}
}

// Trida pro vypis erroru 
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

		GlobalClass::cleanTemporaryFiles();
		exit($this->exit_code);
	}
}

?>