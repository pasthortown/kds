using KDS.Entidades;
using KDS.Interfaces;
using KDS.Modulos;
using KDS.Repositorios;
using Microsoft.AspNetCore.Routing.Tree;
using System.Data;
using System.Diagnostics;
using System.Net.NetworkInformation;
using System.Reflection;
using System.Text.Json;
using static System.Net.Mime.MediaTypeNames;

namespace KDS
{
    public class Program
    {
        public static void Main(string[] args)
        {

            //pruebas();
            //return;

            int reintentos = 3;
            int i = 1;
            //con "u" es formado aaaa-mm-dd hora.... son substring me quedo con la fecha.
            string rutaLog = Path.Combine(Path.GetDirectoryName(System.Reflection.Assembly.GetExecutingAssembly().Location), "logs", $"log {DateTime.Now.Date.ToString("u").Substring(0, 10)}.txt");
            //LogProcesos log = new LogProcesos();
            Configuracion configuracion = new Configuracion();

            try
            {
               
                string rutaArchivoConfig = Path.Combine(Path.GetDirectoryName(System.Reflection.Assembly.GetExecutingAssembly().Location), "config.txt");

                ConfigReader config = new ConfigReader();
                while (i <= reintentos)
                {
                    try
                    {
                        configuracion = config.leerArchivo(rutaArchivoConfig);

                        // Solo leer de base KDS si no hay ConexionBackend configurada
                        if (configuracion.ConexionBackend?.url == null || string.IsNullOrEmpty(configuracion.ConexionBackend.url))
                        {
                            ConfigMaker.Instance.leerBaseDatos(configuracion.ConexionKDS);
                        }
                        else
                        {
                            Console.WriteLine($"KDS2 - Usando ConexionBackend: {configuracion.ConexionBackend.url}");
                            LogProcesos.Instance.Escribir($"INFO: Usando ConexionBackend en lugar de ConexionKDS");
                        }
                        i = reintentos + 1;
                    }
                    catch (Exception ex)
                    {
                        if (i == reintentos)
                        {
                            LogProcesos.Instance.Escribir($"ERROR: Se finaliza el programa");
                            Console.WriteLine($"KDS2 - Error al conectar con base KDS2");
                            return;
                        }
                        else
                        {
                            Console.WriteLine($"ERROR: No se puede inicializar configuración, intento {i} - {ex.Message}");
                            LogProcesos.Instance.Escribir($"ERROR: No se puede inicializar configuración, intento {i} - {ex.Message}");
                        }

                    }
                    i++;

                }
               
                
                LogProcesos.Instance.Escribir("INFO: Archivo config le�do y registrado");
                LogProcesos.Instance.Escribir("INFO: Configuraci�n le�da");
                LogProcesos.Instance.Escribir("INFO: Pantallas esp�as analizadas");
                Assembly assembly = Assembly.GetExecutingAssembly();
                FileVersionInfo fileVersionInfo = FileVersionInfo.GetVersionInfo(assembly.Location);
                LogProcesos.Instance.Escribir($"INFO: Versi�n: {fileVersionInfo.FileVersion}"); ;
                Console.WriteLine($"KDS2 - Versi�n {fileVersionInfo.FileVersion}");
                ConfigMaker.Instance.MostrarDatos();

                JsonMaker jsonMaker = new JsonMaker();
                jsonMaker.conexionKDS = configuracion.ConexionKDS;
                jsonMaker.conexionMXP = configuracion.ConexionMXP;
                jsonMaker.conexionBackend = configuracion.ConexionBackend;
                jsonMaker.rutaLog = rutaLog;
                //jsonMaker.rutaArchivos = configuracion.Comandas.ruta;
                //Si lee archivos o no. True o False
                //jsonMaker.porArchivo = configuracion.Comandas.porArchivo;


                //Proceso de control de estado de pantallas
                ScreenChecker.Instance.listaPantallas = new List<Pantalla>();
                foreach (Pantalla item in ConfigMaker.Instance.listarPantallas())
                {

                    ScreenChecker.Instance.listaPantallas.Add(new Pantalla { activa = false, 
                        cola = item.cola, 
                        impresoraIP = item.impresoraIP, 
                        impresoraNombre = item.impresoraNombre, 
                        imprime = item.imprime, 
                        ip = item.ip, 
                        tiempoActiva = item.tiempoActiva,
                        nombre = item.nombre,
                        filtros = item.filtros,
                        cantidad = item.cantidad,
                        propiedades = item.propiedades});
                }

                //El sistema interpreta que todas las colas tienen comandas asignadas, pero no hay pantallas activas.
                ScreenChecker.Instance.colasProblemas = new Dictionary<string, bool>();

                foreach (Cola cola in ConfigMaker.Instance.listarColas())
                {
                    ScreenChecker.Instance.colasProblemas.Add(cola.nombre, true);
                }
                //ScreenChecker.Instance.listaPantallas = ConfigMaker.Instance.listarPantallas();

                //Creo hilo para leer archivos
                LogProcesos.Instance.Escribir("INFO: Iniciando lector de comandas");
                Thread lector = new Thread(new ThreadStart(jsonMaker.LeerComandas));
                lector.Start();

                //Creo hilo para actualizar el estado de pantallas
                //Si estuvieron cierto tiempo sin recibir solicitudes, se desactivan.
                LogProcesos.Instance.Escribir("INFO: Iniciando ScreenChecker");
                Thread validadorPantallas = new Thread(new ThreadStart(ScreenChecker.Instance.Desactivar));
                validadorPantallas.Start();
            }
            catch (Exception ex)
            {
                Console.WriteLine($"{DateTime.Now}: {ex.Message}");
                LogProcesos.Instance.Escribir(ex.Message);
            }

            

            //ConfigMaker configMaker = new ConfigMaker();
            //configMaker.leerBaseDatos(conexionKDS);
            //string configLeido = JsonSerializer.Serialize(configMaker.configVisible);
            //Console.WriteLine(configLeido);
            
                       
            //Thread lector = new Thread(new ThreadStart(jsonMaker.LeerComandas));
            //lector.Start();

            //Configuro ruta de log y de config

            var builder = WebApplication.CreateBuilder(args);
 
            builder.Services.AddCors(p => p.AddPolicy("corspolicy", build =>
            {
                build.WithOrigins("*").AllowAnyMethod().AllowAnyHeader();
            }));

            // Add services to the container.

            builder.Services.AddControllers();

            var app = builder.Build();

            app.UseCors("corspolicy");
            // Configure the HTTP request pipeline.

            app.UseAuthorization();


            app.MapControllers();

            app.Run();
        }

        static void pruebas()
        {
            //-----------------------------

            //Impresion impresion = new Impresion();
            //impresion.ImprimirComandaDetalle("CFC62EB0-8C43-EE11-BA75-9457A5B4FF49", "192.168.60.95", 9100, 40);
            //return;
            //-----------------------------
            string rutaLog = Path.Combine(Path.GetDirectoryName(System.Reflection.Assembly.GetExecutingAssembly().Location), "logs", $"log {DateTime.Now.Date.ToString("u").Substring(0, 10)}.txt");
            //LogProcesos log = new LogProcesos();
            Configuracion configuracion = new Configuracion();
            ConfigReader config = new ConfigReader();
            string rutaArchivoConfig = Path.Combine(Path.GetDirectoryName(System.Reflection.Assembly.GetExecutingAssembly().Location), "config.txt");
            configuracion = config.leerArchivo(rutaArchivoConfig);
            JsonMaker jsonMaker = new JsonMaker();
            jsonMaker.conexionKDS = configuracion.ConexionKDS;
            jsonMaker.conexionMXP = configuracion.ConexionMXP;
           // jsonMaker.rutaLog = rutaLog;
            //jsonMaker.rutaArchivos = configuracion.Comandas.ruta;
            //jsonMaker.porArchivo = configuracion.Comandas.porArchivo;
            //jsonMaker.LeerComandas();
            //ConfigMaker.Instance.leerBaseDatos(configuracion.ConexionKDS);
            //jsonMaker.LeerComandasBase("15689051-88A7-EF11-BAA8-5CBA2C1DA3DC", "K104F000370277", "5", "0")
            //jsonMaker.LeerComandasBase("71D1A3E4-C4D7-473D-A6B2-AB093EED4953");
            //Impresion impresion = new Impresion();
            //impresion.ImprimirComandaDetalle("E77FC4F2-A6F0-4E2F-893B-24B2F2FEED40", "172.25.25.122", 9100);
        }
    }
}