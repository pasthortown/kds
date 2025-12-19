using KDS.Entidades;
using KDS.Interfaces;
using KDS.Repositorios;
using System.Data;
using System.Text.Json;

namespace KDS.Modulos
{
    public class DistribuidorColas
    {
        /// <summary>
        /// Asignar la comanda a una cola determinada.
        /// </summary>
        /// <param name="idComanda">Id de orden.</param>
        /// <param name="channelName">Nombre de la cola</param>
        /// <exception cref="Exception"></exception>
        public void AsignarCola(string idComanda, string channelName, string comandaString)
        {
            ConexionKDS conexionBaseKDS = ConfigMaker.Instance.configVisible.ConexionKDS;
            //Registra en la base de datos
            //Utilizando el idComanda y el Nombre
            IConector conector = new ConectorSQL();
            conector.sesionConexion(conexionBaseKDS.ip, conexionBaseKDS.usuario,
                conexionBaseKDS.clave, conexionBaseKDS.catalogo);

            //LogProcesos log = new LogProcesos();
            List<Cola> colasAisgnadas = new List<Cola>();
            try
            {
                foreach (var unaCola in ConfigMaker.Instance.listarColas())
                {
                    bool asignar = false;


                    if (unaCola.canales.Count == 0)
                    {
                        asignar = true;
                    }
                    else if (unaCola.canales.Contains(channelName)) //Reviso en los canales disponibles en la cola si existe el canal de la comanda
                    {
                        asignar = true;

                    }

                    if (this.AplicarFiltro(comandaString, unaCola.filtros) == "")
                    {
                        asignar = false;
                    }

                    if (asignar)
                    {
                      
                        conector.consultaDatos("idOrden", System.Data.SqlDbType.VarChar, idComanda);
                        conector.consultaDatos("cola", System.Data.SqlDbType.VarChar, unaCola.nombre);
                        DataTable tabla = conector.consultaEjecutarSPTabla("SP_DistribucionInsertar");

                        bdKDS2.Instance.SP_DistribucionInsertar(new tDistribucion()
                        {
                            idOrden = tabla.Rows[0]["idOrden"].ToString(),
                            Cola = tabla.Rows[0]["Cola"].ToString(),
                            Pantalla = "",
                            IdEstadoDistribucion = tabla.Rows[0]["IdEstadoDistribucion"].ToString(),
                            fechaModificacion = Convert.ToDateTime(tabla.Rows[0]["fechaModificacion"].ToString())
                        });

                        DistribuidorPantallas distribuidorPantallas = new DistribuidorPantallas();
                        List<string> listaComandas = new List<string>();
                        listaComandas.Add(idComanda);
                        distribuidorPantallas.AsignarPantalla(listaComandas, unaCola.nombre);
                    }

                    

                }

                //Debo revisar si al final de todas las asignaciones quedó la orden impresa por todas las colas disponibles
                //Eso significa que no hay pantalla disponible
                //Debo cerrar el pedido, es decir, su cabecera en la base de KDS.
                conector.consultaDatos("idOrden", System.Data.SqlDbType.VarChar, idComanda);
                conector.consultaEjecutarSP("SP_ComandaCerrarSinPantallas");

                return;
            }
            catch (Exception ex)
            {
                throw new Exception(ex.Message);
            }

        }

        private string AplicarFiltro(string comanda, List<string> filtros)
        {
            if (filtros == null)
                return comanda;

            Comanda? unaComanda = JsonSerializer.Deserialize<Comanda>(comanda);
            int b = 0;
            bool sinBorrar = false;

            //Recorro la lista principal de productos
            if (filtros.Count > 0)
                for (int i = 0; i < unaComanda.products.Count; i++)
                {
                    //Pregunto para cada filtro si está en el nombre. SI lo está, NO limpio la cadena.
                    b = 1;
                    foreach (string unFiltro in filtros)
                    {
                        if (unaComanda.products[i].name != null)
                            if (unaComanda.products[i].name.ToUpper().Contains(unFiltro.ToUpper()))
                                b = 0;
                    }
                    if (b == 1)
                        unaComanda.products[i].name = "";

                    //Pregunto para cada filtro si está en contenidoKDS. SI lo está, limpio la cadena.

                    if (unaComanda.products[i].content != null)
                        for (int iContenido = 0; iContenido < unaComanda.products[i].content.Count; iContenido++)
                        {
                            b = 1;
                            foreach (string unFiltro in filtros)
                            {
                                if (unaComanda.products[i].content[iContenido].ToUpper().Contains(unFiltro.ToUpper()))
                                    b = 0;
                            }

                            if (b == 1)
                                unaComanda.products[i].content[iContenido] = "";
                            b = 0;

                        }

                    //Para cada sublista de producto hago el mismo análisis.
                    for (int j = 0; j < unaComanda.products[i].products.Count; j++)
                    {
                        b = 1;
                        //La descripción del producto
                        foreach (string unFiltro in filtros)
                        {
                            if (unaComanda.products[i].products[j].name != null)
                                if (unaComanda.products[i].products[j].name.ToUpper().Contains(unFiltro.ToUpper()))
                                    b = 0;
                        }

                        if (b == 1)
                            unaComanda.products[i].products[j].name = "";


                    }

                }

            foreach (Product item in unaComanda.products)
            {
                if (item.name != "")
                    sinBorrar = true;

                if (item.content.Any(x => x != ""))
                    sinBorrar = true;

                if (item.products != null)
                    foreach (Product2 subtiem in item.products)
                    {
                        if (subtiem.name != "")
                            sinBorrar = true;

                    }
            }

            if (sinBorrar)
                return JsonSerializer.Serialize(unaComanda);
            else
                return "";

        }

    }
}
