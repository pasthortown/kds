using KDS.Entidades;

namespace KDS.Interfaces
{
    public interface IAlgoritmoBalanceo
    {
        public void RegistrarPantallaDestino(string idComanda, string nombreCola, List<Pantalla> pantallasObjetivo);

    }
}
