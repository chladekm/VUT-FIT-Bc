#!/bin/bash

######################################################################
### AUTHOR: Martin Chladek					   ###
### PROJECT: IOS - wrapper - 1st project			   ###
### DATE: 20. 3. 2018						   ###
######################################################################

### TEST ZDA POUZITE PRIKAZY ODPOVIDAJI STANDARDU POSIX --------------

export LC_ALL=POSIX
POSIXLY_CORRECT=yes

### TEST ZDA EXISTUJE REALPATH ---------------------------------------

if [ command -v realpath >/dev/null 2>&1 ]; then
	echo "Command 'realpath' does not exist" >&2
	exit 126
fi


### TEST WEDI_RC A JESTLI SE DA ZAPISOVAT DO SOUBORU -----------------
if [ -z $WEDI_RC ]; then
	echo "WEDI_RC is empty" >&2
	exit 1
else
	if [ ! -f $WEDI_RC ]; then
		mkdir -p `dirname $WEDI_RC`	
		touch $WEDI_RC
	fi
fi

if [ -d $WEDI_RC ]; then
	echo "WEDI_RC is a directory, change it please" >&2
	exit 1
fi

if [ ! -w $WEDI_RC ]; then
	echo "WEDI_RC is not writable" >&2
	exit 1
fi

### FUNKCE PRO EDITACI -----------------------------------------------

editing()
{
	if [ ! -z $EDITOR ]; then
		$EDITOR "$myADRESS"
	elif [ ! -z $VISUAL ]; then
		$VISUAL "$myADRESS"
	
	else
		vi "$myADRESS"
	fi

	echo $myFILE:$myADRESS:$(date +%Y-%m-%d) $'\n'"$(cat "$WEDI_RC")" > "$WEDI_RC"
	exit 0
}

### wedi [ADRESAR] (NEBO JEN wedi)------------------------------------

onlydirec()
{	
		
	myADRESS=`cat "$WEDI_RC" | grep "$myADRESS" | cut -f2 -d ':' | grep -E "$myADRESS/[^/]+$" | head -n1`
	
	myFILE=`basename "$myADRESS"`

	if [ ! -f "$myADRESS" ]; then
		echo "File was not found or no file edited in this folder yet" >&2
		exit 1
	else
		editing
	fi
}


### wedi -m [ADRESAR] (NEBO JEN wedi -m)------------------------------

wedi_most()
{	
	tempADRESS=`cat "$WEDI_RC" | grep "$myADRESS" | cut -f2 -d ':' | uniq -c | sort -g | grep -E "$myADRESS/[^/]+$" | sort -k1,1 -n -r -k2,2 | head -n1`
	
	myFILE=`basename "$tempADRESS"`

	myADRESS="$myADRESS/$myFILE"
	
	if [ ! -f "$myADRESS" ]; then
		echo "No file to edit in this folder" >&2
		exit 1
	else
		editing
	fi
}

### wedi -l [ADRESAR] (NEBO JEN wedi -l)------------------------------

wedi_list()
{	
	myLIST=`grep "$myADRESS/[^/]*$" "$WEDI_RC" | cut -f1 -d ':' | sort | uniq`
	
	if [ -z "$myLIST" ]; then
		echo "No files edited in this directory" >&2
		exit 1
	else
		echo "$myLIST"
		exit 0
	fi 
}

### wedi -b [ADRESAR] (NEBO JEN wedi -b)------------------------------

wedi_before()
{	
	i=0	
	noLINES=`grep -c "$myADRESS/[^/]*$" "$WEDI_RC" ` #NumberOfLines

	myYEAR=`echo "$myDATE" | cut -f1 -d '-'`
	myMONTH=`echo "$myDATE" | cut -f2 -d '-'`
	myDAY=`echo "$myDATE" | cut -f3 -d '-'`

	while [ $noLINES -gt $i ]; do
		
		actualPATH=`grep "$myADRESS/[^/]*$" "$WEDI_RC" | sed "${noLINES}q;d" | rev | cut -f2 -d ':' | rev`
		actualDATE=`grep "$myADRESS/[^/]*$" "$WEDI_RC" | sed "${noLINES}q ;d" | cut -f3 -d ':' `
		actualFILE=`basename "$actualPATH"`
		
		actualYEAR=`echo "$actualDATE" | cut -f1 -d '-'`
		actualMONTH=`echo "$actualDATE" | cut -f2 -d '-'`
		actualDAY=`echo "$actualDATE" | cut -f3 -d '-'`

		if [[ "$actualMONTH" > 12 ]] || [[ "$myMONTH" > 12 ]]; then
			echo "Wrong date fotmat - month can not be over 12"
			exit 1
		elif [[ "$actualDAY" > 31 ]] || [[ "$myDAY" > 31 ]]; then
			echo "Wrong date format - day can not be over 31"
			exit 1
		fi
	
		if [ "$actualYEAR" -lt "$myYEAR" ]; then

			if [ -z "$actualSTRING" ]; then	
				actualSTRING="$actualFILE"
					else
				actualSTRING=""$actualSTRING" \n"$actualFILE""
			fi
		
		elif [ "$actualYEAR" -eq "$myYEAR" ]; then

			if [ "$actualMONTH" -eq "$myMONTH" ]; then

				if [ "$actualDAY" -lt "$myDAY" ]; then
					
					if [ -z "$actualSTRING" ]; then	
						actualSTRING="$actualFILE"
					else
						actualSTRING=""$actualSTRING" \n"$actualFILE""
					fi
				fi
			
			elif [ "$actualMONTH" -lt "$myMONTH" ]; then
			
				if [ -z "$actualSTRING" ]; then	
					actualSTRING="$actualFILE"
				else
					actualSTRING=""$actualSTRING" \n"$actualFILE""
				fi
			fi

		else
			break
		fi
		
		(( noLINES-- ))
	done	


	if [ ! -z "$actualSTRING" ]; then
		printf "$actualSTRING" | xargs -n1 | sort -u
	else
		echo "No files edited before selected date"  >&2
		exit 1
	fi
}	

### wedi -a [ADRESAR] (NEBO JEN wedi -a)------------------------------

wedi_after()
{	
	i=1	
	noLINES=`grep -c "$myADRESS/[^/]*$" "$WEDI_RC" ` #NumberOfLines
	
	(( noLINES++ ))

	myYEAR=`echo "$myDATE" | cut -f1 -d '-'`
	myMONTH=`echo "$myDATE" | cut -f2 -d '-'`
	myDAY=`echo "$myDATE" | cut -f3 -d '-'`

	while [ $i -lt $noLINES ]; do
		
		actualPATH=`grep "$myADRESS/[^/]*$" "$WEDI_RC" | sed "${i}q ;d" | rev | cut -f2 -d ':' | rev`
		actualDATE=`grep "$myADRESS/[^/]*$" "$WEDI_RC" | sed "${i}q ;d" | cut -f3 -d ':' `
		actualFILE=`basename "$actualPATH"`
	
		actualYEAR=`echo "$actualDATE" | cut -f1 -d '-'`
		actualMONTH=`echo "$actualDATE" | cut -f2 -d '-'`
		actualDAY=`echo "$actualDATE" | cut -f3 -d '-'`

		if [[ "$actualMONTH" > 12 ]] || [[ "$myMONTH" > 12 ]]; then
			echo "Wrong date fotmat - month can not be over 12"
			exit 1
		elif [[ "$actualDAY" > 31 ]] || [[ "$myDAY" > 31 ]]; then
			echo "Wrong date format - day can not be over 31"
			exit 1
		fi
			
	
		if [ "$actualYEAR" -gt "$myYEAR" ]; then

			if [ -z "$actualSTRING" ]; then	
				actualSTRING="$actualFILE"
					else
				actualSTRING=""$actualSTRING" \n"$actualFILE""
			fi
		
		elif [ "$actualYEAR" -eq "$myYEAR" ]; then

			if [ "$actualMONTH" -eq "$myMONTH" ]; then

				if [ "$actualDAY" -ge "$myDAY" ]; then
					
					if [ -z "$actualSTRING" ]; then	
						actualSTRING="$actualFILE"
					else
						actualSTRING=""$actualSTRING" \n"$actualFILE""
					fi
				fi
			
			elif [ "$actualMONTH" -gt "$myMONTH" ]; then
			
				if [ -z "$actualSTRING" ]; then	
					actualSTRING="$actualFILE"
				else
					actualSTRING=""$actualSTRING" \n"$actualFILE""
				fi
			fi

		else
			break
		fi
		
		(( i++ ))
	done	


	if [ ! -z "$actualSTRING" ]; then
		printf "$actualSTRING" | xargs -n1 | sort -u
	else
		echo "No files edited after selected date"  >&2
		exit 1
	fi
}	

### DEFINICE PROMENNYCH ----------------------------------------------

most=false
list=false
after=false
before=false
different=true

myADRESS=$(realpath .)

### NACITANI ARGUMENTU -----------------------------------------------

while getopts ':mlab' option
do	case "$option" in
	m) most=true
	   different=false ;;
	l) list=true
	   different=false ;;
	a) after=true
	   different=false ;;
	b) before=true
	   different=false ;;
	*) different=true;
	esac
done

### TESTOVANI, JESTLI BYL ZADAN NEJAKY ARGUMENT -> wedi [akt ADRESAR]-

if [ "$#" = "0" ]; then
	onlydirec
fi

### TESTOVANI PRVNIHO ARGUMENTU (pokud je soubor nebo adresa) --------

ARG1=$1

if [ ! -z "$ARG1" ] && [ "$different" = true ]; then
	
	if [ -f "$ARG1" ]; then # POKUD JE PRVNI ARGUMENT SOUBOR --> wedi[SOUBOR]
		myFILE=`basename "$ARG1"`
		myADRESS=`realpath "$ARG1"`
		editing

	elif [ -d "$ARG1" ]; then #POKUD JE PRVNI ARGUMENT ADRESAR 
		myADRESS=`realpath "$ARG1"`
		onlydirec
	else
		echo "File or directory does not exist!" >&2
		exit 1
	fi 
fi

### TESTOVANI DRUHEHO ARGUMENTU (pokud je soubor nebo adresa) --------

ARG2=$2

if [ ! -z "$ARG2" ]; then
	
	if [ -d $ARG2 ] && [ "$after" = false ] && [ "$before" = false ]; then 
	# wedi -a|-b musí mít druhý argument DATUM 
		myADRESS=`realpath "$ARG2"`

	elif [ "$most" = true ] || [ "$list" = true ]; then
		echo "Directory does not exist" >&2
		exit 1

	elif [[ $ARG2 =~ ^[0-9]{4}-[0-9]{2}-[0-9]{2}$ ]] ;then		
		myDATE="$ARG2"	
	
	else
		echo "Wrong date format" >&2
		exit 1
	fi
fi

if [ -z "$ARG2" ] && ( [ "$after" = true ] || [ "$before" = true ] ); then 
	echo "With -a or -b selected, second parametr needed in form YYYY-MM-DD" >&2
	exit 1
fi

### TESTOVANI TRETIHO ARGUMENTU (pokud je adresa nebo chyba) --------

ARG3=$3

if [ ! -z "$ARG3" ] && [ -d "$ARG3" ]; then
	myADRESS=`realpath "$ARG3"`

elif [ ! -z "$ARG3" ]; then
	echo "Directory does not exist" >&2
	exit 1
fi

### VOLANI PRISLUSNYCH FUNKCI ---------------------------------------

if [ "$most" = true ]; then
	wedi_most
elif [ "$list" = true ]; then
	wedi_list
elif [ "$before" = true ]; then
	wedi_before
elif [ "$after" = true ]; then
	wedi_after
fi

exit 0
