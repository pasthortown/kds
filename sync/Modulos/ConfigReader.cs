using KDS.Entidades;
using KDS.Interfaces;
using KDS.Repositorios;
using System.Reflection;
using System.Text.Json;

namespace KDS.Modulos
{
    public class ConfigReader
    {
        /// <summary>
        /// Leer el archivo config en la ruta especificada.
        /// </summary>
        /// <param name="ruta">Ruta del archivo. Incluir el nombre y la extensión</param>
        /// <returns></returns>
        /// <exception cref="Exception"></exception>
        public Configuracion leerArchivo(string ruta, string rutaArchivoComanda = "")
        {
            try
            {

                string archivoLeido;
                Configuracion configuracion;
                using (StreamReader archivo = new StreamReader(ruta, System.Text.Encoding.UTF8))
                {
                    archivoLeido = archivo.ReadToEnd().Replace("\r","").Replace("\n","").Replace("\t","");
                    archivo.Close();
                }
                configuracion = JsonSerializer.Deserialize<Configuracion>(archivoLeido);

                archivoLeido = JsonSerializer.Serialize<Configuracion>(configuracion);

                // Si hay ConexionBackend configurada, no escribir a SQL Server KDS
                if (configuracion?.ConexionBackend?.url == null || string.IsNullOrEmpty(configuracion.ConexionBackend.url))
                {
                    escribirDatos(configuracion, archivoLeido);
                }
                else
                {
                    LogProcesos.Instance.Escribir("INFO: Usando ConexionBackend en lugar de ConexionKDS (SQL Server)");
                }

                return configuracion;
            }
            catch(Exception ex)
            {
                throw new Exception ($"ERROR: {MethodBase.GetCurrentMethod().Name}: {ex.Message}");
            }

        }

        public string leerFormatoComandas(string ruta)
        {
            string leido;
            try
            {

                using (StreamReader leerArchivo = new StreamReader(ruta))
                {
                    leido = leerArchivo.ReadToEnd();
                }
                return leido;

            }
            catch (Exception ex)
            {
                throw new Exception($"ERROR: {MethodBase.GetCurrentMethod().Name}: {ex.Message}");
            }

        }

        private void escribirDatos(Configuracion? configuracion, string archivoLeido)
        {

            try
            {
                IConector conector = new ConectorSQL();
                conector.sesionConexion(configuracion.ConexionKDS.ip, configuracion.ConexionKDS.usuario,
                    configuracion.ConexionKDS.clave, configuracion.ConexionKDS.catalogo);
                conector.consultaDatos("datosJson", System.Data.SqlDbType.VarChar, archivoLeido);
                conector.consultaEjecutarSP("SP_ConfigInsertar");
            }
            catch (Exception ex)
            {

                throw new Exception($"ERROR: {MethodBase.GetCurrentMethod().Name}: {ex.Message}");
            }
           

        }
    }
}
