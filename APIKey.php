<?php
function GetAPIKey()
{
	return '123';
}
function GetLandCode($Land)
{
	$countryCode = '0';
//Übersetzen der Länder in den Ländercode Alpha2
	switch($Land)
	{
		case "Deutschland":
		$countryCode = 'DE';
		break;
		case "Österreich":
		$countryCode = 'AT';
		break;
		case "Belgien":
		$countryCode = 'BE';
		break;
		case "Bulgarien":
		$countryCode = 'BG';
		break;
		case "Kanada":
		$countryCode = 'CA';
		break;
		case "China":
		$countryCode = 'CN';
		break;
		case "Kroatien":
		$countryCode = 'HR';
		break;
		case "Zypern":
		$countryCode = 'CY';
		break;
		case "Tschechien":
		$countryCode = 'CZ';
		break;
		case "Tschechische Republik":
		$countryCode = 'CZ';
		break;
		case "Dänemark":
		$countryCode = 'DK';
		break;
		case "Finnland":
		$countryCode = 'FI';
		break;
		case "Frankreich":
		$countryCode = 'FR';
		break;
		case "Griechenland":
		$countryCode = 'GR';
		break;
		case "Ungarn":
		$countryCode = 'HU';
		break;
		case "Italien":
		$countryCode = 'IT';
		break;
		case "Norwegen":
		$countryCode = 'NO';
		break;
		case "Polen":
		$countryCode = 'PL';
		break;
		case "Portugal":
		$countryCode = 'PT';
		break;
		case "Russland":
		$countryCode = 'RU';
		break;
		case "Spanien":
		$countryCode = 'ES';
		break;
		case "Schweden":
		$countryCode = 'SE';
		break;
		case "Schweiz":
		$countryCode = 'CH';
		break;
		case "Türkei":
		$countryCode = 'TR';
		break;
		default:
		$countryCode = '0';
		break;
	}
}
?>