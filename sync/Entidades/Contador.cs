namespace KDS.Entidades
{
    public class Contador
    {
        public List<ProductoContar>? counters { get; set; }
       
    }

    public class ProductoContar
    {
        public string name { get; set; }
        public int? amount { get; set; }

        public string etiqueta { get; set; }
    }
}
