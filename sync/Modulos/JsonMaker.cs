using KDS.Entidades;
using KDS.Interfaces;
using KDS.Repositorios;
using Microsoft.AspNetCore.Hosting.Server;
using NATS.Client.Core;
using System;
using System.Data;
using System.Linq;
using System.Text.Json;
using System.Text.Json.Serialization;
using System.Threading.Channels;
using System.Threading.Tasks;

namespace KDS.Modulos
{
    public class JsonMaker
    {

        private DistribuidorColas unDistribuidorColas;

        public string? rutaLog { get; set; }
        public string? rutaArchivos { get; set; }
        private IConector conector { get; set; } = new ConectorSQL();
        private ConectorAPI? conectorAPI { get; set; }
        public ConexionKDS? conexionKDS { get; set; }
        public ConexionMXP? conexionMXP { get; set; }
        public ConexionBackend? conexionBackend { get; set; }

        // Flag para saber si usar API o SQL Server
        private bool usarBackendAPI => conexionBackend?.url != null && !string.IsNullOrEmpty(conexionBackend.url);

        /// <summary>
        /// Lee las comandas de una ruta especificada
        /// </summary>
        /// <param name="ruta">En el constructor se especifica la ruta. Si no se indica se puede indicar aca.</param>
        public async void LeerComandas()
        {
            this.conector = new ConectorSQL();

            // Inicializar ConectorAPI si se usa backend
            if (usarBackendAPI)
            {
                this.conectorAPI = new ConectorAPI();
                this.conectorAPI.ConfigurarConexion(
                    conexionBackend!.url!,
                    conexionBackend.email ?? "admin@kds.local",
                    conexionBackend.password ?? "admin123"
                );
                LogProcesos.Instance.Escribir($"INFO: Usando ConectorAPI para enviar comandas a {conexionBackend.url}");
            }

            //LogProcesos registroProceso = new LogProcesos();
            DistribuidorColas unDistribuidorColas = new DistribuidorColas();
            ConfigMaker configMaker = ConfigMaker.Instance;
            DistribuidorPantallas unDistribuidorPantallas = new DistribuidorPantallas();

            // Solo leer de base KDS si no usamos el backend API
            if (!usarBackendAPI)
            {
                try
                {
                    configMaker.leerBaseDatos(this.conexionKDS);
                }
                catch (Exception ex)
                {
                    LogProcesos.Instance.Escribir(ex.Message);
                }
            }
            else
            {
                LogProcesos.Instance.Escribir("INFO: Saltando leerBaseDatos, se usa ConexionBackend");
            }


            //En primera instancia desactivo comandas viejas
            //if (ConfigMaker.Instance.configVisible.Comandas.tiempoComandasVivas != null)
            //    this.AnularComandasViejas(ConfigMaker.Instance.configVisible.Comandas.tiempoComandasVivas);
            //

            this.CargarListas();

            //Voy por nats o voy por el ciclo infinito
            if (configMaker.configVisible.Generales.ingresoPorBase)
            {
                while (true)
                {
                    try
                    {
                        //Modo automático, reemplazando el extractor de KDS
                        DataTable resultado = this.BuscarComandasParaJson();
                        LogProcesos.Instance.Escribir($"INFO: Se encontraron {resultado.Rows.Count} COMANDAS");
                        //Para cada fila avanzo
                        foreach (DataRow item in resultado.Rows)
                        {
                            try
                            {
                                LogProcesos.Instance.Escribir($"INFO: Procesando orden {item["idCabeceraOrdenPedido"].ToString()}");
                                //Devuelvo dos elemento: Comanda y Channel.name
                                var comanda = LeerComandasBase(item["idCabeceraOrdenPedido"].ToString(), item["cfac_id"].ToString(), item["restaurante"].ToString(), (item["tipo"].ToString() == "SALON" ? "0" : "1"));

                                //JsonMaker registra en la base la comanda.
                                //Tengo el texto de la comanda, eso registro en base.
                                this.RegistrarComanda(item["idCabeceraOrdenPedido"].ToString(), comanda.Item1);
                                LogProcesos.Instance.Escribir($"INFO: Comanda {item["idCabeceraOrdenPedido"].ToString()} insertada en COMANDAS");


                                this.ActualizarComandaJson(item["idCabeceraOrdenPedido"].ToString(), true, item["restaurante"].ToString());

                                //Asigno la cola que le corresponde, utilizando comanda (channel.name y el texto de la comanda)
                                unDistribuidorColas = new DistribuidorColas();
                                unDistribuidorColas.AsignarCola(item["idCabeceraOrdenPedido"].ToString(), comanda.Item2, comanda.Item1);

                            }
                            catch (Exception ex)
                            {
                                this.ActualizarComandaJson(item["idCabeceraOrdenPedido"].ToString(), false, item["restaurante"].ToString());
                                LogProcesos.Instance.Escribir($"ERROR: Comanda {item["idCabeceraOrdenPedido"].ToString()} no se pudo procesar - {ex.Message}");

                            }


                        }


                        //Antes de dormir, mato por tiempo todas las comandas viejas y vuelvo a cargar.
                        //if (ConfigMaker.Instance.configVisible.Comandas.tiempoComandasVivas != null)
                        //{
                        //    LogProcesos.Instance.Escribir($"INFO: Revisando comandas para limpiar");
                        //    this.AnularComandasViejas(ConfigMaker.Instance.configVisible.Comandas.tiempoComandasVivas);
                        //}


                        Thread.Sleep(ConfigMaker.Instance.configVisible.Generales.lecturaComandas * 1000);

                    }
                    catch (Exception ex)
                    {
                        Console.WriteLine($"ERROR: LeerComandas: {ex.Message}");
                        LogProcesos.Instance.Escribir($"ERROR: LeerComandas: {ex.Message}");
                        Thread.Sleep(ConfigMaker.Instance.configVisible.Generales.lecturaComandas * 1000);
                    }
                }
            }
            else
            {
                SuscribirseNats(configMaker.configVisible.ConexionNATS.Url, configMaker.configVisible.ConexionNATS.Tema);
            }

        }

        async void SuscribirseNats(string url, string tema)
        {
            var nats = new NatsConnection(NatsOpts.Default with { Url = url });

            await foreach (var msg in nats.SubscribeAsync<string>(tema))
            {
                LogProcesos.Instance.Escribir($"INFO: Comanda Recibida");
                await ProcesarComanda(msg.Data);

            }
        }

        public async Task ProcesarComanda(string mensaje)
        {
            try
            {
                Comanda order = JsonSerializer.Deserialize<Comanda>(mensaje);
                this.RegistrarComanda(order.orderId, JsonSerializer.Serialize(order));
                LogProcesos.Instance.Escribir($"INFO: Comanda {order.orderId} insertada en COMANDAS");


                //this.ActualizarComandaJson(order.orderId, true, order.id);

                //Asigno la cola que le corresponde, utilizando comanda (channel.name y el texto de la comanda)
                unDistribuidorColas = new DistribuidorColas();
                unDistribuidorColas.AsignarCola(order.orderId, order.channel.name, JsonSerializer.Serialize(order));
            }
            catch (Exception ex)
            {
                Console.WriteLine($"ERROR: LeerComandas: {ex.Message}");
                LogProcesos.Instance.Escribir($"ERROR: LeerComandas: {ex.Message}");
            }

        }


        /// <summary>
        /// Graba una comanda en la base de datos o la envía al backend vía API.
        /// </summary>
        /// <param name="idComanda">Identificador.</param>
        /// <param name="unaComanda">Comanda en formato json</param>
        /// <returns></returns>
        public int RegistrarComanda(string idComanda, string textoComanda)
        {
            try
            {
                // Si usamos backend API, enviar la comanda vía HTTP
                if (usarBackendAPI && conectorAPI != null)
                {
                    return RegistrarComandaAPI(idComanda, textoComanda).Result ? 1 : 0;
                }

                // Modo legacy: usar SQL Server
                this.conector.sesionConexion(this.conexionKDS.ip, this.conexionKDS.usuario,
                    this.conexionKDS.clave, this.conexionKDS.catalogo);
                this.conector.consultaDatos("idComanda", System.Data.SqlDbType.VarChar, idComanda);
                this.conector.consultaDatos("datosComanda", System.Data.SqlDbType.VarChar, textoComanda);
                DataTable tabla = this.conector.consultaEjecutarSPTabla("SP_ComandasInsertar");
                bdKDS2.Instance.SP_ComandasInsertar(new tComanda() { IdOrden = idComanda,
                    datosComanda = tabla.Rows[0]["datosComanda"].ToString(),
                    fechaIngreso = Convert.ToDateTime(tabla.Rows[0]["fechaIngreso"].ToString()),
                    idEstadoComanda = tabla.Rows[0]["idEstadoComanda"].ToString(),
                    fechaCreacion = tabla.Rows[0]["fechaCreacion"].ToString(),
                    Reimpresion = 0
                });
                return 1;
            }
            catch (Exception ex)
            {
                throw new Exception($"ERROR: RegistrarComanda: {ex.Message}");
            }
        }

        /// <summary>
        /// Registra comanda via API al backend Node.js/PostgreSQL
        /// </summary>
        private async Task<bool> RegistrarComandaAPI(string idComanda, string textoComanda)
        {
            try
            {
                // Deserializar la comanda del texto JSON
                var comandaOriginal = JsonSerializer.Deserialize<Comanda>(textoComanda);
                if (comandaOriginal == null)
                {
                    LogProcesos.Instance.Escribir($"ERROR: No se pudo deserializar comanda {idComanda}");
                    return false;
                }

                // Convertir al formato API
                var comandaApi = new ComandaApi
                {
                    id = comandaOriginal.id ?? idComanda.Substring(0, Math.Min(4, idComanda.Length)),
                    orderId = idComanda,
                    createdAt = DateTime.Now.ToString("o"),
                    channel = new ChannelApi
                    {
                        id = comandaOriginal.channel?.id ?? 1,
                        name = comandaOriginal.channel?.name ?? "DESCONOCIDO",
                        type = comandaOriginal.channel?.type ?? ""
                    },
                    cashRegister = new CashRegisterApi
                    {
                        cashier = comandaOriginal.cashRegister?.cashier ?? "",
                        name = comandaOriginal.cashRegister?.name ?? ""
                    },
                    customer = comandaOriginal.customer != null ? new CustomerApi { name = comandaOriginal.customer.name ?? "" } : null,
                    products = comandaOriginal.products?.Select(p => new ProductApi
                    {
                        productId = p.productId,
                        name = p.name ?? "",
                        amount = p.amount ?? 1,
                        category = p.category,
                        content = p.content,
                        products = p.products?.Select(sp => new SubProductApi
                        {
                            productId = sp.productId,
                            name = sp.name ?? "",
                            amount = sp.amount ?? 1,
                            category = sp.category,
                            content = sp.content
                        }).ToList()
                    }).ToList() ?? new List<ProductApi>(),
                    otrosDatos = comandaOriginal.otrosDatos != null ? new OtrosDatosApi
                    {
                        turno = comandaOriginal.otrosDatos.turno,
                        nroCheque = comandaOriginal.otrosDatos.nroCheque,
                        llamarPor = comandaOriginal.otrosDatos.llamarPor,
                        Fecha = comandaOriginal.otrosDatos.Fecha,
                        Direccion = comandaOriginal.otrosDatos.Direccion
                    } : null
                };

                var resultado = await conectorAPI!.EnviarComanda(comandaApi);

                if (resultado.Success)
                {
                    // También guardar en memoria local para el procesamiento interno
                    bdKDS2.Instance.SP_ComandasInsertar(new tComanda()
                    {
                        IdOrden = idComanda,
                        datosComanda = textoComanda,
                        fechaIngreso = DateTime.Now,
                        idEstadoComanda = "PENDIENTE",
                        fechaCreacion = DateTime.Now.ToString("o"),
                        Reimpresion = 0
                    });
                }

                return resultado.Success;
            }
            catch (Exception ex)
            {
                LogProcesos.Instance.Escribir($"ERROR: RegistrarComandaAPI: {ex.Message}");
                return false;
            }
        }

        /// <summary>
        /// Obtener el listado de comandas disponibles para enviar a KDS
        /// </summary>
        /// <returns>Tabla con las comandas.</returns>
        public DataTable BuscarComandasParaJson()
        {
            //ConexionMXP conexionBaseMXP = ConfigMaker.Instance.configVisible.ConexionMXP;
            //IConector conector = new ConectorSQL();
            this.conector.sesionConexion(this.conexionMXP.ip, this.conexionMXP.usuario,
                    this.conexionMXP.clave, this.conexionMXP.catalogo);


            DataTable resultados = this.conector.consultaEjecutarSPTabla("SP_BuscarComandasKDS");
            return resultados;
        }

        /// <summary>
        /// Registra en canal_movimiento que la comanda ya fue procesada.
        /// </summary>
        /// <param name="idCabeceraOrdenPedido">Comanda.</param>
        /// <param name="valorSeteo">Valor a registrar</param>
        /// <exception cref="Exception">Error al actualizar la comanda.</exception>
        public void ActualizarComandaJson(string idCabeceraOrdenPedido, bool valorSeteo, string restaurante)
        {
            int reintentos = ConfigMaker.Instance.configVisible.Comandas.reintentosBase;
            int i = 1;
            DataTable valorProcesado;

            while (i <= reintentos) 
            {
                try
                {
                    //IConector conector = new ConectorSQL();
                    this.conector.sesionConexion(this.conexionMXP.ip, this.conexionMXP.usuario,
                            this.conexionMXP.clave, this.conexionMXP.catalogo);


                    if (valorSeteo)
                    {
                        this.conector.consultaDatos("idRestaurante", System.Data.SqlDbType.Int, restaurante);
                        this.conector.consultaDatos("coleccion", System.Data.SqlDbType.VarChar, "KDS");
                        this.conector.consultaDatos("parametro", System.Data.SqlDbType.VarChar, "ORDEN PROCESADA");
                        valorProcesado = this.conector.consultaEjecutarSPTabla("config.sp_ColeccionRestauranteGenerica");
                    }
                    else
                    {
                        this.conector.consultaDatos("idRestaurante", System.Data.SqlDbType.Int, restaurante);
                        this.conector.consultaDatos("coleccion", System.Data.SqlDbType.VarChar, "KDS");
                        this.conector.consultaDatos("parametro", System.Data.SqlDbType.VarChar, "ORDEN PROCESADA ERROR");
                        valorProcesado = this.conector.consultaEjecutarSPTabla("config.sp_ColeccionRestauranteGenerica");
                    }

                    LogProcesos.Instance.Escribir($"INFO: Actualizar Canal_movimiento con valor {valorProcesado}");
                    this.conector.consultaDatos("idComanda", System.Data.SqlDbType.VarChar, idCabeceraOrdenPedido);
                    this.conector.consultaDatos("valorEstado", System.Data.SqlDbType.Float, valorProcesado.Rows[0]["dato"].ToString());
                    this.conector.consultaEjecutarSP("SP_ActualizarCanalMovimientoKDS");

                    LogProcesos.Instance.Escribir($"INFO: Actualizando Canal_movimiento para {idCabeceraOrdenPedido}");
                    i = reintentos + 1;
                }
                catch (Exception ex)
                {
                    if (i == reintentos)
                    {
                        throw new Exception($"ActualizarComandaJson: No se pudo procesar. {ex.Message}");
                    }
                    else
                    {
                        LogProcesos.Instance.Escribir($"ERROR: Reintento {i} Actualizando Canal_movimiento para  {idCabeceraOrdenPedido}. {ex.Message}");
                    }
                    i++;
                }
            }
            
          
        }

        /// <summary>
        /// Leer el detalle de la comanda de la base de datos.
        /// </summary>
        /// <param name="idOrden">Comanda.</param>
        /// <param name="cabeceraFactura">Número de cheque</param>
        /// <param name="idRestaurante">Id del restaurante</param>
        /// <param name="esLunch">Si es lunch o no</param>
        /// <returns></returns>
        /// <exception cref="Exception"></exception>
        public Tuple<string, string> LeerComandasBase(string idOrden, string cabeceraFactura, string idRestaurante, string esLunch)
        {
            try
            {
                this.conector = new ConectorSQL();
                this.conector.sesionConexion(this.conexionMXP.ip, this.conexionMXP.usuario,
                        this.conexionMXP.clave, this.conexionMXP.catalogo);

                this.conector.consultaDatos("idCabeceraOrdenPedido", System.Data.SqlDbType.VarChar, idOrden);
                this.conector.consultaDatos("esLunch", System.Data.SqlDbType.Int, esLunch);
                DataTable DetallePedido = this.conector.consultaEjecutarSPTabla("pedido.USP_AgrupacionProductosKds");  //múltiples filas, detalle del pedido

                this.conector.consultaDatos("modo", System.Data.SqlDbType.VarChar, "sync");
                this.conector.consultaDatos("id", System.Data.SqlDbType.VarChar, idOrden);
                this.conector.consultaDatos("idCadena", System.Data.SqlDbType.Int, idRestaurante);
                this.conector.consultaDatos("esLunch", System.Data.SqlDbType.Int, esLunch);
                DataTable DetalleCanal = this.conector.consultaEjecutarSPTabla("dbo.obtenerCanalKds");  //una sola fila, dice el canal de la orden

                this.conector.consultaDatos("idCabeceraOrdenPedido", System.Data.SqlDbType.VarChar, idOrden);
                this.conector.consultaDatos("esLunch", System.Data.SqlDbType.Int, esLunch);
                DataTable CabeceraPedido = this.conector.consultaEjecutarSPTabla("dbo.datosAdicionalesKds");  //cliente, estación, el usuario y el turno de la estación


                Comanda comanda = new Comanda();
                comanda.id = cabeceraFactura.Substring(10, 4); //es por política, corregir
                comanda.orderId = idOrden;
                comanda.channel = new ChannelC()
                {
                    id = int.Parse(idRestaurante),
                    name = DetalleCanal.Rows[0]["medio"].ToString(),
                    type = DetalleCanal.Rows[0]["clasificacion"].ToString()
                };
                comanda.cashRegister = new CashRegister()
                {
                    cashier = CabeceraPedido.Rows[0]["usuarioCajero"].ToString(),
                    name = CabeceraPedido.Rows[0]["estacion"].ToString()
                };
                comanda.customer = new Customer()
                {
                    name = CabeceraPedido.Rows[0]["cliente"].ToString()
                };

                comanda.otrosDatos = new OtrosDatos()
                {
                    turno = int.Parse(CabeceraPedido.Rows[0]["turno"].ToString()),
                    nroCheque = cabeceraFactura,
                    llamarPor = CabeceraPedido.Rows[0]["nombre"].ToString(),
                    Fecha = CabeceraPedido.Rows[0]["Fecha"].ToString(),
                    Direccion = CabeceraPedido.Rows[0]["Direccion"].ToString()
                };

                comanda.products = new List<Product>();
                foreach (DataRow item in DetallePedido.Rows)
                {
                    Product product = new Product();
                    if (item["ordenImpresion"].ToString() != "0")
                    {

                        //por cada fila agrego un nodo y además verifico si tiene contenido KDS
                        product.productId = item["plu_id"].ToString();
                        product.name = item["magp_desc_impresion"].ToString().Replace("[LLEVAR]", "").TrimEnd();
                        product.category = "";
                        product.amount = int.Parse(item["dop_cantidad"].ToString());
                        product.content = DesarmarContenidoKDS(item["content"].ToString(), int.Parse(item["dop_cantidad"].ToString()));
                        product.products = new List<Product2>();
                        comanda.products.Add(product);
                    }
                    else
                    {
                        //si ordenImpresio es 0, significa que es subitem de combo. La lista ya viene ordenada
                        //si es el primer producto a agregar en la sublista, hago new.
                        if (comanda.products[comanda.products.Count - 1].products == null)
                            comanda.products[comanda.products.Count - 1].products = new List<Product2>();

                        Product2 product2 = new Product2();
                        product2.productId = item["plu_id"].ToString();
                        product2.name = item["magp_desc_impresion"].ToString().Replace("[LLEVAR]", "").TrimEnd();
                        product2.amount = int.Parse(item["dop_cantidad"].ToString());
                        product2.category = "";
                        product2.content = DesarmarContenidoKDS(item["content"].ToString(), int.Parse(item["dop_cantidad"].ToString()));
                        comanda.products[comanda.products.Count - 1].products.Add(product2);

                    }

                }

                return Tuple.Create(JsonSerializer.Serialize(comanda), comanda.channel.name.ToString());
            }
            catch (Exception ex)
            {
                throw new Exception ($"ERROR: LeerComandasBase: Error al crear json. {ex.Message}");
            }
            

        }
        /// <summary>
        /// Toma los renglones de contenido KDS y la cantidad del combo para obtener las unidades correctas
        /// </summary>
        /// <param name="contenidoKDS">Cadena con los componentes</param>
        /// <param name="cantidad">Cantidad comprada de ese producto</param>
        /// <returns></returns>
        private List<string> DesarmarContenidoKDS(string contenidoKDS, int cantidad)
        {
            List<string> listaContenido = new List<string>();
            try
            {
                
                if (contenidoKDS  != "")
                {
                    
                    string[] renglones = contenidoKDS.Split(',');
                    foreach (string renglon in renglones)
                    {
                        try
                        {
                            string texto = renglon.Trim();
                            int cantidadContenido = Convert.ToInt32(texto.Substring(0, texto.ToUpper().IndexOf("X"))) * cantidad;
                            string descripcion = texto.Substring(texto.ToUpper().IndexOf("X") + 1).Trim();
                            string renglonFinal = $"{cantidadContenido.ToString()}x {descripcion}";
                            listaContenido.Add(renglonFinal);
                        }
                        catch (Exception ex)
                        {
                            LogProcesos.Instance.Escribir($"ERROR: No se puede desarmar {renglon.Trim()}");
                        }
                       
                    }
                }
                return listaContenido;
            }
            catch (Exception)
            {
                LogProcesos.Instance.Escribir($"ERROR: contenidoKDS {contenidoKDS} no se puede descomponer");
                return listaContenido;
            }
            
        }

        /// <summary>
        /// Cambia de estado las órdenes que no estén cerradas luego de cierto tiempo.
        /// </summary>
        /// <param name="tiempo">Minutos.</param>
        /// <exception cref="Exception">Error producido en la base de datos.</exception>
        public void AnularComandasViejas(int? tiempo)
        {
            try
            {
                //IConector conector = new ConectorSQL();
                this.conector.sesionConexion(this.conexionKDS.ip, this.conexionKDS.usuario,
                    this.conexionKDS.clave, this.conexionKDS.catalogo);
                conector.consultaDatos("tiempoMinutos", System.Data.SqlDbType.Int, tiempo.ToString());
                int resultado = conector.consultaEjecutarSPEscalar("SP_AnularComandasPorTiempo");
                if (resultado > 0) 
                {
                    LogProcesos.Instance.Escribir("INFO: Tiempo de vida de comandas excedidos. Se recargan las listas");
                    this.CargarListas();
                }
                return;
            }
            catch (Exception ex)
            {
                throw new Exception($"ERROR: AnularComandasViejas: {ex.Message}");
            }
        }

        public void CargarListas()
        {
            try
            {
                LogProcesos.Instance.Escribir("INFO: Cargando listas");

                // Si usamos backend API, las listas no se cargan desde SQL Server
                if (usarBackendAPI)
                {
                    bdKDS2.Instance.LimpiarListas();
                    LogProcesos.Instance.Escribir("INFO: Listas limpiadas, usando ConexionBackend para persistencia");
                    return;
                }

                //Cargo en memoria las listas de comandas y distribucion
                this.conector.sesionConexion(this.conexionKDS.ip, this.conexionKDS.usuario,
                        this.conexionKDS.clave, this.conexionKDS.catalogo);

                bdKDS2.Instance.LimpiarListas();
                DataTable tablaComandas = this.conector.consultaEjecutarSPTabla("SP_ComandasListar");
                LogProcesos.Instance.Escribir($"INFO: Cargando comandas en memoria. Total: {tablaComandas.Rows.Count}");
                foreach (DataRow fila in tablaComandas.Rows)
                {
                    bdKDS2.Instance.CargarComandas(fila["IdOrden"].ToString(), fila["datosComanda"].ToString(), Convert.ToDateTime(fila["fechaIngreso"].ToString()), fila["idEstadoComanda"].ToString(),
                        fila["fechaCreacion"].ToString(), Convert.ToInt32(fila["Reimpresion"].ToString()));
                }
                tablaComandas = null;
                DataTable tablaDistribucion = this.conector.consultaEjecutarSPTabla("SP_DistribucionListar");
                LogProcesos.Instance.Escribir($"INFO: Cargando distribuciones en memoria. Total: {tablaDistribucion.Rows.Count}");
                foreach (DataRow fila in tablaDistribucion.Rows)
                {
                    bdKDS2.Instance.CargarDistribucion(fila["IdOrden"].ToString(), fila["Cola"].ToString(), fila["Pantalla"].ToString(),
                        fila["idEstadoDistribucion"].ToString(), Convert.ToDateTime(fila["fechaModificacion"].ToString()));
                }
                tablaDistribucion = null;
            }
            catch(Exception ex)
            {
                throw new Exception($"ERROR: CargarListas: {ex.Message}");
            }

        }

    }
}
