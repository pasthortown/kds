using System.Data;
using System.Net.Http;
using System.Net.Http.Json;
using System.Text;
using System.Text.Json;
using KDS.Modulos;

namespace KDS.Repositorios
{
    /// <summary>
    /// Conector HTTP para enviar comandas al backend Node.js/PostgreSQL
    /// Reemplaza la conexión a SQL Server para la base KDS
    /// </summary>
    public class ConectorAPI
    {
        private static readonly HttpClient _httpClient = new HttpClient();
        private string _baseUrl = "";
        private string _authToken = "";

        public string BaseUrl
        {
            get => _baseUrl;
            set => _baseUrl = value.TrimEnd('/');
        }

        /// <summary>
        /// Configura la conexión al backend
        /// </summary>
        public void ConfigurarConexion(string baseUrl, string email, string password)
        {
            _baseUrl = baseUrl.TrimEnd('/');

            // Obtener token de autenticación
            try
            {
                ObtenerToken(email, password).Wait();
            }
            catch (Exception ex)
            {
                LogProcesos.Instance.Escribir($"WARN: ConectorAPI - No se pudo obtener token: {ex.Message}");
            }
        }

        /// <summary>
        /// Obtiene token de autenticación del backend
        /// </summary>
        private async Task ObtenerToken(string email, string password)
        {
            var loginData = new { email, password };
            var response = await _httpClient.PostAsJsonAsync($"{_baseUrl}/api/auth/login", loginData);

            if (response.IsSuccessStatusCode)
            {
                var result = await response.Content.ReadFromJsonAsync<LoginResponse>();
                _authToken = result?.accessToken ?? "";
                LogProcesos.Instance.Escribir("INFO: ConectorAPI - Token obtenido exitosamente");
            }
            else
            {
                var error = await response.Content.ReadAsStringAsync();
                throw new Exception($"Error al obtener token: {response.StatusCode} - {error}");
            }
        }

        /// <summary>
        /// Envía una comanda al backend via API
        /// </summary>
        public async Task<ApiResponse> EnviarComanda(ComandaApi comanda)
        {
            try
            {
                var request = new HttpRequestMessage(HttpMethod.Post, $"{_baseUrl}/api/tickets/receive");
                request.Content = new StringContent(
                    JsonSerializer.Serialize(comanda),
                    Encoding.UTF8,
                    "application/json"
                );

                if (!string.IsNullOrEmpty(_authToken))
                {
                    request.Headers.Authorization = new System.Net.Http.Headers.AuthenticationHeaderValue("Bearer", _authToken);
                }

                var response = await _httpClient.SendAsync(request);
                var content = await response.Content.ReadAsStringAsync();

                if (response.IsSuccessStatusCode)
                {
                    LogProcesos.Instance.Escribir($"INFO: ConectorAPI - Comanda {comanda.orderId} enviada exitosamente");
                    return new ApiResponse { Success = true, OrderId = comanda.orderId };
                }
                else
                {
                    LogProcesos.Instance.Escribir($"ERROR: ConectorAPI - Error al enviar comanda: {response.StatusCode} - {content}");
                    return new ApiResponse { Success = false, Error = content };
                }
            }
            catch (Exception ex)
            {
                LogProcesos.Instance.Escribir($"ERROR: ConectorAPI - Excepción al enviar comanda: {ex.Message}");
                return new ApiResponse { Success = false, Error = ex.Message };
            }
        }

        /// <summary>
        /// Envía múltiples comandas al backend via API
        /// </summary>
        public async Task<ApiResponse> EnviarComandasBatch(List<ComandaApi> comandas)
        {
            try
            {
                var request = new HttpRequestMessage(HttpMethod.Post, $"{_baseUrl}/api/tickets/receive-batch");
                request.Content = new StringContent(
                    JsonSerializer.Serialize(new { comandas }),
                    Encoding.UTF8,
                    "application/json"
                );

                if (!string.IsNullOrEmpty(_authToken))
                {
                    request.Headers.Authorization = new System.Net.Http.Headers.AuthenticationHeaderValue("Bearer", _authToken);
                }

                var response = await _httpClient.SendAsync(request);
                var content = await response.Content.ReadAsStringAsync();

                if (response.IsSuccessStatusCode)
                {
                    LogProcesos.Instance.Escribir($"INFO: ConectorAPI - Batch de {comandas.Count} comandas enviado exitosamente");
                    return new ApiResponse { Success = true };
                }
                else
                {
                    LogProcesos.Instance.Escribir($"ERROR: ConectorAPI - Error al enviar batch: {response.StatusCode} - {content}");
                    return new ApiResponse { Success = false, Error = content };
                }
            }
            catch (Exception ex)
            {
                LogProcesos.Instance.Escribir($"ERROR: ConectorAPI - Excepción al enviar batch: {ex.Message}");
                return new ApiResponse { Success = false, Error = ex.Message };
            }
        }

        /// <summary>
        /// Verifica el estado de salud del backend
        /// </summary>
        public async Task<bool> VerificarConexion()
        {
            try
            {
                var response = await _httpClient.GetAsync($"{_baseUrl}/api/config/health");
                return response.IsSuccessStatusCode;
            }
            catch
            {
                return false;
            }
        }
    }

    // Clases de datos para la API

    public class LoginResponse
    {
        public string? accessToken { get; set; }
        public string? refreshToken { get; set; }
    }

    public class ApiResponse
    {
        public bool Success { get; set; }
        public string? OrderId { get; set; }
        public string? Error { get; set; }
    }

    public class ComandaApi
    {
        public string id { get; set; } = "";
        public string orderId { get; set; } = "";
        public string createdAt { get; set; } = "";
        public ChannelApi channel { get; set; } = new ChannelApi();
        public CashRegisterApi cashRegister { get; set; } = new CashRegisterApi();
        public CustomerApi? customer { get; set; }
        public List<ProductApi> products { get; set; } = new List<ProductApi>();
        public OtrosDatosApi? otrosDatos { get; set; }
    }

    public class ChannelApi
    {
        public int id { get; set; }
        public string name { get; set; } = "";
        public string type { get; set; } = "";
    }

    public class CashRegisterApi
    {
        public string cashier { get; set; } = "";
        public string name { get; set; } = "";
    }

    public class CustomerApi
    {
        public string name { get; set; } = "";
    }

    public class ProductApi
    {
        public string? productId { get; set; }
        public string name { get; set; } = "";
        public int amount { get; set; } = 1;
        public string? category { get; set; }
        public List<string>? content { get; set; }
        public List<SubProductApi>? products { get; set; }
    }

    public class SubProductApi
    {
        public string? productId { get; set; }
        public string name { get; set; } = "";
        public int amount { get; set; } = 1;
        public string? category { get; set; }
        public List<string>? content { get; set; }
    }

    public class OtrosDatosApi
    {
        public int? turno { get; set; }
        public string? nroCheque { get; set; }
        public string? llamarPor { get; set; }
        public string? Fecha { get; set; }
        public string? Direccion { get; set; }
    }
}
