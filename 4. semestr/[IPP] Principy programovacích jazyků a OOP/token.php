<?php

class Token
{
	public $token_type;
	public $token_atribute;

	public function __construct($type, $atribute)
	{
		$this->token_type = $type;
		$this->token_atribute = $atribute;
	}
}

/** Pole typů tokenu **/
class TokenType
{
	/** Instrukce **/
	public static $instructions = array
	(
		'T_INS_MOVE' => "MOVE",
		'T_INS_CREATEFRAME' => "CREATEFRAME",
		'T_INS_PUSHFRAME' => "PUSHFRAME",
		'T_INS_POPFRAME' => "POPFRAME",
		'T_INS_DEFVAR' => "DEFVAR",
		'T_INS_CALL' => "CALL",
		'T_INS_RETURN' => "RETURN",
		'T_INS_PUSHS' => "PUSHS",
		'T_INS_POPS' => "POPS",
		'T_INS_ADD' => "ADD",
		'T_INS_SUB' => "SUB",
		'T_INS_MUL' => "MUL",
		'T_INS_IDIV' => "IDIV",
		'T_INS_LT' => "LT",
		'T_INS_GT' => "GT",
		'T_INS_EQ' => "EQ",
		'T_INS_AND' => "AND",
		'T_INS_OR' => "OR",
		'T_INS_NOT' => "NOT",
		'T_INS_INT2CHAR' => "INT2CHAR",
		'T_INS_STRI2INT' => "STRI2INT",
		'T_INS_READ' => "READ",
		'T_INS_WRITE' => "WRITE",
		'T_INS_CONCAT' => "CONCAT",
		'T_INS_STRLEN' => "STRLEN",
		'T_INS_GETCHAR' => "GETCHAR",
		'T_INS_SETCHAR' => "SETCHAR",
		'T_INS_TYPE' => "TYPE",
		'T_INS_LABEL' => "LABEL",
		'T_INS_JUMP' => "JUMP",
		'T_INS_JUMPIFEQ' => "JUMPIFEQ",
		'T_INS_JUMPIFNEQ' => "JUMPIFNEQ",
		'T_INS_EXIT' => "EXIT",
		'T_INS_DPRINT' => "DPRINT",
		'T_INS_BREAK' => "BREAK",
	);

	/** Rámce **/
	public static $frames = array
	(
		'T_LF' => "LF",
		'T_GF' => "GF",
		'T_TF' => "TF",
	);

	/** Konstanty & Proměnné **/
	public static $values = array
	(
		'T_SYMB_INT' => "int",
		'T_SYMB_BOOL' => "bool",
		'T_SYMB_STRING' => "string",
		'T_SYMB_NIL' => "nil",
		
		'T_VAR' => "var",
	);

	/** Návěští & Typ **/
	public static $others = array
	(
		'T_LABEL' => "label_desc",
		'T_TYPE' => "type",
	);
}

?>