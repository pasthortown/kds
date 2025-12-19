namespace KDS.Entidades
{
    public class ComandaNetCore
    {
        public string idImpresora { get; set; }
        public string idMarca { get; set; }
        public string idPlantilla { get; set; }
        public Data data { get; set; }
        public List<Registro> registros { get; set; }
    }

    public class Data
    {
        public string reimpresion { get; set; }
        public string canal { get; set; }
        public string turno { get; set; }
        public string cajero { get; set; }
        public string fecha { get; set; }
        public string cheque { get; set; }
    }

    public class Registro
    {
        public List<RegistroDetalle> registrosDetalle { get; set; }
    }

    public class RegistroDetalle
    {
        public string cantidad { get; set; }
        public string descripcion { get; set; }
    }

}
