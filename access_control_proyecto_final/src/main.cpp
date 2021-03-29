
#include "SPI.h"
#include <Arduino.h>
#include <WiFi.h>
#include <PubSubClient.h>
#include <Adafruit_ILI9341.h>

const String serial_number = "123456789";

#define RXD2 16
#define TXD2 17

TaskHandle_t Task1;

const char* ssid     = "GByP";
const char* password = "qwertyuiop";

//para evitar que el dhcp nos asigne ip, o si el ruter no cuenta con dhcp
//podemos seleccionar una ip fija si no lo usas comentar las 5 líneas
IPAddress local_IP(192, 168, 0, 184);
IPAddress gateway(192, 168, 0, 1);
IPAddress subnet(255, 255, 255, 0);
IPAddress primaryDNS(8, 8, 8, 8);
IPAddress secondaryDNS(8, 8, 4, 4);


//*****************************
//***   CONFIGURACION MQTT  ***
//*****************************

const char *mqtt_server = "cursoiot.ga";
const int mqtt_port = 1883;
const char *mqtt_user = "web_client";
const char *mqtt_pass = "121212";

WiFiClient espClient;
PubSubClient client(espClient);

long lastMsg = 0;
char msg[25];
bool send_access_query = false;

//********************************
//***   CONFIGURACION DISPLAY  ***
//********************************
#define TFT_DC 10
#define TFT_CS  9

//Adafruit_ILI9341 tft = Adafruit_ILI9341(TFT_CS, TFT_DC, TFT_MOSI, TFT_CLK, TFT_RST, TFT_MISO);
Adafruit_ILI9341 tft = Adafruit_ILI9341(    5,      22,      23,      18,      4,        19);

String rfid = "";
String user_name = "";

//*****************************
//*** DECLARACION FUNCIONES ***
//*****************************
void setup_wifi();
void callback(char* topic, byte* payload, unsigned int length);
void reconnect();
void access_screen(bool access);
void closing();
void opening();
void iddle();
void sending();



//*****************************
//***   SENSOR INT TEMP     ***
//*****************************

#ifdef __cplusplus
extern "C" {
	#endif

	uint8_t temprature_sens_read();

	#ifdef __cplusplus
}
#endif

uint8_t temprature_sens_read();

//*****************************
//***   TAREA OTRO NUCLEO   ***
//*****************************

void codeForTask1(void *parameter)
{

	for (;;)
	{
		while (Serial2.available()) {
			rfid += char(Serial2.read());
		}

		if(rfid!=""){
			Serial.println("Se solicita acceso para tarjeta -> " + rfid );
			send_access_query = true;
			delay(1000);

		}

		vTaskDelay(10);
	}
}


void setup() {

	pinMode(BUILTIN_LED, OUTPUT);
	Serial.begin(115200);
	Serial2.begin(9600, SERIAL_8N1, RXD2, TXD2);
	randomSeed(micros());

	xTaskCreatePinnedToCore(
		codeForTask1, /* Task function. */
		"Task_1",	 /* name of task. */
		1000,		  /* Stack size of task */
		NULL,		  /* parameter of the task */
		1,			  /* priority of the task */
		&Task1,		  /* Task handle to keep track of created task */
		0);			  /* Core */

		//Iniciamos display LCD
		tft.begin();
		tft.setRotation(3);
		tft.fillScreen(ILI9341_BLACK);
		iddle();

		setup_wifi();
		client.setServer(mqtt_server, mqtt_port);
		client.setCallback(callback);
	}

	void loop() {
		if (!client.connected()) {
			reconnect();
		}

		client.loop();

		long now = millis();

		if (now - lastMsg > 2000){
			lastMsg = now;
			String to_send = String((temprature_sens_read() - 32) / 1.8);
			to_send.toCharArray(msg, 25);

			char topic[25];
			String topic_aux = serial_number + "/temp";
			topic_aux.toCharArray(topic,25);

			client.publish(topic, msg);
		}

		if (send_access_query == true){

			String to_send = rfid;
			rfid = "";

			sending();
			to_send.toCharArray(msg, 25);

			char topic[25];
			String topic_aux = serial_number + "/access_query";
			topic_aux.toCharArray(topic,25);

			client.publish(topic, msg);

			send_access_query = false;
		}

	}


	//*****************************
	//*** PANTALLAS ACCESO      ***
	//*****************************

	void access_screen(bool access) {

		if (access) {
			tft.fillRect(0, 0, 320, 240, ILI9341_BLACK);
			tft.setTextSize(3);
			tft.setCursor(20, 30);
			tft.setTextColor(ILI9341_GREEN);
			tft.println("Hola " + user_name);
			tft.println("");
			tft.print(" ACCESO PERMITIDO");

			delay(2000);
			opening();
		}else{
			tft.fillRect(0, 0, 320, 240, ILI9341_RED);
			tft.setTextSize(3);
			tft.setCursor(20, 100);
			tft.setTextColor(ILI9341_WHITE);
			tft.print("ACCESO DENEGADO");
			delay(2000);
			iddle();
		}
	}

	void opening() {


		tft.fillRect(0, 0, 320, 240, ILI9341_BLUE);
		tft.setTextSize(3);
		tft.setCursor(30, 100);
		tft.setTextColor(ILI9341_WHITE);
		tft.print("ABRIENDO...");
		delay(2000);
		iddle();

	}

	void closing() {

		tft.fillRect(0, 0, 320, 240, ILI9341_BLACK);
		tft.setTextSize(3);
		tft.setCursor(30, 100);
		tft.setTextColor(ILI9341_WHITE);
		tft.print("CERRANDO...");
		delay(2000);
		iddle();

	}

	void sending() {

		tft.fillRect(0, 0, 320, 240, ILI9341_BLACK);
		tft.setTextSize(3);
		tft.setCursor(30, 100);
		tft.setTextColor(ILI9341_WHITE);
		tft.print("Aguarde");
		delay(300);
		tft.print(".");
		delay(300);
		tft.print(".");
		delay(300);
		tft.print(".");
		delay(300);

	}

	void iddle(){
		digitalWrite(BUILTIN_LED, LOW);
		tft.fillRect(0, 0, 320, 240, ILI9341_BLACK);
		tft.setTextSize(3);
		tft.setCursor(30, 100);
		tft.setTextColor(ILI9341_WHITE);
		tft.print("INGRESE TARJETA");
	}

	//*****************************
	//***    CONEXION WIFI      ***
	//*****************************
	void setup_wifi(){
		delay(10);
		// este if intentará implementar las ip que seleccionamos si no se usa comentar el if completo
		if (!WiFi.config(local_IP, gateway, subnet, primaryDNS, secondaryDNS)) {
			Serial.println("STA Failed to configure");
		}

		// Nos conectamos a nuestra red Wifi
		Serial.println();
		Serial.print("Conectando a ");
		Serial.println(ssid);

		WiFi.begin(ssid, password);

		while (WiFi.status() != WL_CONNECTED) {
			delay(500);
			Serial.print(".");
		}

		Serial.println("");
		Serial.println("Conectado a red WiFi!");
		Serial.println("Dirección IP: ");
		Serial.println(WiFi.localIP());

	}



	void callback(char* topic, byte* payload, unsigned int length){
		String incoming = "";
		Serial.print("Mensaje recibido desde -> ");
		Serial.print(topic);
		Serial.println("");
		for (int i = 0; i < length; i++) {
			incoming += (char)payload[i];
		}
		incoming.trim();
		Serial.println("Mensaje -> " + incoming);

		String str_topic(topic);

		if (str_topic == serial_number + "/command"){

			if ( incoming == "open") {
				digitalWrite(BUILTIN_LED, HIGH);
				opening();
			}

			if ( incoming == "close") {
				digitalWrite(BUILTIN_LED, LOW);
				closing();
			}

			if ( incoming == "granted") {
				digitalWrite(BUILTIN_LED, HIGH);
				access_screen(true);
			}

			if ( incoming == "refused") {
				digitalWrite(BUILTIN_LED, LOW);
				access_screen(false);
			}
		}

		if (str_topic == serial_number + "/user_name"){
			user_name = incoming;
		}



	}

	void reconnect() {

		while (!client.connected()) {
			Serial.print("Intentando conexión Mqtt...");
			// Creamos un cliente ID
			String clientId = "esp32_";
			clientId += String(random(0xffff), HEX);
			// Intentamos conectar
			if (client.connect(clientId.c_str(),mqtt_user,mqtt_pass)) {
				Serial.println("Conectado!");

				// Nos suscribimos a comandos
				char topic[25];
				String topic_aux = serial_number + "/command";
				topic_aux.toCharArray(topic,25);
				client.subscribe(topic);

				// Nos suscribimos a username
				char topic2[25];
				String topic_aux2 = serial_number + "/user_name";
				topic_aux2.toCharArray(topic2,25);
				client.subscribe(topic2);

			} else {
				Serial.print("falló :( con error -> ");
				Serial.print(client.state());
				Serial.println(" Intentamos de nuevo en 5 segundos");

				delay(5000);
			}
		}
	}

	/*

	//CODIGO ANTERIOR

	#include <Arduino.h>
	#include <WiFi.h>
	#include <PubSubClient.h>

	const char* ssid     = "GByP";
	const char* password = "pingpong";

	const char *mqtt_server = "cursoiot.ga";
	const int mqtt_port = 1883;
	const char *mqtt_user = "web_client";
	const char *mqtt_pass = "121212";

	WiFiClient espClient;
	PubSubClient client(espClient);

	long lastMsg = 0;
	char msg[25];

	int temp1 = 0;
	int temp2 = 1;
	int volts = 2;

	//*****************************
	//*** DECLARACION FUNCIONES ***
	//*****************************
	void setup_wifi();
	void callback(char* topic, byte* payload, unsigned int length);
	void reconnect();

	void setup() {
	pinMode(BUILTIN_LED, OUTPUT);
	Serial.begin(115200);
	randomSeed(micros());
	setup_wifi();
	client.setServer(mqtt_server, mqtt_port);
	client.setCallback(callback);
}

void loop() {
if (!client.connected()) {
reconnect();
}

client.loop();

long now = millis();
if (now - lastMsg > 500){
lastMsg = now;
temp1++;
temp2++;
volts++;

String to_send = String(temp1) + "," + String(temp2) + "," + String(volts);
to_send.toCharArray(msg, 25);
Serial.print("Publicamos mensaje -> ");
Serial.println(msg);
client.publish("values", msg);
}
}



//*****************************
//***    CONEXION WIFI      ***
//*****************************
void setup_wifi(){
delay(10);
// Nos conectamos a nuestra red Wifi
Serial.println();
Serial.print("Conectando a ");
Serial.println(ssid);

WiFi.begin(ssid, password);

while (WiFi.status() != WL_CONNECTED) {
delay(500);
Serial.print(".");
}

Serial.println("");
Serial.println("Conectado a red WiFi!");
Serial.println("Dirección IP: ");
Serial.println(WiFi.localIP());
}



void callback(char* topic, byte* payload, unsigned int length){
String incoming = "";
Serial.print("Mensaje recibido desde -> ");
Serial.print(topic);
Serial.println("");
for (int i = 0; i < length; i++) {
incoming += (char)payload[i];
}
incoming.trim();
Serial.println("Mensaje -> " + incoming);

if ( incoming == "on") {
digitalWrite(BUILTIN_LED, HIGH);
} else {
digitalWrite(BUILTIN_LED, LOW);
}
}

void reconnect() {

while (!client.connected()) {
Serial.print("Intentando conexión Mqtt...");
// Creamos un cliente ID
String clientId = "esp32_";
clientId += String(random(0xffff), HEX);
// Intentamos conectar
if (client.connect(clientId.c_str(),mqtt_user,mqtt_pass)) {
Serial.println("Conectado!");
// Nos suscribimos
client.subscribe("led1");
client.subscribe("led2");
} else {
Serial.print("falló :( con error -> ");
Serial.print(client.state());
Serial.println(" Intentamos de nuevo en 5 segundos");

delay(5000);
}
}
}
/**/
