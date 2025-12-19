using KDS.Modulos;
using KDS.Entidades;
using Microsoft.AspNetCore.Mvc;
using System.Data;
using System.Net.Sockets;
using System.Net;
using System.Text;

namespace KDS.Controllers
{
    [ApiController]
    [Route("")]
    public class DebugController : ControllerBase
    {
        //[HttpGet("debug")]
        //public IActionResult ListarPantalla(string ipPantalla)
        //{

        //    //http://ip:puerto/debug?ipPantalla=
        //    Pantalla pantalla = ConfigMaker.Instance.detallePantalla(ipPantalla);
        //    ScreenManager screenManager = new ScreenManager();
        //    //Retorno la tabla de resultados
        //    DataTable resultados = screenManager.ObtenerComandas(pantalla);
        //    List<string> comandasSeparadas = new List<string>();
        //    int i = 0;
        //    foreach (DataRow fila in resultados.Rows)
        //    {
        //        i++;
        //        string comanda = screenManager.AplicarFiltro(fila["datosComanda"].ToString(), pantalla.filtros);
        //        if (comanda.Length > 0)
        //            comandasSeparadas.Add(comanda);
        //    }

        //    string listaConfiguraciones = ConfigMaker.Instance.configuracionPantalla(ipPantalla);
        //    string listaComandas = "[" + String.Join(",", comandasSeparadas) + "]";

        //    return Ok($"{{'config': {listaConfiguraciones} , 'comandas': {listaComandas}}}".Replace("'","\""));
        //}

        //[HttpGet("debugImprimir")]
        //public IActionResult ImprimirComandaPrueba(string ipImpresora, int puertoImpresora)
        //{
        //    try
        //    {
        //        byte[] enter5 = StringToByteArray("1b6405"); //ENTER
        //        byte[] cierre2 = StringToByteArray("1b6d00"); //CORTE DE PAPEL

        //        Socket miSocket = new Socket(AddressFamily.InterNetwork, SocketType.Stream, ProtocolType.Tcp);
        //        IPEndPoint miDireccion = new IPEndPoint(IPAddress.Parse(ipImpresora), puertoImpresora);
        //        miSocket.Connect(miDireccion);

        //        string cadena = "HOLA MUNDO";
        //        byte[] mensajeFinal = new byte[cadena.Length + "1B6400".Length];

        //        byte[] aEnviar = Encoding.UTF8.GetBytes(cadena).ToArray();

        //        _ = miSocket.Send(aEnviar, SocketFlags.None);
        //        _ = miSocket.Send(enter5, SocketFlags.None);
        //        _ = miSocket.Send(cierre2, SocketFlags.None);

        //        miSocket.Close();

        //        return Ok("Impresión exitosa");
        //    }
        //    catch (Exception ex)
        //    {

        //        return Ok(ex.Message);
        //    }

        //}

        //public static byte[] StringToByteArray(String hex)
        //{
        //    int NumberChars = hex.Length / 2;
        //    byte[] bytes = new byte[NumberChars];
        //    using (var sr = new StringReader(hex))
        //    {
        //        for (int i = 0; i < NumberChars; i++)
        //            bytes[i] =
        //              Convert.ToByte(new string(new char[2] { (char)sr.Read(), (char)sr.Read() }), 16);
        //    }
        //    return bytes;
        //}
    }
}
