#include <WiFi.h>
#include <WebSocketsClient.h>
#include <DHT.h>

// Credenciales de la red WiFi
const char* ssid = "OPPO de Dante";
const char* password = "NETSHWLANSHOWPROFILE";

// URL del WebSocket y detalles
const char* websocket_server = "sgi.castelancarpinteyro.com";
const uint16_t websocket_port = 443;
const char* websocket_path = "/";
const char* websocket_protocol = "arduino";

// Pin del sensor DHT11
#define DHTPIN 4
#define DHTTYPE DHT11

DHT dht(DHTPIN, DHTTYPE);
WebSocketsClient webSocket;

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi");

  dht.begin();

  // Configurar WebSocket con los parámetros necesarios
  webSocket.beginSSL(websocket_server, websocket_port, websocket_path, "", websocket_protocol);
  webSocket.onEvent(webSocketEvent);
}

void loop() {
  webSocket.loop();

  float humidity = dht.readHumidity();
  if (isnan(humidity)) {
    Serial.println("Failed to read from DHT sensor!");
    return;
  }

  String payload = "{\"humidity\": " + String(humidity) + "}";
  webSocket.sendTXT(payload);

  delay(5000); // Enviar cada 5 segundos
}

void webSocketEvent(WStype_t type, uint8_t * payload, size_t length) {
  switch(type) {
    case WStype_DISCONNECTED:
      Serial.println("WebSocket Disconnected!");
      break;
    case WStype_CONNECTED:
      Serial.println("WebSocket Connected!");
      break;
    case WStype_TEXT:
      Serial.printf("Received text: %s\n", payload);
      break;
  }
}