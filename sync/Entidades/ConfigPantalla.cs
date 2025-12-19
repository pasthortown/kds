namespace KDS.Entidades
{
    public class Appearance
    {
        public List<CardColor> cardColor { get; set; }
        public List<ChannelColor> channelColor { get; set; }
        public string fontSize { get; set; }
        public string ordersDisplay { get; set; }
        public string theme { get; set; }
        public int columnsPerScreen { get; set; }
        public string columnSize { get; set; }
        public string fontFamily { get; set; }
        public string screenName { get; set; }
    }

    public class CardColor
    {
        public string id { get; set; }
        public string color { get; set; }
        public string minutes { get; set; }
        public bool isFullBackground { get; set; }
    }

    public class ChannelColor
    {
        public string id { get; set; }
        public string color { get; set; }
        public string channel { get; set; }
    }

    public class ClientData
    {
        public bool active { get; set; }
    }

    public class FinishOrder
    {
        public bool active { get; set; }
        public string timePicker { get; set; }
    }

    public class KeyboardShortcuts
    {
        public string confirmCloseOrderModal { get; set; }
        public string cancelCloseOrderModal { get; set; }
        public string finishFirstOrder { get; set; }
        public string finishSecondOrder { get; set; }
        public string finishThirdOrder { get; set; }
        public string finishFourthOrder { get; set; }
        public string finishFifthOrder { get; set; }
        public string undo { get; set; }
        public string nextPage { get; set; }
        public string previousPage { get; set; }
        public string resetTime { get; set; }
        public string firstPage { get; set; }
        public string secondPage { get; set; }
        public string middlePage { get; set; }
        public string penultimatePage { get; set; }
        public string lastPage { get; set; }
        public string power { get; set; }
        public string exit { get; set; }
    }

    public class Preference
    {
        public FinishOrder finishOrder { get; set; }
        public ClientData clientData { get; set; }
        public SourceBox sourceBox { get; set; }
        public ShowName showName { get; set; }
        public ShowIdentifier showIdentifier { get; set; }
        public ShowNumerator showNumerator { get; set; }
        public ShowPagination showPagination { get; set; }
        public KeyboardShortcuts keyboardShortcuts { get; set; }
    }

    public class ConfigPantalla
    {
        public Appearance appearance { get; set; }
        public Preference preference { get; set; }
    }

    public class ShowIdentifier
    {
        public bool active { get; set; }
        public string message { get; set; }
    }

    public class ShowName
    {
        public bool active { get; set; }
    }

    public class ShowNumerator
    {
        public bool active { get; set; }
    }

    public class ShowPagination
    {
        public bool active { get; set; }
    }

    public class SourceBox
    {
        public bool active { get; set; }
        public string message { get; set; }
    }
}
