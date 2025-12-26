# Ejemplos de Código de Integración

Este documento contiene ejemplos de código en diferentes lenguajes para integrar sistemas externos con el KDS.

## C# (.NET)

### Cliente HTTP Completo

```csharp
using System;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Text;
using System.Text.Json;
using System.Threading.Tasks;

namespace KdsIntegration
{
    public class KdsClient
    {
        private readonly HttpClient _httpClient;
        private string _baseUrl;
        private string _accessToken;
        private string _refreshToken;
        private string _email;
        private string _password;

        public KdsClient()
        {
            _httpClient = new HttpClient();
            _httpClient.Timeout = TimeSpan.FromSeconds(30);
        }

        /// <summary>
        /// Configura las credenciales y URL del KDS
        /// </summary>
        public void Configurar(string baseUrl, string email, string password)
        {
            _baseUrl = baseUrl.TrimEnd('/');
            _email = email;
            _password = password;
        }

        /// <summary>
        /// Realiza autenticación y obtiene tokens
        /// </summary>
        public async Task<bool> AutenticarAsync()
        {
            try
            {
                var loginData = new { email = _email, password = _password };
                var content = new StringContent(
                    JsonSerializer.Serialize(loginData),
                    Encoding.UTF8,
                    "application/json"
                );

                var response = await _httpClient.PostAsync(
                    $"{_baseUrl}/api/auth/login",
                    content
                );

                if (response.IsSuccessStatusCode)
                {
                    var json = await response.Content.ReadAsStringAsync();
                    var result = JsonSerializer.Deserialize<LoginResponse>(json);

                    _accessToken = result.accessToken;
                    _refreshToken = result.refreshToken;

                    Console.WriteLine($"[KDS] Autenticación exitosa. Rol: {result.user?.role}");
                    return true;
                }
                else
                {
                    Console.WriteLine($"[KDS] Error de autenticación: {response.StatusCode}");
                    return false;
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[KDS] Excepción en autenticación: {ex.Message}");
                return false;
            }
        }

        /// <summary>
        /// Renueva el access token usando el refresh token
        /// </summary>
        public async Task<bool> RenovarTokenAsync()
        {
            try
            {
                var refreshData = new { refreshToken = _refreshToken };
                var content = new StringContent(
                    JsonSerializer.Serialize(refreshData),
                    Encoding.UTF8,
                    "application/json"
                );

                var response = await _httpClient.PostAsync(
                    $"{_baseUrl}/api/auth/refresh",
                    content
                );

                if (response.IsSuccessStatusCode)
                {
                    var json = await response.Content.ReadAsStringAsync();
                    var result = JsonSerializer.Deserialize<RefreshResponse>(json);

                    _accessToken = result.accessToken;
                    Console.WriteLine("[KDS] Token renovado exitosamente");
                    return true;
                }
                else
                {
                    Console.WriteLine($"[KDS] Error renovando token: {response.StatusCode}");
                    return false;
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[KDS] Excepción renovando token: {ex.Message}");
                return false;
            }
        }

        /// <summary>
        /// Envía una comanda al KDS
        /// </summary>
        public async Task<EnvioResult> EnviarComandaAsync(ApiComanda comanda)
        {
            return await EnviarConReintentosAsync(comanda, 3);
        }

        private async Task<EnvioResult> EnviarConReintentosAsync(ApiComanda comanda, int maxReintentos)
        {
            int intento = 0;

            while (intento < maxReintentos)
            {
                intento++;

                try
                {
                    var request = new HttpRequestMessage(HttpMethod.Post,
                        $"{_baseUrl}/api/tickets/receive");

                    request.Content = new StringContent(
                        JsonSerializer.Serialize(comanda),
                        Encoding.UTF8,
                        "application/json"
                    );

                    // Agregar token de autorización
                    if (!string.IsNullOrEmpty(_accessToken))
                    {
                        request.Headers.Authorization =
                            new AuthenticationHeaderValue("Bearer", _accessToken);
                    }

                    var response = await _httpClient.SendAsync(request);
                    var responseContent = await response.Content.ReadAsStringAsync();

                    if (response.IsSuccessStatusCode)
                    {
                        Console.WriteLine($"[KDS] Comanda {comanda.orderId} enviada exitosamente");
                        return new EnvioResult { Success = true, OrderId = comanda.orderId };
                    }

                    // Token expirado - renovar y reintentar
                    if (response.StatusCode == System.Net.HttpStatusCode.Unauthorized)
                    {
                        Console.WriteLine("[KDS] Token expirado, renovando...");

                        if (await RenovarTokenAsync())
                        {
                            continue; // Reintentar con nuevo token
                        }

                        // Si renovación falla, intentar login completo
                        if (await AutenticarAsync())
                        {
                            continue;
                        }

                        return new EnvioResult
                        {
                            Success = false,
                            Error = "No se pudo renovar autenticación"
                        };
                    }

                    // Error del servidor - reintentar
                    if ((int)response.StatusCode >= 500)
                    {
                        Console.WriteLine($"[KDS] Error del servidor ({response.StatusCode}), reintentando...");
                        await Task.Delay(1000 * intento); // Backoff exponencial
                        continue;
                    }

                    // Error del cliente - no reintentar
                    Console.WriteLine($"[KDS] Error del cliente: {response.StatusCode} - {responseContent}");
                    return new EnvioResult { Success = false, Error = responseContent };
                }
                catch (TaskCanceledException)
                {
                    Console.WriteLine("[KDS] Timeout, reintentando...");
                    await Task.Delay(1000 * intento);
                }
                catch (HttpRequestException ex)
                {
                    Console.WriteLine($"[KDS] Error de red: {ex.Message}");
                    await Task.Delay(1000 * intento);
                }
            }

            return new EnvioResult
            {
                Success = false,
                Error = "Máximo de reintentos alcanzado"
            };
        }
    }

    // DTOs
    public class ApiComanda
    {
        public string id { get; set; }
        public string orderId { get; set; }
        public string createdAt { get; set; }
        public Channel channel { get; set; }
        public CashRegister cashRegister { get; set; }
        public Customer customer { get; set; }
        public List<Product> products { get; set; }
        public OtrosDatos otrosDatos { get; set; }
        public string comments { get; set; }
        public string statusPos { get; set; }
    }

    public class Channel
    {
        public int id { get; set; }
        public string name { get; set; }
        public string type { get; set; }
    }

    public class CashRegister
    {
        public string cashier { get; set; }
        public string name { get; set; }
    }

    public class Customer
    {
        public string name { get; set; }
    }

    public class Product
    {
        public string productId { get; set; }
        public string name { get; set; }
        public int amount { get; set; } = 1;
        public string category { get; set; }
        public List<string> content { get; set; }
        public string modifier { get; set; }
        public string comments { get; set; }
    }

    public class OtrosDatos
    {
        public int? turno { get; set; }
        public string nroCheque { get; set; }
        public string llamarPor { get; set; }
    }

    public class LoginResponse
    {
        public string accessToken { get; set; }
        public string refreshToken { get; set; }
        public UserInfo user { get; set; }
    }

    public class UserInfo
    {
        public string userId { get; set; }
        public string email { get; set; }
        public string role { get; set; }
    }

    public class RefreshResponse
    {
        public string accessToken { get; set; }
    }

    public class EnvioResult
    {
        public bool Success { get; set; }
        public string OrderId { get; set; }
        public string Error { get; set; }
    }
}
```

### Ejemplo de Uso en C#

```csharp
// Ejemplo de uso
class Program
{
    static async Task Main(string[] args)
    {
        var kdsClient = new KdsClient();

        // Configurar conexión
        kdsClient.Configurar(
            "https://kds.empresa.com",
            "operador@empresa.com",
            "clave_segura"
        );

        // Autenticar
        if (!await kdsClient.AutenticarAsync())
        {
            Console.WriteLine("No se pudo conectar al KDS");
            return;
        }

        // Crear comanda de ejemplo
        var comanda = new ApiComanda
        {
            id = "ORD-2025-001234",
            orderId = "ORD-2025-001234",
            createdAt = DateTime.UtcNow.ToString("o"),
            channel = new Channel
            {
                id = 1,
                name = "Kiosko-Efectivo",
                type = "LLEVAR"
            },
            cashRegister = new CashRegister
            {
                cashier = "CAJ001",
                name = "Caja Principal"
            },
            customer = new Customer
            {
                name = "Juan Pérez"
            },
            products = new List<Product>
            {
                new Product
                {
                    productId = "PROD-001",
                    name = "Combo Familiar 15pcs",
                    amount = 1,
                    content = new List<string> { "*8 ORIGINAL", "*7 CRISPY" }
                },
                new Product
                {
                    name = "Papas Grandes",
                    amount = 2,
                    content = new List<string> { "*SIN SAL" }
                }
            },
            otrosDatos = new OtrosDatos
            {
                turno = 145,
                nroCheque = "FAC-001234"
            },
            statusPos = "PEDIDO TOMADO"
        };

        // Enviar comanda
        var resultado = await kdsClient.EnviarComandaAsync(comanda);

        if (resultado.Success)
        {
            Console.WriteLine($"Comanda enviada exitosamente: {resultado.OrderId}");
        }
        else
        {
            Console.WriteLine($"Error: {resultado.Error}");
        }
    }
}
```

## PHP

### Cliente KDS en PHP

```php
<?php

class KdsClient
{
    private $baseUrl;
    private $accessToken;
    private $refreshToken;
    private $email;
    private $password;

    /**
     * Configura las credenciales y URL del KDS
     */
    public function configurar($baseUrl, $email, $password)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Realiza autenticación y obtiene tokens
     */
    public function autenticar()
    {
        $url = $this->baseUrl . '/api/auth/login';

        $data = json_encode([
            'email' => $this->email,
            'password' => $this->password
        ]);

        $response = $this->realizarPeticion('POST', $url, $data, false);

        if ($response['success']) {
            $result = json_decode($response['body'], true);
            $this->accessToken = $result['accessToken'];
            $this->refreshToken = $result['refreshToken'];
            return true;
        }

        return false;
    }

    /**
     * Renueva el access token
     */
    public function renovarToken()
    {
        $url = $this->baseUrl . '/api/auth/refresh';

        $data = json_encode([
            'refreshToken' => $this->refreshToken
        ]);

        $response = $this->realizarPeticion('POST', $url, $data, false);

        if ($response['success']) {
            $result = json_decode($response['body'], true);
            $this->accessToken = $result['accessToken'];
            return true;
        }

        return false;
    }

    /**
     * Envía una comanda al KDS
     */
    public function enviarComanda($comanda)
    {
        $url = $this->baseUrl . '/api/tickets/receive';
        $data = json_encode($comanda);

        $response = $this->realizarPeticion('POST', $url, $data, true);

        // Si token expirado, renovar y reintentar
        if ($response['status'] == 401) {
            if ($this->renovarToken()) {
                $response = $this->realizarPeticion('POST', $url, $data, true);
            } elseif ($this->autenticar()) {
                $response = $this->realizarPeticion('POST', $url, $data, true);
            }
        }

        return [
            'success' => $response['success'],
            'orderId' => $comanda['orderId'],
            'error' => $response['success'] ? null : $response['body']
        ];
    }

    /**
     * Realiza una petición HTTP
     */
    private function realizarPeticion($method, $url, $data, $useAuth)
    {
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        if ($useAuth && $this->accessToken) {
            $headers[] = 'Authorization: Bearer ' . $this->accessToken;
        }

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        return [
            'success' => $status >= 200 && $status < 300,
            'status' => $status,
            'body' => $body,
            'error' => $error
        ];
    }
}

// =====================================
// EJEMPLO DE USO
// =====================================

// Crear cliente
$kds = new KdsClient();

// Configurar
$kds->configurar(
    'https://kds.empresa.com',
    'operador@empresa.com',
    'clave_segura'
);

// Autenticar
if (!$kds->autenticar()) {
    die('Error de autenticación');
}

// Crear comanda
$comanda = [
    'id' => 'ORD-2025-001234',
    'orderId' => 'ORD-2025-001234',
    'createdAt' => date('c'), // ISO 8601
    'channel' => [
        'id' => 1,
        'name' => 'Kiosko-Efectivo',
        'type' => 'LLEVAR'
    ],
    'cashRegister' => [
        'cashier' => 'CAJ001',
        'name' => 'Caja Principal'
    ],
    'customer' => [
        'name' => 'Juan Pérez'
    ],
    'products' => [
        [
            'productId' => 'PROD-001',
            'name' => 'Combo Familiar 15pcs',
            'amount' => 1,
            'content' => ['*8 ORIGINAL', '*7 CRISPY']
        ],
        [
            'name' => 'Papas Grandes',
            'amount' => 2,
            'content' => ['*SIN SAL']
        ]
    ],
    'otrosDatos' => [
        'turno' => 145,
        'nroCheque' => 'FAC-001234'
    ],
    'statusPos' => 'PEDIDO TOMADO'
];

// Enviar
$resultado = $kds->enviarComanda($comanda);

if ($resultado['success']) {
    echo "Comanda enviada: " . $resultado['orderId'];
} else {
    echo "Error: " . $resultado['error'];
}
```

## JavaScript / Node.js

### Cliente KDS en JavaScript

```javascript
const axios = require('axios');

class KdsClient {
    constructor() {
        this.baseUrl = '';
        this.accessToken = '';
        this.refreshToken = '';
        this.email = '';
        this.password = '';
    }

    /**
     * Configura las credenciales y URL del KDS
     */
    configurar(baseUrl, email, password) {
        this.baseUrl = baseUrl.replace(/\/$/, '');
        this.email = email;
        this.password = password;
    }

    /**
     * Realiza autenticación y obtiene tokens
     */
    async autenticar() {
        try {
            const response = await axios.post(`${this.baseUrl}/api/auth/login`, {
                email: this.email,
                password: this.password
            });

            this.accessToken = response.data.accessToken;
            this.refreshToken = response.data.refreshToken;

            console.log(`[KDS] Autenticación exitosa. Rol: ${response.data.user?.role}`);
            return true;
        } catch (error) {
            console.error('[KDS] Error de autenticación:', error.message);
            return false;
        }
    }

    /**
     * Renueva el access token
     */
    async renovarToken() {
        try {
            const response = await axios.post(`${this.baseUrl}/api/auth/refresh`, {
                refreshToken: this.refreshToken
            });

            this.accessToken = response.data.accessToken;
            console.log('[KDS] Token renovado exitosamente');
            return true;
        } catch (error) {
            console.error('[KDS] Error renovando token:', error.message);
            return false;
        }
    }

    /**
     * Envía una comanda al KDS
     */
    async enviarComanda(comanda) {
        const maxReintentos = 3;

        for (let intento = 1; intento <= maxReintentos; intento++) {
            try {
                const response = await axios.post(
                    `${this.baseUrl}/api/tickets/receive`,
                    comanda,
                    {
                        headers: {
                            'Authorization': `Bearer ${this.accessToken}`,
                            'Content-Type': 'application/json'
                        },
                        timeout: 30000
                    }
                );

                console.log(`[KDS] Comanda ${comanda.orderId} enviada exitosamente`);
                return { success: true, orderId: comanda.orderId };

            } catch (error) {
                // Token expirado
                if (error.response?.status === 401) {
                    console.log('[KDS] Token expirado, renovando...');

                    if (await this.renovarToken()) {
                        continue;
                    }

                    if (await this.autenticar()) {
                        continue;
                    }

                    return { success: false, error: 'No se pudo renovar autenticación' };
                }

                // Error del servidor
                if (error.response?.status >= 500) {
                    console.log(`[KDS] Error del servidor, reintentando (${intento}/${maxReintentos})...`);
                    await this.sleep(1000 * intento);
                    continue;
                }

                // Error del cliente o de red
                console.error('[KDS] Error:', error.message);
                return {
                    success: false,
                    error: error.response?.data || error.message
                };
            }
        }

        return { success: false, error: 'Máximo de reintentos alcanzado' };
    }

    sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}

// =====================================
// EJEMPLO DE USO
// =====================================

async function main() {
    const kds = new KdsClient();

    // Configurar
    kds.configurar(
        'https://kds.empresa.com',
        'operador@empresa.com',
        'clave_segura'
    );

    // Autenticar
    if (!await kds.autenticar()) {
        console.log('No se pudo conectar al KDS');
        return;
    }

    // Crear comanda
    const comanda = {
        id: 'ORD-2025-001234',
        orderId: 'ORD-2025-001234',
        createdAt: new Date().toISOString(),
        channel: {
            id: 1,
            name: 'Kiosko-Efectivo',
            type: 'LLEVAR'
        },
        cashRegister: {
            cashier: 'CAJ001',
            name: 'Caja Principal'
        },
        customer: {
            name: 'Juan Pérez'
        },
        products: [
            {
                productId: 'PROD-001',
                name: 'Combo Familiar 15pcs',
                amount: 1,
                content: ['*8 ORIGINAL', '*7 CRISPY']
            },
            {
                name: 'Papas Grandes',
                amount: 2,
                content: ['*SIN SAL']
            }
        ],
        otrosDatos: {
            turno: 145,
            nroCheque: 'FAC-001234'
        },
        statusPos: 'PEDIDO TOMADO'
    };

    // Enviar
    const resultado = await kds.enviarComanda(comanda);

    if (resultado.success) {
        console.log(`Comanda enviada exitosamente: ${resultado.orderId}`);
    } else {
        console.log(`Error: ${resultado.error}`);
    }
}

main();
```

## Python

### Cliente KDS en Python

```python
import requests
import json
from datetime import datetime
from typing import Optional, Dict, Any, List
import time

class KdsClient:
    def __init__(self):
        self.base_url = ""
        self.access_token = ""
        self.refresh_token = ""
        self.email = ""
        self.password = ""
        self.session = requests.Session()
        self.session.timeout = 30

    def configurar(self, base_url: str, email: str, password: str):
        """Configura las credenciales y URL del KDS"""
        self.base_url = base_url.rstrip('/')
        self.email = email
        self.password = password

    def autenticar(self) -> bool:
        """Realiza autenticación y obtiene tokens"""
        try:
            response = self.session.post(
                f"{self.base_url}/api/auth/login",
                json={"email": self.email, "password": self.password}
            )

            if response.ok:
                data = response.json()
                self.access_token = data["accessToken"]
                self.refresh_token = data["refreshToken"]
                print(f"[KDS] Autenticación exitosa. Rol: {data.get('user', {}).get('role')}")
                return True
            else:
                print(f"[KDS] Error de autenticación: {response.status_code}")
                return False

        except Exception as e:
            print(f"[KDS] Excepción en autenticación: {e}")
            return False

    def renovar_token(self) -> bool:
        """Renueva el access token"""
        try:
            response = self.session.post(
                f"{self.base_url}/api/auth/refresh",
                json={"refreshToken": self.refresh_token}
            )

            if response.ok:
                data = response.json()
                self.access_token = data["accessToken"]
                print("[KDS] Token renovado exitosamente")
                return True
            else:
                print(f"[KDS] Error renovando token: {response.status_code}")
                return False

        except Exception as e:
            print(f"[KDS] Excepción renovando token: {e}")
            return False

    def enviar_comanda(self, comanda: Dict[str, Any]) -> Dict[str, Any]:
        """Envía una comanda al KDS"""
        max_reintentos = 3

        for intento in range(1, max_reintentos + 1):
            try:
                headers = {
                    "Authorization": f"Bearer {self.access_token}",
                    "Content-Type": "application/json"
                }

                response = self.session.post(
                    f"{self.base_url}/api/tickets/receive",
                    json=comanda,
                    headers=headers
                )

                if response.ok:
                    print(f"[KDS] Comanda {comanda['orderId']} enviada exitosamente")
                    return {"success": True, "orderId": comanda["orderId"]}

                # Token expirado
                if response.status_code == 401:
                    print("[KDS] Token expirado, renovando...")

                    if self.renovar_token():
                        continue

                    if self.autenticar():
                        continue

                    return {"success": False, "error": "No se pudo renovar autenticación"}

                # Error del servidor
                if response.status_code >= 500:
                    print(f"[KDS] Error del servidor, reintentando ({intento}/{max_reintentos})...")
                    time.sleep(intento)
                    continue

                # Error del cliente
                print(f"[KDS] Error del cliente: {response.status_code}")
                return {"success": False, "error": response.text}

            except requests.Timeout:
                print(f"[KDS] Timeout, reintentando ({intento}/{max_reintentos})...")
                time.sleep(intento)

            except requests.RequestException as e:
                print(f"[KDS] Error de red: {e}")
                time.sleep(intento)

        return {"success": False, "error": "Máximo de reintentos alcanzado"}


# =====================================
# EJEMPLO DE USO
# =====================================

if __name__ == "__main__":
    kds = KdsClient()

    # Configurar
    kds.configurar(
        "https://kds.empresa.com",
        "operador@empresa.com",
        "clave_segura"
    )

    # Autenticar
    if not kds.autenticar():
        print("No se pudo conectar al KDS")
        exit(1)

    # Crear comanda
    comanda = {
        "id": "ORD-2025-001234",
        "orderId": "ORD-2025-001234",
        "createdAt": datetime.utcnow().isoformat() + "Z",
        "channel": {
            "id": 1,
            "name": "Kiosko-Efectivo",
            "type": "LLEVAR"
        },
        "cashRegister": {
            "cashier": "CAJ001",
            "name": "Caja Principal"
        },
        "customer": {
            "name": "Juan Pérez"
        },
        "products": [
            {
                "productId": "PROD-001",
                "name": "Combo Familiar 15pcs",
                "amount": 1,
                "content": ["*8 ORIGINAL", "*7 CRISPY"]
            },
            {
                "name": "Papas Grandes",
                "amount": 2,
                "content": ["*SIN SAL"]
            }
        ],
        "otrosDatos": {
            "turno": 145,
            "nroCheque": "FAC-001234"
        },
        "statusPos": "PEDIDO TOMADO"
    }

    # Enviar
    resultado = kds.enviar_comanda(comanda)

    if resultado["success"]:
        print(f"Comanda enviada exitosamente: {resultado['orderId']}")
    else:
        print(f"Error: {resultado['error']}")
```

## cURL (Línea de Comandos)

### Autenticación

```bash
# Login
curl -X POST https://kds.empresa.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "operador@empresa.com",
    "password": "clave_segura"
  }'

# Respuesta:
# {
#   "accessToken": "eyJhbGciOiJIUzI1NiIs...",
#   "refreshToken": "eyJhbGciOiJIUzI1NiIs...",
#   "user": { "userId": "...", "email": "...", "role": "OPERATOR" }
# }
```

### Renovar Token

```bash
curl -X POST https://kds.empresa.com/api/auth/refresh \
  -H "Content-Type: application/json" \
  -d '{
    "refreshToken": "eyJhbGciOiJIUzI1NiIs..."
  }'
```

### Enviar Comanda

```bash
# Guardar token en variable
TOKEN="eyJhbGciOiJIUzI1NiIs..."

# Enviar comanda
curl -X POST https://kds.empresa.com/api/tickets/receive \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "id": "ORD-2025-001234",
    "orderId": "ORD-2025-001234",
    "createdAt": "2025-01-15T14:30:00.000Z",
    "channel": {
      "id": 1,
      "name": "Kiosko-Efectivo",
      "type": "LLEVAR"
    },
    "cashRegister": {
      "cashier": "CAJ001",
      "name": "Caja Principal"
    },
    "products": [
      {
        "name": "Combo Familiar 15pcs",
        "amount": 1,
        "content": ["*8 ORIGINAL", "*7 CRISPY"]
      }
    ],
    "otrosDatos": {
      "turno": 145
    }
  }'
```

### Verificar Estado del Sistema

```bash
curl -X GET https://kds.empresa.com/api/config/health
```

## Notas Importantes

1. **Manejo de Errores**: Siempre implementar manejo robusto de errores y reintentos.

2. **Almacenamiento de Tokens**:
   - Access token: puede estar solo en memoria
   - Refresh token: guardar de forma segura (no en texto plano)

3. **HTTPS**: Siempre usar HTTPS en producción.

4. **Timeouts**: Configurar timeouts razonables (15-30 segundos).

5. **Logging**: Registrar envíos exitosos y fallidos para diagnóstico.

6. **Cola de Pendientes**: Implementar cola local para comandas que no pudieron enviarse.
