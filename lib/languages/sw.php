<?php
	
	/* Language: Svenska
	 * Script: Fosen Utvikling AS - fuTelldus
	 * Author: Robert Andresen
	 * Last edited: 02.01.2013
	*/
	
$lang = array(
	
	// Navigation
	"Home" => "Hem",
	"Sensors" => "Sensorer",
	"Chart" => "Grafer",
	"Report" => "Rapport",
	"Lights" => "Ljus",
	"Settings" => "Inställningar",
	"Log out" => "Logga ut",
	"Log in" => "Logga in",
	"Return" => "Gå tillbaka",
  	"Switch to" => "Byt till",
  	"Signed in as" => "Inloggad som",

	"Page settings" => "Sidinställningar",
	"Users" => "Användare",
    "Shared sensors" => "Delade sensorer",
	"Test cron-files" => "Testa cron-filer",
	"View public page" => "Visa allmän sida",
	"View public sensors" => "Visa allmän sensorer",



	// User
	"Usersettings" => "Användarinställningar",
	"Userprofile" => "Användarprofil",
	"My profile" => "Min profil",
	"Not logged in" => "Inte inloggad",
	"Remember me" => "Kom ihåg mig",
	
	

	// Messages
	"Userdata updated" => "Användardata uppdaterad",
	"Old password is wrong" => "Gammalt lösenord är fel",
	"New password does not match" => "Nytt lösenord stämmer inte",
	"User added" => "Användare tillagd",
	"User deleted" => "Användare borttagen",
	"Sensor added to monitoring" => "Sensor tillagd för övervakning",
	"Sensor removed from monitoring" => "Sensor borttagen från övervaking",
	"Wrong timeformat" => "Något är fel med vald tid/datum. Säkerställ att Till-datum är efter Från-datum :-)",
	"Nothing to display" => "Inget att visa",
	"Data saved" => "Data sparat",
	"Deleted" => "Borttaget",
	
	
	// Form
	"Login" => "Logga in",
	"Email" => "E-post",
	"Password" => "Lösenord",
	"Leave field to keep current" => "Lämna lösenord blankt för att behålla nuvarande",
	"User language" => "Språk",
	"Save data" => "Spara data",
	"Create new" => "Skapa nytt",
	"Create user" => "Skapa användare",
	"Page title" => "Titel på sidan",
	"General settings" => "Allmänna inställningar",
	"Delete" => "Ta bort",
	"Are you sure you want to delete" => "Är du säker att du vill ta bort?",
	"Edit" => "Redigera",
	"Date to" => "Datum till",
	"Date from" => "Datum från",
	"Show data" => "Visa data",
	"Jump" => "Steg",
	"Jump description" => "Hoppa över valda nummer av loggad tid och temperatur. Temperaturen loggas var 15:e minut, så ett steg på 4 kommer att visa ett resultat på 1 timme. 4*24=96 för en dag.",
	"XML URL" => "XML URL",
	"Description" => "Beskrivning",
	"Select chart" => "Välj graf",
	"Default chart" => "Använd standard graf",
	"Chart max days" => "Visa graf för max dagar tillbaka i tiden",


	// Telldus
	"Telldus keys" => "Telldus nycklar",
	"Public key" => "Public key",
	"Private key" => "Private key",
	"Token" => "Token",
	"Token secret" => "Token secret",
	"Telldus connection test" => "Telldus anslutningstest",
	"Sync lists everytime" => "Synka listor varje gång",
	"List synced" => "Listan synkroniserad",


	// Temperature & chart
	"Latest readings" => "Senaste avläsningar",
	"Temperature" => "Temperatur",
	"Humidity" => "Fuktighet",
	"Combine charts" => "Kombinerad graf",
	"Split charts" => "Delade grafer",
	"View chart" => "Visa graf",
  	"Temp now" => "Nuvarande temperatur",
	"Set filter" => "Ändra filter",
	"Chose graph" => "Välj graf",
	"Export as" => "Exportera som",


	// Sensors
	"Sensor" => "Sensor",
	"Sensorname" => "Sensornamn",
	"Sensordata" => "Sensordata",
	"Sensor ID" => "Sensor ID",
	"Sensors description" => "<p>Lägg till dina sensorer till cronjob för att logga sensordata i databasen.</p><p>Sensorlistan hämtas med nycklarna som är tillagda under <a href='?page=settings&view=user'>din användarprofil</a>.</p>",
	"Non public" => "Icke offentlig",
	"Public" => "Offentligt",


	// Shared sensors
	"Add shared sensor" => "Lägg till delade sensorer",


	// Schedule
	"Schedule" => "Schema",
	"Notifications" => "Notifikationer",
	"Repeat every" => "Upprepa var",
	"Higher than" => "Högre än",
	"Lower than" => "Lägre än",
	"Send to" => "Skicka till",
	"Send warning" => "Skicka varning",
	"Rule" => "Regel",
	"Last sent" => "Senast skickat",
	"Device action" => "Enhetsåtgärd",
	"No device action" => "Ingen åtgärd",

	// Mail notifications
	"Notification mail low temperature" => "Varning: Temperatur är låg<br /><br />Sensor: %%sensor%%<br />Temperatur är %%value%% &deg;",
	"Notification mail high temperature" => "Varning: Temperatur är hög!<br /><br />Sensor: %%sensor%%<br />Temperatur är %%value%% &deg;",
	"Notification mail low humidity" => "Varning: Fuktighetsnivån är låg!<br /><br />Sensor: %%sensor%%<br />Fuktighetsnivån är %%value%% &deg;",
	"Notification mail high humidity" => "Varning: Fuktighetsnivån är hög!<br /><br />Sensor: %%sensor%%<br />Fuktighetsnivån är %%value%% &deg;",



	// Lights
	"On" => "På",
	"Off" => "Av",
	"Groups" => "Grupper",
	"Devices" => "Enheter",



	// Div
	"Language" => "Språk",
	"New" => "Nytt",
	"Repeat" => "Upprepa",
	"Admin" => "Admin",
	"Total" => "Totalt",
	"Max" => "Max",
	"Min" => "Min",
	"Avrage" => "Medel",
	"Stop" => "Stopp",
	"Data" => "Data",
	"ID" => "ID",
	"Name" => "Namn",
	"Ignored" => "Ignorerad",
	"Client" => "Klient",
	"Client name" => "Klientnamn",
	"Online" => "Online",
	"Editable" => "Redigerbar",
	"Last update" => "Senast uppdaterad",
	"Monitor" => "Övervaka",
	"Protocol" => "Protokoll",
	"Timezone offset" => "Timezon offset",
	"Time" => "Tid",
	"Active" => "Aktiv",
	"Disabled" => "Avaktiverad",
	"Location" => "Plats",
	"Celsius" => "Celsius",
	"Degrees" => "Grader",
	"Type" => "Typ",
	"Value" => "Värde",
	"Cancel" => "Avbryt",
	"Warning" => "Varning",
	"High" => "Hög",
	"Low" => "Låg",
	"Primary" => "Primär",
	"Secondary" => "Sekundär",
	"Now" => "Nu",
	"Action" => "Action",
	"Enter" => "Ange",
	"To" => "Till",

	// send warning IF temperature IS more/less THAN   / FOR sensor ...
	"If" => "Om",
	"Is" => "Är",
	"Than" => "Då",
	"For" => "För",


	// Time (ago)
	"since" => "sedan",
	"secound" => "sekund",
	"minute" => "minut",
	"hour" => "timme",
	"day" => "dag",
	"week" => "vecka",
	"month" => "månad",
	"year" => "år",

	"secounds" => "sekunder",
	"minutes" => "minuter",
	"hours" => "timmar",
	"days" => "dagar",
	"weeks" => "veckor",
	"months" => "månader",
	"years" => "år",
	
	// Rangeselector
	"h" => "h",
	"d" => "d",
	"m" => "m",
	"w" => "v",
	"yr" => "år",
	"All" => "Allt",

);

?>