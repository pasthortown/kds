using KDS.Entidades;
using KDS.Interfaces;
using KDS.Repositorios;
using System.Data;
using System.Reflection;
using System.Text.Json;

namespace KDS.Modulos
{
    public class DistribuidorPantallas
    {

        /// <summary>
        /// Aisgna una pantalla a una orden. Si no hay pantallas activas, las marca como SIN_ASIGNAR.
        /// </summary>
        /// <param name="listaComandas">LLista de comandas</param>
        /// <param name="nombreCola">Nombre de cola que contiene las pantallas</param>
        /// <param name="rebalanceo">SI o NO paraindicar que se debe limpiar y reasignar</param>
        public void AsignarPantalla(List<string> listaComandas, string nombreCola)
        {

            IAlgoritmoBalanceo balanceo = null;
            switch (ConfigMaker.Instance.detalleCola(nombreCola).distribucion)
            {
                case "D":
                    balanceo = new AlgoritmoDisponibilidad();
                    break;
                case "RR":
                    break;
            }

            if (balanceo != null)
            {
                //COMIENZO
                //1. Debo obtener la lista de pantallas activas
                List<Pantalla> listaPantallasActivas = ScreenChecker.Instance.PantallasActivas(nombreCola);

                //2. Debo obtener las pantallas disponibles en una cola
               // List<Pantalla> listaPantallasDeUnaCola = ScreenChecker.Instance.Pantallas(nombreCola);

                //Cruzo pantallas activas con las pantallas existentes en la cola
                //y me quedo con las que están realmente
                //genero una estructura que tenga Nombre y Cantidad

                //ENTONCES: Inicializo en -1 para indicar que aún no se ha buscado información
                foreach (Pantalla unaPC in listaPantallasActivas)
                {
                    unaPC.cantidad = 0;
                    //Cruzo con pantallas activas
                }

                //Luego llamo a registrar la pantalla
                //El objeto balanceo tiene el algoritmo necesario para asignar
                foreach (string idComanda in listaComandas)
                {

                    ////Si no hay pantallas existentes (NO SIGNIFICA QUE NO ESTÉ CONECTADA), no se asigna a nadie
                    if (ScreenChecker.Instance.Pantallas(nombreCola).Count == 0)
                    {
                        Console.WriteLine($"Comanda {idComanda} - No hay pantallas asignadas a la cola {nombreCola}");
                        LogProcesos.Instance.Escribir($"Comanda {idComanda} - No hay pantallas asignadas a la cola {nombreCola}");
                        continue;
                    }

                    //Si no hay pantallas ACTIVAS, no se asigna a nadie
                    if (ScreenChecker.Instance.PantallasActivas(nombreCola).Count == 0)
                    {
                        Console.WriteLine($"Comanda {idComanda} - No hay pantallas ACTIVAS en la cola {nombreCola}");
                        LogProcesos.Instance.Escribir($"Comanda {idComanda} - No hay pantallas ACTIVAS en la cola {nombreCola}");

                        if (ConfigMaker.Instance.configVisible.Comandas.imprimirSinPantallasActivas)
                        {
                            Console.WriteLine("No se encontraron pantallas activas, se manda a imprimir");
                            //Significa que no hay pantallas activas y la comanda nunca se mandó a asignar
                            //La borro de la memoria (sería mandar a imprimir) y mando efectivamente a imprimir
                            Impresion impresion = new Impresion();
                            LogProcesos.Instance.Escribir($"INFO: Imprime por falta de pantallas por TCP - {ConfigMaker.Instance.configVisible.Comandas.impresoraIP}:{ConfigMaker.Instance.configVisible.Comandas.impresoraPuerto} - {idComanda}");
                            Thread ordenImpresion = new Thread(() =>
                            {
                                //Conexión a la base
                                ConexionKDS conexionBaseKDS = ConfigMaker.Instance.configVisible.ConexionKDS;
                                IConector conector = new ConectorSQL();
                                conector.sesionConexion(conexionBaseKDS.ip, conexionBaseKDS.usuario,
                                    conexionBaseKDS.clave, conexionBaseKDS.catalogo);

                                LogProcesos.Instance.Escribir($"INFO: En imprimir detalle - {idComanda}");
                                tComanda? comandaResultado = bdKDS2.Instance.SP_ComandaLeer(idComanda);

                                if (comandaResultado == null)
                                {
                                    LogProcesos.Instance.Escribir($"INFO: Comanda no encontrada - {idComanda}");
                                    return;
                                }
                                Comanda? unaComanda = JsonSerializer.Deserialize<Comanda>(comandaResultado.datosComanda);

                                impresion.ImprimirComandaDetalle(comandaResultado, unaComanda, ConfigMaker.Instance.configVisible.Comandas.impresoraIP, ConfigMaker.Instance.configVisible.Comandas.impresoraPuerto, ConfigMaker.Instance.configVisible.Comandas.columnasDetalles, ConfigMaker.Instance.configVisible.Comandas.fuenteDetalles, true, nombreCola);
                                conector.consultaDatos("idOrden", System.Data.SqlDbType.VarChar, idComanda);
                                conector.consultaDatos("cola", System.Data.SqlDbType.VarChar, nombreCola);
                                conector.consultaEjecutarSP("SP_DistribucionSinPantallaActiva");
                                bdKDS2.Instance.SP_DistribucionImprimir(idComanda);
                            });
                            ordenImpresion.Start();

                            //Borro de memoria, no importa qué pantalla o cola, hay que sacarlo
                           
                        }

                        //Activo flag en Screencheker.
                        if (ScreenChecker.Instance.colasProblemas != null)
                            ScreenChecker.Instance.colasProblemas[nombreCola] = true;
                        //Limpio: que todas las comandas en esa cola tengan el null en "Pantalla asignada"
                        this.LimpiarAsignaciones(idComanda, nombreCola);
                        continue;
                    }

                    balanceo.RegistrarPantallaDestino(idComanda, nombreCola, listaPantallasActivas);
                }

            }
  
        }

        /// <summary>
        /// Asignar cantidad cero a la pantalla para indicar que está activa y recibirá cuántas comandas tiene.
        /// </summary>
        /// <param name="activas">Lista de pantallas activas</param>
        /// <param name="cola">Lista de pantallas de la cola a la que pertenece una pantalla</param>
        /// <exception cref="Exception"></exception>
        //private void CruzarPantallasActivasYCola(List<Pantalla> activas, List<Pantalla> cola)
        //{
        //    try
        //    {
        //        foreach (Pantalla unaPActiva in activas)
        //        {
        //            //Valido si cada pantalla activa existe en la cola.
        //            //Si existe le cargo 0.
        //            Pantalla pElegida = cola.Where(pantalla => pantalla.ip == unaPActiva.ip).FirstOrDefault();
        //            if (pElegida != null)
        //                pElegida.cantidad = 0;
        //        }
        //    }
        //    catch (Exception ex)
        //    {

        //        throw new Exception($"{MethodBase.GetCurrentMethod().Name}: {ex.Message} ");
        //    }
           
        //}


        /// <summary>
        /// Cambia a estado REASIGNAR y luego le asigna una pantalla. El método controla cuántas pantallas activas tiene.
        /// El mecanismo de rebalanceo se ejecuta con una o más pantallas activas.
        /// </summary>
        /// <param name="ipPantalla">IP de la pantalla.</param>
        public void ReasignarComandas(string ipPantalla)
        {

            //string rutaLog = $"{Path.GetDirectoryName(System.Reflection.Assembly.GetExecutingAssembly().Location)}\\logs\\log {DateTime.Now.Date.ToString("u").Substring(0, 10)}.txt";
            //LogProcesos log = new LogProcesos();
            Pantalla unaPantalla = ConfigMaker.Instance.detallePantalla(ipPantalla);

            //Conexión a la base
            ConexionKDS conexionBaseKDS = ConfigMaker.Instance.configVisible.ConexionKDS;
            IConector conector = new ConectorSQL();
            conector.sesionConexion(conexionBaseKDS.ip, conexionBaseKDS.usuario,
                conexionBaseKDS.clave, conexionBaseKDS.catalogo);

            //Busco pantallas activas según la cola donde existe esa pantalla
            if (ScreenChecker.Instance.PantallasActivas(unaPantalla.cola).Count >= 1)
            {
                conector.consultaDatos("cola", System.Data.SqlDbType.VarChar, unaPantalla.cola);
                conector.consultaDatos("pantalla", System.Data.SqlDbType.VarChar, unaPantalla.nombre);
                DataTable comandasAReasignar = conector.consultaEjecutarSPTabla("SP_DistribucionLimpiarAsignacion");
                bdKDS2.Instance.SP_DistribucionLimpiarAsignacion(unaPantalla.cola, unaPantalla.nombre);
                LogProcesos.Instance.Escribir($"Enviando a otra pantalla por desactivación. Origen: {unaPantalla.cola}-{unaPantalla.nombre}.");
                List<string> listaComandas = new List<string>();
                for (int i = 0; i < comandasAReasignar.Rows.Count; i++)
                {
                    listaComandas.Add(comandasAReasignar.Rows[i]["IdOrden"].ToString());
                }

                this.AsignarPantalla(listaComandas, unaPantalla.cola);
            }
            //Falta salvar si no hay pantallas activas, entonces es todo A SIN_PANTALLA
            else //entonces si no hay más pantallas, a todas las de esa cola las paso a Sin_Asignar
            {
                //A todas las pantallas que estén en esa cola, las mando a SIN_PANTALLA
                conector.consultaDatos("cola", System.Data.SqlDbType.VarChar, unaPantalla.cola);
                conector.consultaEjecutarSP("SP_DistribucionColaASinPantalla");
                bdKDS2.Instance.SP_DistribucionColaASinPantalla(unaPantalla.cola);
                LogProcesos.Instance.Escribir($"Todas las pantallas de la cola {unaPantalla.cola} están inactivas.");
            }


        }

        private void LimpiarAsignaciones(string idComanda, string nombreCola)
        {
            ConexionKDS conexionBaseKDS = ConfigMaker.Instance.configVisible.ConexionKDS;
            IConector conector = new ConectorSQL();
            conector.sesionConexion(conexionBaseKDS.ip, conexionBaseKDS.usuario,
                    conexionBaseKDS.clave, conexionBaseKDS.catalogo);
            conector.consultaDatos("idOrden", System.Data.SqlDbType.VarChar, idComanda);
            conector.consultaDatos("cola", System.Data.SqlDbType.VarChar, nombreCola);
            conector.consultaEjecutarSP("SP_DistribucionAsignarSinPantalla"); 
            bdKDS2.Instance.SP_DistribucionAsignarSinPantalla(idComanda, nombreCola);
        }

        public void AsignarComandasSinPantalla(string ipPantalla)
        {
            ConexionKDS conexionBaseKDS = ConfigMaker.Instance.configVisible.ConexionKDS;
            IConector conector = new ConectorSQL();
            conector.sesionConexion(conexionBaseKDS.ip, conexionBaseKDS.usuario,
                    conexionBaseKDS.clave, conexionBaseKDS.catalogo);

            Pantalla pantalla = ScreenChecker.Instance.detallePantalla(ipPantalla);
            conector.consultaDatos("cola", System.Data.SqlDbType.VarChar, pantalla.cola);
            DataTable comandasSinPantalla = conector.consultaEjecutarSPTabla("SP_DistribucionListarComandSinPantalla");
            List<string> listaComandas = new List<string>();
            for (int i = 0; i < comandasSinPantalla.Rows.Count; i++)
            {
                listaComandas.Add(comandasSinPantalla.Rows[i]["IdOrden"].ToString());
            }
            this.AsignarPantalla(listaComandas, pantalla.cola);
        }

        /// <summary>
        /// Al detectar un ON de pantalla hay que distribuir en partes iguales en todas las pantallas.
        /// La lógica está en el SP
        /// </summary>
        /// <param name="ipPantalla"></param>
        public void BalancearPorNuevaPantallaActiva(string ipPantalla)
        {
            string nombreCola = ScreenChecker.Instance.detallePantalla(ipPantalla).cola;
            List<Pantalla> listaPantallas = ScreenChecker.Instance.PantallasActivas(nombreCola);
            if (listaPantallas.Count > 1)
            {
                ConexionKDS conexionBaseKDS = ConfigMaker.Instance.configVisible.ConexionKDS;
                IConector conector = new ConectorSQL();
                conector.sesionConexion(conexionBaseKDS.ip, conexionBaseKDS.usuario,
                        conexionBaseKDS.clave, conexionBaseKDS.catalogo);

                conector.consultaDatos("cola", System.Data.SqlDbType.VarChar, nombreCola);
                DataTable resultados = conector.consultaEjecutarSPTabla("SP_DistribucionListarComandasEnCola");

                int numeroPantalla = 0;
                int iPos = 0;
                for (int i = 0; i < resultados.Rows.Count; i++)
                {
                    conector.consultaDatos("idOrden", System.Data.SqlDbType.VarChar, resultados.Rows[i]["IdOrden"].ToString());
                    conector.consultaDatos("cola", System.Data.SqlDbType.VarChar, nombreCola);
                    conector.consultaDatos("Pantalla", System.Data.SqlDbType.VarChar, listaPantallas[numeroPantalla].nombre);
                    DataTable resultado = conector.consultaEjecutarSPTabla("SP_DistribucionActualizarPantalla");
                    bdKDS2.Instance.SP_DistribucionActualizarPantalla(resultados.Rows[i]["IdOrden"].ToString(), nombreCola, listaPantallas[numeroPantalla].nombre, resultado.Rows[0]["IdEstadoDistribucion"].ToString());
                    numeroPantalla++;

                    if (numeroPantalla == listaPantallas.Count)
                        numeroPantalla = 0;
                }
                
            }
        }

    }
}
