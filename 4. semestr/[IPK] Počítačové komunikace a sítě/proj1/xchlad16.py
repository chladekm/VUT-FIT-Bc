#!/usr/bin/env python3

#################################### MODULY ################################
import argparse
import socket
import json
import sys

IP_ADRESS = 'api.openweathermap.org'
PORT = 80

def main():

	######################### ZPRACOVANI ARGUMENTU ##########################

	parser = argparse.ArgumentParser()
	parser.add_argument("key")
	parser.add_argument("city")

	args = parser.parse_args()

	api_key = args.key
	city = args.city


	######################## KOMUNIKACE SE SERVEREM #########################

	clientSocket = socket.socket(socket.AF_INET, socket.SOCK_STREAM); 

	clientSocket.connect((IP_ADRESS, PORT))

	requestString = "GET /data/2.5/weather?APPID=" + api_key + "&q=" + city + "&units=metric HTTP/1.1\r\nHost: api.openweathermap.org\r\n\r\n"

	clientSocket.sendall(requestString.encode('utf-8'))

	receivedData = clientSocket.recv(1024) 


	########################### ZPRACOVANI DAT ##############################

	## Prevedeni prijatych dat na string
	receivedData = repr(receivedData)

	## Odebrani ' z konce stringu
	receivedData = receivedData[:-1]

	## Z odpovedi vystrihnout uzitecne informace pro JSON
	receivedData = receivedData[receivedData.find("{"):]

	## Prevedeni na format JSON
	jsonData = json.loads(receivedData)
	

	###################### PARSOVANI ZPRAVY & VYPIS #########################

	## Mesto
	try:
		json_city = jsonData['name']
		print(city)
	except:
		print("Cannot reach informations about weather in this city", file=sys.stderr)
		print("The API key or name of the city is incorrect", file=sys.stderr)
		exit()

	## Oblacnost
	try:
		json_cloudiness = jsonData['weather'][0]['description']
		print(json_cloudiness)
	except:
		print("Description about cloudiness is not available")
	
	## Teplota
	try:
		json_temperature = jsonData['main']['temp']
		degreeChar = u"\N{DEGREE SIGN}" + "C"
		print("temp:",json_temperature,degreeChar,sep='')
	except:
		print("temp: not available")

	## Vlhkost
	try:
		json_humidity = jsonData['main']['humidity']
		percentageChar = u"\u0025"
		print("humidity:",json_humidity,percentageChar,sep='')
	except:
		print("pressure: not available")

	## Atmosfericky tlak
	try:
		json_pressure = jsonData['main']['pressure']
		print("pressure:",json_pressure," hPa",sep='')
	except:
		print("pressure: not available")

	## Rychlost vetru
	try:
		json_wind_speed = jsonData['wind']['speed']
		json_wind_speed = json_wind_speed * 3.6;
		json_wind_speed = round(json_wind_speed,2)
		print("wind-speed:",json_wind_speed,"km/h",sep='')
	except:
		print("wind-speed: not available")

	## Smer vetru
	try:
		json_wind_deg = jsonData['wind']['deg']
		print("wind-deg:",json_wind_deg)
	except:
		print("wind-deg: not available")


## Zavolani funkce main
if __name__ == "__main__":
    main()

