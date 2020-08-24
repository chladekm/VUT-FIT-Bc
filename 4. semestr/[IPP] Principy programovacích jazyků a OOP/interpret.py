#################################### MODULY ################################

import sys
import argparse
import re
import os.path
import xml.etree.ElementTree as ET

all_instructions = ["MOVE", "CREATEFRAME", "PUSHFRAME", "POPFRAME", "DEFVAR", "CALL", "RETURN", "PUSHS", "POPS", "ADD", "SUB", "MUL", "IDIV", "LT", "GT", "EQ", "AND", "OR", "NOT", "INT2CHAR", "STRI2INT", "READ", "WRITE", "CONCAT", "STRLEN", "GETCHAR", "SETCHAR", "TYPE", "LABEL", "JUMP", "JUMPIFEQ", "JUMPIFNEQ", "EXIT", "DPRINT", "BREAK"]

############################# RAMCE A ADT ##############################
GF = []
TF = None
LF = None

DataStack = []
CallStack = []
LabelsDic = {} 

ReadQueue = []

######################### ZPRACOVANI ARGUMENTU ##########################
class Arguments():

	arg_source = ''
	arg_input = ''

	arg_source_set = False
	arg_input_set = False

	arg_source_string = ''
	arg_input_string = ''

	@classmethod
	def getArguments(self):

		if len(sys.argv) > 3:
			displayError("Arguments: Forbidden count of arguments", 10)

		for arg in sys.argv[1:]:

			## --help
			if arg == "--help":
				if len(sys.argv) != 2:
					displayError("Arguments: Forbidden count of arguments. Use --help only", 10)
				else:
					displayHelp()
					sys.exit(0)
			## --source
			elif arg[:9] == "--source=":
				self.arg_source_set = True
				self.arg_source = arg[9:]

				if(not(os.path.exists(self.arg_source))):
					displayError("Arguments: Source file does not exists.", 11)

				self.arg_source = open (self.arg_source, 'r')
			## --input
			elif arg[:8] == "--input=":
				self.arg_input_set = True
				self.arg_input = arg[8:]

				if(not(os.path.exists(self.arg_input))):
					displayError("Arguments: Input file does not exists.", 11)

				self.arg_input = open (self.arg_input, 'r')
			else:
				displayError("Arguments: Unknown argument", 10)

		## Nebyl zadan ani parametr --source, ani --input
		if (not self.arg_source_set) and (not self.arg_input_set):
			displayError("Arguments: --source or --input must be set!", 10)

		## --source se bude cist ze stdin
		if not self.arg_source_set:
			Source = sys.stdin.read()

	## Funkce upravi vstup ze souboru na string
	def fileInput_to_String(what):
		
		tmp_string = ''

		for line in Arguments.__dict__[what]:
			tmp_string += line

		return tmp_string

########################## UPRAVA PORADI V XML ##########################
class Syntax():

	## Funkce prehaze instrukce a jejich argumenty tak, aby byly v korektnim poradi
	def organize_XML_order(program):

		array_ins = [None] * len(program)

		for instruction in program:
				
			## Kontrola, zda se mi do toho nezapletlo neco jineho nez instrukce (spatne XML)
			if(instruction.tag != "instruction"):
				displayError("XML syntax: Invalid XML.", 32)

			## Prevod na velka pismena pro case insensitive kontroly
			instruction.attrib["opcode"] = instruction.attrib["opcode"].upper()

			## Pokud je atribut order vetsi nez pocet instrukci nebo mensi nez 1 (indexace od 1)
			if int(instruction.attrib["order"]) > len(program) or int(instruction.attrib["order"]) < 1:
				displayError("XML syntax: Incorrect order numbers.", 32)

			## Kontrola, zda je instrukce validni (je instrukci)
			if not(instruction.attrib["opcode"] in all_instructions):
				displayError("XML syntax: Invalid XML - not an instruction.", 32)

			## Kontrola zda 2 instrukce nemaji uvedene stejne poradi
			if array_ins[int(instruction.attrib["order"]) - 1] != None:
				displayError("XML syntax: Two instruction has same order number.", 32)

			array_arg = list.copy(list(instruction))

			## Serazeni argumentu instrukce
			for argument in array_arg:
				if int(argument.tag[-1:]) > len(instruction):
					displayError("XML syntax: Invalid argument in XML.", 32)

				instruction[int(argument.tag[-1:]) - 1] = argument

			## Uvedeni instrukce na správnou pozici v XML
			array_ins[int(instruction.attrib["order"]) - 1] = instruction

		return array_ins

	## Funkce overuje atributy, pocet a typ operandu
	def check_InstructionSyntax(instruction, operand_array):

		if len(instruction) != len(operand_array):
			displayError("Syntax: Invalid count of arguments.", 32)		

		if len(instruction) == 0:
			return 0

		i = 0

		check_arg_array = []

		## Serazeni argumentu instrukce
		array_arg = list.copy(list(instruction))
		for argument in array_arg:
			if int(argument.tag[-1:]) > len(instruction):
				displayError("XML syntax: Invalid argument in XML.", 32)

			instruction[int(argument.tag[-1:]) - 1] = argument

		## Porovnavaji se typy argumentu (referencni se vstupem)
		for argument in instruction:
			
			## Kontroluje, aby se nevyskytlo vicekrat napr arg1
			if(argument.tag != ("arg" + str(i+1))):
				displayError("Syntax: Invalid arguments.", 32)

			try:
				attrib_type = argument.attrib["type"]
				if len(argument.attrib) > 1: 
					raise
			except:
				displayError("XML: XML is not well formed.", 31)


			## symb ##
			if operand_array[i] == "symb":

				if (attrib_type == "int"):    
					if (re.fullmatch('^[+-]?[0-9]+$', argument.text) == None):
						displayError("Syntax: Incorrect format of integer.", 32)
						
				elif attrib_type == "string":

					if argument.text == None:
						pass
					elif (re.fullmatch('^(\w|\\\\[0-9]{3}|[-_$&%*+:,().!?><\"\'@/])*$', argument.text) == None):
						displayError("Syntax: Incorrect format of string.", 32)
				
				elif attrib_type == "bool":
					if (re.fullmatch('^(true|false)$', argument.text) == None):
						displayError("Syntax: Incorrect format of bool.", 32)
				
				elif attrib_type == "nil":
					if (re.fullmatch('^nil$', argument.text) == None):
						displayError("Syntax: Incorrect format of nil.", 32)

				elif attrib_type == "var":
					if (re.fullmatch('^(GF|LF|TF)@([a-z]|[A-Z]|[-_$&%*+!?><\"\'/])(\w|[-_$&%*+!?><\"\'/])*$', argument.text) == None):
						displayError("Syntax: Incorrect format of variable.", 32)
				else:
					displayError("Syntax: Invalid type of argument.", 32)

			## var ##
			elif operand_array[i] == "var" and attrib_type == "var":
				if (re.fullmatch('^(GF|LF|TF)@([a-z]|[A-Z]|[-_$&%*+!?><\"\'/])(\w|[-_$&%*+!?><\"\'/])*$', argument.text) == None):
						displayError("Syntax: Incorrect format of variable.", 32)
			
			## type ##
			elif operand_array[i] == "type" and attrib_type == "type":
				if (re.fullmatch('^(int|string|bool)$', argument.text) == None):
						displayError("Syntax: Incorrect format of type.", 32)

			## label ##
			elif operand_array[i] == "label" and attrib_type == "label":
				if (re.fullmatch('^([a-z]|[A-Z]|[-_$&%*+!?><\"\'/])([a-z]|[A-Z]|[0-9]|[-_$&%*+!?><\"\'/])*$', argument.text) == None):
						displayError("Syntax: Incorrect format of label.", 32)
				elif (instruction.attrib["opcode"] == "LABEL"):
					Interpret.m_LABEL(instruction)

			## odlisny referencni a aktualni typ ##
			elif operand_array[i] != attrib_type:
				displayError("Syntax: Invalid type of argument.", 32)
			
			i += 1

		return 0

	## Funkce kontroluje atributy instrukce a vola kontrolu syntaxe instrukci
	def checkSyntax(program):

		try:
			## Kontrola hlavičky
			if program.attrib["language"].upper() != "IPPCODE19":
				raise

			if len(program.attrib) > 3:
				raise
			elif len(program.attrib) == 3:
				if "name" not in program.attrib or "description" not in program.attrib:
					raise
			elif len(program.attrib) == 2:
				if "name" in program.attrib or "description" in program.attrib:
					pass
				else:
					raise
		except:
			displayError("XML: XML is not well formed", 31)

		instruction_operands = { 
			"MOVE": "var symb", 
			"CREATEFRAME": "", 
			"PUSHFRAME": "", 
			"POPFRAME": "", 
			"DEFVAR": "var", 
			"CALL": "label", 
			"RETURN": "", 
			"PUSHS": "symb", 
			"POPS": "var", 
			"ADD": "var symb symb", 
			"SUB": "var symb symb", 
			"MUL": "var symb symb", 
			"IDIV": "var symb symb", 
			"LT": "var symb symb", 
			"GT" : "var symb symb", 
			"EQ": "var symb symb", 
			"AND": "var symb symb", 
			"OR": "var symb symb", 
			"NOT": "var symb", 
			"INT2CHAR": "var symb", 
			"STRI2INT": "var symb symb", 
			"READ": "var type",
			"WRITE": "symb", 
			"CONCAT": "var symb symb", 
			"STRLEN": "var symb", 
			"GETCHAR": "var symb symb", 
			"SETCHAR": "var symb symb", 
			"TYPE": "var symb", 
			"LABEL": "label",
			"JUMP": "label",
			"JUMPIFEQ": "label symb symb",
			"JUMPIFNEQ": "label symb symb",
			"EXIT": "symb",
			"DPRINT": "symb",
			"BREAK": "", 
		}

		for instruction in program:
			
			if "opcode" not in instruction.attrib or "order" not in instruction.attrib or len(instruction.attrib) > 2:
				displayError("XML: XML is not well formed", 31)

			operand_string = instruction_operands.get(instruction.attrib["opcode"].upper(), "nothing")
			if operand_string == "nothing":
				displayError("Syntax: Not an instruction.", 32)
				
			operand_array = list()
			
			for operand in operand_string.split():
				operand_array.append(operand)

			Syntax.check_InstructionSyntax(instruction, operand_array)

############################# PRACE S RAMCI ##############################
class FrameOperations():

	## Funkce zkontroluje existenci ramce
	def test_Frame_existance(frame):
		if(frame == "GF"):
			return
		elif(frame == "LF"):
			if(LF == None):
				displayError("Frames: Frame was not created.", 55)
		elif(frame == "TF"):
			if(TF == None):
				displayError("Frames: Frame was not created.", 55)

	## Funkce vklada do prislusneho ramce
	def insert_to_Frame(frame, name, value, type):
		FrameOperations.test_Frame_existance(frame)

		if frame == "LF":
			insert = frame + "[0].insert(0, [name, value, type])"
		else:
			insert = frame + ".insert(0, [name, value, type])"

		eval(insert)
			
	## Funkce, ktera najde v ramci promennou podle jmena a vrati jeji pozici v danem listu
	def find_variable(frame, name):

		FrameOperations.test_Frame_existance(frame)

		if(frame == "LF"):
			search_frame = frame + "[0][i]"
			length_of_frame = len(LF[0])
		else:
			search_frame = frame + "[i]"
			length_of_frame = len(eval(frame))
		
		i=0

		for i in range(0,length_of_frame):
		
			## Jedna polozka v ramci
			item = (eval(search_frame))

			if item[0] == name:
				if frame == "LF":
				 	return_position = frame + "[0]" + "[" + str(i) + "]"
				else:
				 	return_position = frame + "[" + str(i) + "]"

				return return_position

		return None

	## Aktualizuje hodnotu promenne
	def actualize_var_value(frame, name, new_value, new_type):
		
		position = FrameOperations.find_variable(frame, name)
		
		if position == None:
			displayError("Frames: Variable is not defined in this frame", 54)

		index = position[-2:]
		index = index[:1]
		index = int(index)

		current_type = eval(position + "[2]")

		if frame == "GF":
			GF[index][1] = new_value
			GF[index][2] = new_type	
		elif frame == "LF": 
			LF[0][index][1] = new_value
			LF[0][index][2] = new_type
		elif frame == "TF": 
			TF[index][1] = new_value
			TF[index][2] = new_type
		
		return 0

	## Funkce najde promennou a vrati jeji hodnotu a typ
	def find_and_return_value_and_type(frame, name):

		position = FrameOperations.find_variable(frame, name)

		if position == None:
			displayError("Frames: Variable is not defined in this frame", 54)

		value = eval(position)
		return value[1], value[2]
	
########################## SAMOTNA INTERPRETACE ##########################
class Interpret():

	actual_instruction = 0
	finished_instruction = 0

	## Hlavni funkce, ktera provadi interpretaci volanim prislusnych funkci pro instrukce
	def runProgram(program):

		switcher = { 
			"MOVE": "m_MOVE",
			"CREATEFRAME": "m_CREATEFRAME",
			"PUSHFRAME": "m_PUSHFRAME",
			"POPFRAME": "m_POPFRAME",
			"DEFVAR": "m_DEFVAR",
			"CALL": "m_CALL",
			"RETURN": "m_RETURN",
			"PUSHS": "m_PUSHS",
			"POPS": "m_POPS",
			"ADD": "m_CALCULATOR",
			"SUB": "m_CALCULATOR",
			"MUL": "m_CALCULATOR",
			"IDIV": "m_CALCULATOR",
			"LT": "m_COMPARE",
			"GT" : "m_COMPARE",
			"EQ": "m_COMPARE",
			"AND": "m_LOGICAL",
			"OR": "m_LOGICAL",
			"NOT": "m_LOGICAL",
			"INT2CHAR": "m_INT2CHAR",
			"STRI2INT": "m_STRI2INT",
			"READ": "m_READ",
			"WRITE": "m_WRITE",
			"CONCAT": "m_CONCAT",
			"STRLEN": "m_STRLEN",
			"GETCHAR": "m_GETCHAR",
			"SETCHAR": "m_SETCHAR",
			"TYPE": "m_TYPE",
			"JUMP": "m_JUMP",
			"JUMPIFEQ": "m_JUMPIF",
			"JUMPIFNEQ": "m_JUMPIF",
			"EXIT": "m_EXIT",
			"DPRINT": "m_DPRINT",
			"BREAK": "m_BREAK",
		}

		i = 0

		while i < len(program):
			
			function = switcher.get(program[i].attrib["opcode"], lambda: "Invalid instruction")
			
			Interpret.actual_instruction = i+1
			if program[i].attrib["opcode"] == "LABEL":
				return_code = 0
			else:
				function = "Interpret." + function + "(program[i])"
				## Zavolani prislusne funkce
				return_code = eval(function)

			Interpret.finished_instruction += 1

			if return_code != 0:
				i = return_code
			else:
				i += 1


	## Funkce nahradi escape sekvence ve formatu \xyz
	def replace_string_escape_sequences(argument):

		if argument.count("\\") == 0:
			return argument

		### nalezeni escape sekvenci
		escape_array = re.findall('\\\\[0-9]{3}', argument)

		## Odebrani duplicit
		escape_array = list(set(escape_array))
		

		while len(escape_array) != 0:
			
			what_to_replace = escape_array.pop()
			
			## Nahrazeni escape sekvence
			argument = argument.replace(what_to_replace, chr(int(what_to_replace[1:])))

		return argument


	## Funkce upravi vstupni hodnoty ze stringu na korektni hodnoty
	def correct_input_values(arg_value, arg_type, instruction_name = None):
		if arg_type == "int":
			arg_value = int(arg_value)
		elif arg_type == "bool" and arg_value == "true":
			arg_value = True
		elif arg_type == "bool" and arg_value == "false":
			arg_value = False
		elif arg_type == "string" and arg_value == None:
			arg_value = ""
		elif arg_type == "string":
			if instruction_name != "READ":
				arg_value = Interpret.replace_string_escape_sequences(arg_value)

		return arg_value

	## Funkce rozdeli promennou na jeji hodnotu a typ
	def get_Value_and_Type(argument, instruction_name = None):
		

		if(argument.attrib["type"] == "var"):
			arg_frame, arg_name = Interpret.get_Frame_and_Name(argument)
			arg_value, arg_type= FrameOperations.find_and_return_value_and_type(arg_frame, arg_name)
		else:
			arg_type = argument.attrib["type"]
			arg_value = Interpret.correct_input_values(argument.text, arg_type, instruction_name) 

		if arg_type == None and instruction_name != "TYPE":
			displayError("Interpret: Uninitialized variable", 56)

		return arg_value, arg_type

	## Funkce rozdeli promennou na ramec a jeji nazev a vrati je
	def get_Frame_and_Name(arg):
		arg = arg.text.split("@",1)
		return arg[0], arg[1]


	####################### INSTRUKCE #######################

 	## 01	MOVE <var> <symb> 	
	def m_MOVE(instruction):
		
		arg1_frame, arg1_name = Interpret.get_Frame_and_Name(instruction[0])
		
		new_value, new_type = Interpret.get_Value_and_Type(instruction[1])

		FrameOperations.actualize_var_value(arg1_frame, arg1_name, new_value, new_type)
		return 0

	## 02	CREATEFRAME 
	def m_CREATEFRAME(instruction):
		
		global TF
		TF = []

		return 0

	## 03	PUSHFRAME 
	def m_PUSHFRAME(instruction):
		
		global LF,TF
		if(LF == None):
			LF=[]

		FrameOperations.test_Frame_existance("TF")
		
		LF.insert(0, TF)
		TF = None

		return 0

	## 04	POPFRAME 
	def m_POPFRAME(instruction):
		
		global LF,TF

		FrameOperations.test_Frame_existance("LF")
		TF=[]

		for item in LF[0]:
			TF.insert(0, item)
		
		LF.pop(0)

		if len(LF) == 0: 
			LF = None

		return 0

	## 05	DEFVAR <var> 
	def m_DEFVAR(instruction):

		arg1_frame, arg1_name = Interpret.get_Frame_and_Name(instruction[0])

		if FrameOperations.find_variable(arg1_frame, arg1_name) != None:
			displayError("Interpret: Variable already exists.", 57)

		FrameOperations.insert_to_Frame(arg1_frame, arg1_name, None, None)
		return 0

	## 06	CALL <label> 
	def m_CALL(instruction):
		
		CallStack.append(Interpret.actual_instruction)
		return Interpret.m_JUMP(instruction)

	## 07	RETURN 
	def m_RETURN(instruction):
		
		if len(CallStack) == 0:
			displayError("Interpret: Trying to return, but stack is empty.", 56)
		
		position = CallStack.pop()
		return position
	
	## 08	PUSHS <symb> 
	def m_PUSHS(instruction):
		
		arg1_value, arg1_type = Interpret.get_Value_and_Type(instruction[0])
		DataStack.append([arg1_value, arg1_type])
		return 0

	## 09	POPS <var> 
	def m_POPS(instruction):

		if len(DataStack) == 0:
			displayError("Interpret: Trying to pop from empty stack.", 56)

		arg1_frame, arg1_name = Interpret.get_Frame_and_Name(instruction[0])
		
		new_value, new_type = DataStack.pop()

		FrameOperations.actualize_var_value(arg1_frame, arg1_name, new_value, new_type)
		return 0

	## 10	ADD <var> <symb1> <symb2> 
	## 11	SUB <var> <symb1> <symb2> 
	## 12	MUL <var> <symb1> <symb2> 
	## 13	IDIV <var> <symb1> <symb2> 
	def m_CALCULATOR(instruction):
		
		operation = (instruction.attrib["opcode"])

		arg1_frame, arg1_name = Interpret.get_Frame_and_Name(instruction[0])
		
		arg2_value, arg2_type = Interpret.get_Value_and_Type(instruction[1])
		arg3_value, arg3_type = Interpret.get_Value_and_Type(instruction[2])

		if arg2_type != "int" or  arg3_type != "int":
			displayError("Interpret: Incompatible types in calculation.", 53)
		else:
			new_type = "int"

		if operation == "ADD":
			new_value = arg2_value + arg3_value
		elif operation == "SUB":
			new_value = arg2_value - arg3_value
		elif operation == "MUL":
			new_value = arg2_value * arg3_value
		elif operation == "IDIV":
			if arg3_value == 0:
				displayError("Interpret: Trying to divide by 0.", 57)
			else:
				new_value = arg2_value // arg3_value

		FrameOperations.actualize_var_value(arg1_frame, arg1_name, new_value, new_type)
		return 0

	## 14	LT <var> <symb1> <symb2> 
	## 15	GT <var> <symb1> <symb2> 
	## 16	EQ <var> <symb1> <symb2> 
	def m_COMPARE(instruction):

		operation = (instruction.attrib["opcode"])

		arg1_frame, arg1_name = Interpret.get_Frame_and_Name(instruction[0])
		
		arg2_value, arg2_type = Interpret.get_Value_and_Type(instruction[1])
		arg3_value, arg3_type = Interpret.get_Value_and_Type(instruction[2])

		if arg2_type != arg3_type and (operation == "LT" or operation == "GT"):
			displayError("Interpret: Incompatible types in comparison.", 53)
		
		if arg2_type == "nil" and operation != "EQ":
			displayError("Interpret: Type nil can be compared only in instruction EQ.", 53)

		if operation == "EQ" and (arg2_type != arg3_type) and (arg2_type != "nil" and arg3_type != "nil"):
			displayError("Interpret: In instruction EQ can be different types only in comparision with nil.", 53)

		if operation == "LT":
			result = True if arg2_value < arg3_value else False
		elif operation == "GT":
			result = True if arg2_value > arg3_value else False
		elif operation == "EQ":
			result = True if arg2_value == arg3_value else False

		FrameOperations.actualize_var_value(arg1_frame, arg1_name, result, 'bool')	
		return 0

	## 17	AND <var> <symb1> <symb2> 
	## 18	OR <var> <symb1> <symb2> 
	## 19	NOT <var> <symb1>
	def m_LOGICAL(instruction):
		
		operation = (instruction.attrib["opcode"])

		arg1_frame, arg1_name = Interpret.get_Frame_and_Name(instruction[0])
		
		arg2_value, arg2_type = Interpret.get_Value_and_Type(instruction[1])
		
		if operation == "AND" or operation == "OR":
			arg3_value, arg3_type = Interpret.get_Value_and_Type(instruction[2])
			if arg3_type != "bool":
				displayError("Interpret: Incompatible types in logical operation.", 53)

		if arg2_type != "bool":
			displayError("Interpret: Incompatible types in logical operation.", 53)

		if operation == "AND":
			result = (arg2_value and arg3_value)
		elif operation == "OR":
			result = (arg2_value or arg3_value)
		elif operation == "NOT":
			result = (not arg2_value)

		FrameOperations.actualize_var_value(arg1_frame, arg1_name, result, 'bool')	
		return 0
	
	## 20	INT2CHAR <var> <symb> 
	def m_INT2CHAR(instruction):
		
		arg1_frame, arg1_name = Interpret.get_Frame_and_Name(instruction[0])
		
		new_value, new_type = Interpret.get_Value_and_Type(instruction[1])

		if new_type != "int": 
			displayError("Interpret: Integer needed to convertion", 53)

		if new_value < 0 or new_value > 1114111:
			displayError("Interpret: Value cannot be converted to char.", 58)

		new_value = chr(int(new_value))
		FrameOperations.actualize_var_value(arg1_frame, arg1_name, new_value, 'string')	
		return 0

	## 21	STRI2INT <var> <symb1> <symb2> 
	def m_STRI2INT(instruction):

		arg1_frame, arg1_name = Interpret.get_Frame_and_Name(instruction[0])
		
		arg2_value, arg2_type = Interpret.get_Value_and_Type(instruction[1])
		arg3_value, arg3_type = Interpret.get_Value_and_Type(instruction[2])

		if arg2_type != "string" or arg3_type != "int": 
			displayError("Interpret: Cannot do convertion string to int", 53)
		
		arg3_value = int(arg3_value)

		if arg3_value >= len(arg2_value) or arg3_value < 0:
			displayError("Interpret: Index is greather than length of string", 58)

		char = arg2_value[arg3_value]
		integer = ord(char)

		FrameOperations.actualize_var_value(arg1_frame, arg1_name, integer, 'int')	
		return 0

	## 22	READ <var> <type> 
	def m_READ(instruction):

		arg1_frame, arg1_name = Interpret.get_Frame_and_Name(instruction[0])
		arg2_value, arg2_type = Interpret.get_Value_and_Type(instruction[1], "READ")

		exception = False

		if Arguments.arg_input_set:
			try:
				readed_value = ReadQueue.pop()
			except:
				exception = True
		else:
			try:
				readed_value = input()
			except:
				exception = True

		if arg2_value == "int":
			try:
				readed_value = int(readed_value)
			except:
				readed_value = 0

		elif arg2_value == "bool":
			if readed_value.lower() == 'true':
				readed_value = True
			else:
				readed_value = False

		elif arg2_value == "string":
			if exception:
				readed_value = ''
			else:
				readed_value = str(readed_value)

		FrameOperations.actualize_var_value(arg1_frame, arg1_name, readed_value, arg2_value)
		return 0

	## 23	WRITE <symb> 
	def m_WRITE(instruction):

		arg_value, arg_type = Interpret.get_Value_and_Type(instruction[0])

		if arg_type == "bool":
			if arg_value:
				print("true", end='')
			else:
				print("false", end='')
		elif arg_type == "nil":
			print("", end='')
		else:
			print(arg_value, end='')

		return 0

	## 24	CONCAT <var> <symb1> <symb2> 
	def m_CONCAT(instruction):
		
		arg1_frame, arg1_name = Interpret.get_Frame_and_Name(instruction[0])
		
		arg2_value, arg2_type = Interpret.get_Value_and_Type(instruction[1])
		arg3_value, arg3_type = Interpret.get_Value_and_Type(instruction[2])

		if arg2_type != "string" or arg3_type != "string":
			displayError("Interpret: Strings needed for concatenation", 53)

		FrameOperations.actualize_var_value(arg1_frame, arg1_name, arg2_value + arg3_value, 'string')
		return 0
	
	## 25	STRLEN <var> <symb> 
	def m_STRLEN(instruction):

		arg1_frame, arg1_name = Interpret.get_Frame_and_Name(instruction[0])
		
		arg2_value, arg2_type = Interpret.get_Value_and_Type(instruction[1])

		if arg2_type != "string":
			displayError("Interpret: Strings needed for strlen", 53)

		FrameOperations.actualize_var_value(arg1_frame, arg1_name, len(arg2_value), 'int')
		return 0
	
	## 26	GETCHAR <var> <symb1> <symb2> 
	def m_GETCHAR(instruction):
		
		arg1_frame, arg1_name = Interpret.get_Frame_and_Name(instruction[0])
		arg1_value, arg1_type= FrameOperations.find_and_return_value_and_type(arg1_frame, arg1_name)
		
		arg2_value, arg2_type = Interpret.get_Value_and_Type(instruction[1])
		arg3_value, arg3_type = Interpret.get_Value_and_Type(instruction[2])

		if arg2_type != "string" or arg3_type != "int": 
			displayError("Interpret: Incorrect types in getchar.", 53)

		if arg3_value >= len(arg2_value) or arg3_value < 0 or len(arg2_value) == 0:
			displayError("Interpret: Index is greather than length of string or string is empty.", 58)

		char = arg2_value[arg3_value]

		FrameOperations.actualize_var_value(arg1_frame, arg1_name, char, 'string')	
		return 0

	## 27	SETCHAR <var> <symb1> <symb2> 
	def m_SETCHAR(instruction):

		arg1_frame, arg1_name = Interpret.get_Frame_and_Name(instruction[0])
		arg1_value, arg1_type= FrameOperations.find_and_return_value_and_type(arg1_frame, arg1_name)
		
		arg2_value, arg2_type = Interpret.get_Value_and_Type(instruction[1])
		arg3_value, arg3_type = Interpret.get_Value_and_Type(instruction[2])

		if arg1_type != "string" or arg2_type != "int" or arg3_type != "string": 
			displayError("Interpret: Incorrect types in setchar.", 53)

		if arg2_value >= len(arg1_value) or arg2_value < 0 or len(arg3_value) == 0:
			displayError("Interpret: Index is greather than length of string or string is empty.", 58)

		char = arg3_value[0]

		new_string = arg1_value[:arg2_value] + char + arg1_value[arg2_value+1:]

		FrameOperations.actualize_var_value(arg1_frame, arg1_name, new_string, 'string')	
		return 0

	## 28	TYPE <var> <symb> 
	def m_TYPE(instruction):
		
		arg1_frame, arg1_name = Interpret.get_Frame_and_Name(instruction[0])
		
		arg2_value, arg2_type = Interpret.get_Value_and_Type(instruction[1], instruction.attrib["opcode"])

		if arg2_type == "int":
			type_of_symb = 'int'
		elif arg2_type == "string":
			type_of_symb = 'string'
		elif arg2_type == "bool":
			type_of_symb = 'bool'
		elif arg2_type == "nil":
			type_of_symb = 'nil'
		elif arg2_type == None:
			type_of_symb = ''

		FrameOperations.actualize_var_value(arg1_frame, arg1_name, type_of_symb, 'string')	

		return 0
	
	## 29	LABEL <label> 
	def m_LABEL(instruction):

		if instruction[0].text in LabelsDic:
			displayError("Interpret: Trying to redefine label.", 52)

		LabelsDic.update({instruction[0].text : int(instruction.attrib["order"])})

		return 0

	## 30	JUMP <label> 
	def m_JUMP(instruction):
		
		jump_row = LabelsDic.get(instruction[0].text, None)
		if jump_row == None:
			displayError("Interpret: Undefined label", 52)

		return jump_row
	
	## 31	JUMPIFEQ <label> <symb1> <symb2> 
	## 32	JUMPIFNEQ <label> <symb1> <symb2> 
	def m_JUMPIF(instruction):
		
		operation = (instruction.attrib["opcode"])
		
		arg2_value, arg2_type = Interpret.get_Value_and_Type(instruction[1])
		arg3_value, arg3_type = Interpret.get_Value_and_Type(instruction[2])

		if arg2_type != arg3_type:
			displayError("Interpret: Incompatible types in comparison.", 53)

		result = True if arg2_value == arg3_value else False

		jump_row = LabelsDic.get(instruction[0].text, None)

		if jump_row == None:
			displayError("Interpret: Undefined label", 52)

		if operation == "JUMPIFEQ" and result == True:
			return jump_row
		elif operation == "JUMPIFNEQ" and result == False:
			return jump_row
		else:
			return 0

	## 33	EXIT <symb> 
	def m_EXIT(instruction):

		arg_value, arg_type = Interpret.get_Value_and_Type(instruction[0])

		if arg_type != "int":
			displayError("Interpret: Invalid type.", 53)

		if arg_value > 49 or arg_value < 0:
			displayError("Interpret: Invalid value for exit.", 57)
		else:
			sys.exit(arg_value)

		return 0
	
	## 34	DPRINT <symb> 
	def m_DPRINT(instruction):

		arg_value, arg_type = Interpret.get_Value_and_Type(instruction[0])
		print(arg_value, file=sys.stderr)

		return 0

	## 35	BREAK 
	def m_BREAK(instruction):

		print("======================= PROGRAM INFO =======================", file=sys.stderr)
		print("Actual instruction (position in code):", Interpret.actual_instruction, file=sys.stderr)
		print("Instructions finished:", Interpret.finished_instruction, file=sys.stderr)
		print("========================== FRAMES ==========================", file=sys.stderr)
		print("Global Frame:", GF, file=sys.stderr)
		print("\nLocal Frame:", LF, file=sys.stderr)
		print("\nTemporary Frame:", TF, file=sys.stderr)
		print("======================== OTHER STACKS ======================", file=sys.stderr)
		print("Data Stack:", DataStack, file=sys.stderr)
		print("\nCall stack:", CallStack, file=sys.stderr)
		print("\nInitialized Labels:", LabelsDic, file=sys.stderr)
		print("============================================================", file=sys.stderr)

		return 0

############################## MAIN ###############################
def main():	

	Arguments.getArguments()

	if Arguments.arg_source_set == True:
		Arguments.arg_source_string = Arguments.fileInput_to_String("arg_source")

	if Arguments.arg_input_set == True:
		Arguments.arg_input_string = Arguments.fileInput_to_String("arg_input")
		global ReadQueue
		ReadQueue = Arguments.arg_input_string.split('\n',)
		ReadQueue = ReadQueue[::-1]

	try:
		program = ET.fromstring(Arguments.arg_source_string)	
	except:
		displayError("Main: Invalid XML.", 32)			

	Syntax.checkSyntax(program)

	program = Syntax.organize_XML_order(program)
	
	Interpret.runProgram(program)

	closeFiles()

########################### VYPIS HELP #############################
def displayHelp():
	print("\nProgram nacte XML reprezentaci programu ze zadaneho souboru a tento program s vyuzitim \nstandartniho vstupu a výstupu interpretuje.")
	print("\nSkript pracuje s nasledujicimi argumenty:")
	print("--help \t\tvypise napovedu")
	print("--source=file \tvstupní soubor s XML reprezentací zdrojového kódu")
	print("--input=file \tsoubor se vstupy pro samotnou interpretaci zdrojoveho kodu")
	print("\nAlespon jeden z parametru (--source nebo --input) musi byt vzdy zadan.\nPokud jeden z nich chybi, tak jsou odpovidajici data nacitana ze standardniho vstupu.")

########################### VYPIS ERRORU ############################
def displayError(message, exit_code):
	print("[Error]", message, "Exit code:", exit_code, file=sys.stderr)
	closeFiles()
	sys.exit(exit_code)

########################## ZAVRENI SOUBORU ##########################
def closeFiles():
	try:
		if Arguments.arg_source_set:
			Arguments.arg_source.close()
		if Arguments.arg_input_set:
			Arguments.arg_input.close()
	except:
		pass

## Zavolani funkce main
if __name__ == "__main__":
	main()