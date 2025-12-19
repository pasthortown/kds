using KDS.Entidades;
using KDS.Interfaces;
using KDS.Repositorios;
using System.Data;
using System.Reflection;

namespace KDS.Modulos
{
    public class AlgoritmoDisponibilidad : IAlgoritmoBalanceo
    {
        public void RegistrarPantallaDestino(string idComanda, string nombreCola, List<Pantalla> pantallasObjetivo)
        {
            //LogProcesos log = new LogProcesos();
            try
            {
                //Conexión a la base
                ConexionKDS conexionBaseKDS = ConfigMaker.Instance.configVisible.ConexionKDS;
                IConector conector = new ConectorSQL();
                conector.sesionConexion(conexionBaseKDS.ip, conexionBaseKDS.usuario,
                    conexionBaseKDS.clave, conexionBaseKDS.catalogo);

                //Para cada pantalla objetivo reviso cuántas comandas tiene asignadas
                //en estado EN_PANTALLA, es decir, tiene comandas mostrando por la pantalla.
                foreach (Pantalla unaPantalla in pantallasObjetivo)
                {
                    conector.consultaDatos("pantalla", System.Data.SqlDbType.VarChar, unaPantalla.nombre);
                    conector.consultaDatos("cola", System.Data.SqlDbType.VarChar, nombreCola);
                    //DataTable resultado = conector.consultaEjecutarSPTabla("SP_DistribucionCantidadComandas");
                    //unaPantalla.cantidad = int.Parse(resultado.Rows[0][0].ToString());
                    unaPantalla.cantidad = conector.consultaEjecutarSPEscalar("SP_DistribucionCantidadComandas"); 
                }

                //Me quedo con aquellas que tenga 0 o más y orden por cantidad
                //Entonces obtengo la primera
                if (pantallasObjetivo.Count > 0)
                {
                    LogProcesos.Instance.Escribir($"INFO: Cantidad de pantallas {pantallasObjetivo.Count}");
                    Pantalla pantallaSeleccionada = pantallasObjetivo.Where(pantalla => pantalla.cantidad >= 0).OrderBy(pantalla => pantalla.cantidad).First();
                    LogProcesos.Instance.Escribir($"INFO: Pantalla seleccionada {pantallaSeleccionada.nombre}");
                    //Cuando grabo en la base, utilizo el nombre de la pantallaSeleccionada
                    conector.consultaDatos("idOrden", System.Data.SqlDbType.VarChar, idComanda);
                    conector.consultaDatos("cola", System.Data.SqlDbType.VarChar, nombreCola);
                    conector.consultaDatos("pantalla", System.Data.SqlDbType.VarChar, pantallaSeleccionada.nombre);
                    DataTable tabla = conector.consultaEjecutarSPTabla("SP_DistribucionActualizarPantalla");
                    bdKDS2.Instance.SP_DistribucionActualizarPantalla(idComanda, nombreCola, pantallaSeleccionada.nombre, tabla.Rows[0]["IdEstadoDistribucion"].ToString());
                    LogProcesos.Instance.Escribir($"INFO: {idComanda} asignada en COLA: {nombreCola} - PANTALLA: {pantallaSeleccionada.nombre}");
                }
                else
                {
                    LogProcesos.Instance.Escribir($"INFO: No hay pantallas activas para asignar {idComanda} en la cola {nombreCola}");
                }
               
            }
            catch (Exception ex)
            {

                throw new Exception($"{MethodBase.GetCurrentMethod().Name}: {ex.Message}");
            }
          

        }
    }
}
