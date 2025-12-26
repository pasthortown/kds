package business

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io"
	"lib-shared/protos/lib_gen_proto"
	"lib-shared/utils/logger"
	"net/http"
	"new-order-store/internals/entity/maxpoint"
	"new-order-store/internals/infrastructure/sqlserver"
	"strings"
	"time"
)

// KDSPolicies contiene las políticas de configuración del KDS Regional
type KDSPolicies struct {
	URL                  string
	Email                string
	Password             string
	CanalesExcluidos     string
	Activo               bool
	ImpresionTiempoReal  bool
}

// KDSAuthResponse respuesta de autenticación del KDS
type KDSAuthResponse struct {
	AccessToken  string `json:"accessToken"`
	RefreshToken string `json:"refreshToken"`
	User         struct {
		UserId string `json:"userId"`
		Email  string `json:"email"`
		Role   string `json:"role"`
	} `json:"user"`
}

// KDSComanda estructura de la comanda para enviar al KDS
type KDSComanda struct {
	ID           string            `json:"id"`
	OrderID      string            `json:"orderId"`
	CreatedAt    string            `json:"createdAt"`
	Channel      KDSChannel        `json:"channel"`
	CashRegister KDSCashRegister   `json:"cashRegister"`
	Customer     *KDSCustomer      `json:"customer,omitempty"`
	Products     []KDSProduct      `json:"products"`
	OtrosDatos   *KDSOtrosDatos    `json:"otrosDatos,omitempty"`
	Comments     string            `json:"comments,omitempty"`
	StatusPos    string            `json:"statusPos,omitempty"`
}

// KDSChannel canal de la orden
type KDSChannel struct {
	ID   int    `json:"id"`
	Name string `json:"name"`
	Type string `json:"type"`
}

// KDSCashRegister información de la caja
type KDSCashRegister struct {
	Cashier string `json:"cashier"`
	Name    string `json:"name"`
}

// KDSCustomer información del cliente
type KDSCustomer struct {
	Name string `json:"name"`
}

// KDSProduct producto de la orden
type KDSProduct struct {
	ProductID string   `json:"productId,omitempty"`
	Name      string   `json:"name"`
	Amount    int      `json:"amount"`
	Category  string   `json:"category,omitempty"`
	Content   []string `json:"content,omitempty"`
	Modifier  string   `json:"modifier,omitempty"`
	Comments  string   `json:"comments,omitempty"`
}

// KDSOtrosDatos datos adicionales de la orden
type KDSOtrosDatos struct {
	Turno      interface{} `json:"turno,omitempty"`
	NroCheque  string      `json:"nroCheque,omitempty"`
	LlamarPor  string      `json:"llamarPor,omitempty"`
	Fecha      string      `json:"Fecha,omitempty"`
	Direccion  string      `json:"Direccion,omitempty"`
}

// KDSRegionalService servicio para integración con KDS Regional
type KDSRegionalService struct {
	connection   *sqlserver.DatabaseSql
	storeData    *maxpoint.StoreData
	order        *lib_gen_proto.Order
	idEstacion   string
	cfacId       string
	cashierName  string
}

// NewKDSRegionalService crea una nueva instancia del servicio KDS Regional
func NewKDSRegionalService(
	connection *sqlserver.DatabaseSql,
	storeData *maxpoint.StoreData,
	order *lib_gen_proto.Order,
	idEstacion string,
	cfacId string,
	cashierName string,
) *KDSRegionalService {
	return &KDSRegionalService{
		connection:  connection,
		storeData:   storeData,
		order:       order,
		idEstacion:  idEstacion,
		cfacId:      cfacId,
		cashierName: cashierName,
	}
}

// SendOrderToKDS envía la orden al KDS Regional si las políticas lo permiten
func (s *KDSRegionalService) SendOrderToKDS() error {
	// 1. Obtener políticas del KDS
	policies, err := s.GetKDSPolicies()
	if err != nil {
		logger.Warning.Printf("[order.kds.regional.go] Error al obtener políticas KDS: %v", err)
		return nil // No retornar error para no afectar el flujo principal
	}

	// 2. Validar si está activo
	if !policies.Activo {
		logger.Info.Printf("[order.kds.regional.go] KDS Regional no está activo para esta estación")
		return nil
	}

	// 3. Validar URL
	if policies.URL == "" {
		logger.Warning.Printf("[order.kds.regional.go] URL del KDS no configurada")
		return nil
	}

	// 4. Validar canal excluido
	channelName := s.getChannelName()
	if s.isChannelExcluded(channelName, policies.CanalesExcluidos) {
		logger.Info.Printf("[order.kds.regional.go] Canal '%s' está excluido del KDS", channelName)
		return nil
	}

	// 5. Autenticar con el KDS
	token, err := s.authenticate(policies)
	if err != nil {
		logger.Error.Printf("[order.kds.regional.go] Error de autenticación con KDS: %v", err)
		return nil
	}

	// 6. Construir y enviar la comanda
	comanda := s.buildComanda()
	err = s.sendComanda(policies.URL, token, comanda)
	if err != nil {
		logger.Error.Printf("[order.kds.regional.go] Error al enviar comanda al KDS: %v", err)
		return nil
	}

	logger.Info.Printf("[order.kds.regional.go] Orden %s enviada exitosamente al KDS", s.cfacId)
	return nil
}

// GetKDSPolicies obtiene las políticas del KDS desde la base de datos
func (s *KDSRegionalService) GetKDSPolicies() (*KDSPolicies, error) {
	policies := &KDSPolicies{}

	// Ejecutar el SP sp_GetPoliticasKDSRegional
	spPolicies := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "sp_GetPoliticasKDSRegional")
	spPolicies.AddValueParameterized("rst_id", s.storeData.RestaurantId)
	spPolicies.AddValueParameterized("IDEstacion", s.idEstacion)

	rows, err := s.connection.IQuery(spPolicies.GetStoreProcedure(), spPolicies.GetValues())
	if err != nil {
		return nil, fmt.Errorf("error al ejecutar sp_GetPoliticasKDSRegional: %v", err)
	}
	defer rows.Close()

	for rows.Next() {
		var url, email, password, canalesExcluidos *string
		var activo, impresionTiempoReal *bool

		err = rows.Scan(&url, &email, &password, &canalesExcluidos, &activo, &impresionTiempoReal)
		if err != nil {
			return nil, fmt.Errorf("error al leer resultado de sp_GetPoliticasKDSRegional: %v", err)
		}

		if url != nil {
			policies.URL = *url
		}
		if email != nil {
			policies.Email = *email
		}
		if password != nil {
			policies.Password = *password
		}
		if canalesExcluidos != nil {
			policies.CanalesExcluidos = *canalesExcluidos
		}
		if activo != nil {
			policies.Activo = *activo
		}
		if impresionTiempoReal != nil {
			policies.ImpresionTiempoReal = *impresionTiempoReal
		}
	}

	return policies, nil
}

// authenticate realiza la autenticación con el KDS y retorna el token
func (s *KDSRegionalService) authenticate(policies *KDSPolicies) (string, error) {
	loginURL := strings.TrimRight(policies.URL, "/") + "/api/auth/login"

	loginData := map[string]string{
		"email":    policies.Email,
		"password": policies.Password,
	}

	jsonData, err := json.Marshal(loginData)
	if err != nil {
		return "", fmt.Errorf("error al serializar datos de login: %v", err)
	}

	client := &http.Client{Timeout: 30 * time.Second}
	req, err := http.NewRequest("POST", loginURL, bytes.NewBuffer(jsonData))
	if err != nil {
		return "", fmt.Errorf("error al crear request de login: %v", err)
	}

	req.Header.Set("Content-Type", "application/json")

	resp, err := client.Do(req)
	if err != nil {
		return "", fmt.Errorf("error al ejecutar request de login: %v", err)
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		body, _ := io.ReadAll(resp.Body)
		return "", fmt.Errorf("error de autenticación (status %d): %s", resp.StatusCode, string(body))
	}

	var authResponse KDSAuthResponse
	if err := json.NewDecoder(resp.Body).Decode(&authResponse); err != nil {
		return "", fmt.Errorf("error al decodificar respuesta de login: %v", err)
	}

	return authResponse.AccessToken, nil
}

// buildComanda construye el objeto KDSComanda a partir de la orden
func (s *KDSRegionalService) buildComanda() *KDSComanda {
	comanda := &KDSComanda{
		ID:        s.cfacId,
		OrderID:   s.cfacId,
		CreatedAt: time.Now().UTC().Format(time.RFC3339),
		Channel: KDSChannel{
			ID:   1,
			Name: s.getChannelName(),
			Type: s.getChannelType(),
		},
		CashRegister: KDSCashRegister{
			Cashier: s.cashierName,
			Name:    "Kiosko",
		},
		Products:  s.buildProducts(),
		StatusPos: "PEDIDO TOMADO",
	}

	// Agregar cliente si existe
	if s.order.Cabecera != nil && s.order.Cabecera.Client != nil {
		clientName := s.order.Cabecera.Client.Name
		if clientName != "" {
			comanda.Customer = &KDSCustomer{Name: clientName}
		}
	}

	// Agregar otros datos
	comanda.OtrosDatos = &KDSOtrosDatos{
		NroCheque: s.cfacId,
		LlamarPor: s.getClientName(),
	}

	return comanda
}

// buildProducts construye la lista de productos para el KDS
func (s *KDSRegionalService) buildProducts() []KDSProduct {
	var products []KDSProduct

	if s.order.Items == nil || s.order.Items.Product == nil {
		return products
	}

	for _, item := range s.order.Items.Product {
		product := KDSProduct{
			ProductID: fmt.Sprintf("%d", item.ProductId),
			Name:      item.NameProduct,
			Amount:    int(item.Quantity),
		}

		// Agregar modificadores como content
		var modifiers []string
		if item.ModifierGroups != nil {
			for _, mod := range item.ModifierGroups {
				modifiers = append(modifiers, fmt.Sprintf("*%s x%d", mod.NameProduct, mod.Quantity))
			}
		}
		if len(modifiers) > 0 {
			product.Content = modifiers
		}

		products = append(products, product)

		// Agregar modificadores como productos separados (opcional)
		if item.ModifierGroups != nil {
			for _, mod := range item.ModifierGroups {
				modProduct := KDSProduct{
					ProductID: fmt.Sprintf("%d", mod.ProductId),
					Name:      mod.NameProduct,
					Amount:    int(mod.Quantity),
					Modifier:  fmt.Sprintf("Modificador de %s", item.NameProduct),
				}
				products = append(products, modProduct)
			}
		}
	}

	return products
}

// sendComanda envía la comanda al KDS via HTTP POST
func (s *KDSRegionalService) sendComanda(baseURL, token string, comanda *KDSComanda) error {
	ticketURL := strings.TrimRight(baseURL, "/") + "/api/tickets/receive"

	jsonData, err := json.Marshal(comanda)
	if err != nil {
		return fmt.Errorf("error al serializar comanda: %v", err)
	}

	client := &http.Client{Timeout: 30 * time.Second}
	req, err := http.NewRequest("POST", ticketURL, bytes.NewBuffer(jsonData))
	if err != nil {
		return fmt.Errorf("error al crear request: %v", err)
	}

	req.Header.Set("Content-Type", "application/json")
	req.Header.Set("Authorization", "Bearer "+token)

	resp, err := client.Do(req)
	if err != nil {
		return fmt.Errorf("error al enviar comanda: %v", err)
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		body, _ := io.ReadAll(resp.Body)
		return fmt.Errorf("error al crear orden en KDS (status %d): %s", resp.StatusCode, string(body))
	}

	return nil
}

// isChannelExcluded verifica si el canal está en la lista de excluidos
func (s *KDSRegionalService) isChannelExcluded(channel, excludedChannels string) bool {
	if excludedChannels == "" {
		return false
	}

	excluded := strings.Split(excludedChannels, ",")
	channelUpper := strings.ToUpper(strings.TrimSpace(channel))

	for _, exc := range excluded {
		if strings.ToUpper(strings.TrimSpace(exc)) == channelUpper {
			return true
		}
	}

	return false
}

// getChannelName obtiene el nombre del canal basado en el tipo de servicio
func (s *KDSRegionalService) getChannelName() string {
	if s.order.Cabecera == nil {
		return "KIOSKO"
	}

	typeService := strings.ToUpper(s.order.Cabecera.TypeService)
	paymentType := strings.ToUpper(s.order.Cabecera.PaymentType)

	// Determinar el nombre del canal
	if strings.Contains(paymentType, "EFECTIVO") {
		return "EFECTIVO"
	}
	if strings.Contains(paymentType, "TARJETA") {
		return "TARJETA"
	}

	// Por defecto según tipo de servicio
	switch typeService {
	case "LLEVAR", "PARA LLEVAR":
		return "Kiosko-Llevar"
	case "SERVIRSE", "COMER AQUI":
		return "Kiosko-Servirse"
	default:
		return "Kiosko"
	}
}

// getChannelType obtiene el tipo de canal
func (s *KDSRegionalService) getChannelType() string {
	if s.order.Cabecera == nil {
		return "LOCAL"
	}

	typeService := strings.ToUpper(s.order.Cabecera.TypeService)

	switch typeService {
	case "LLEVAR", "PARA LLEVAR":
		return "LLEVAR"
	case "SERVIRSE", "COMER AQUI":
		return "SALON"
	default:
		return "LOCAL"
	}
}

// getClientName obtiene el nombre del cliente
func (s *KDSRegionalService) getClientName() string {
	if s.order.Cabecera != nil && s.order.Cabecera.Client != nil {
		return s.order.Cabecera.Client.Name
	}
	return ""
}

// SendOrderToKDSAsync envía la orden al KDS de forma asíncrona (no bloquea el flujo principal)
func (s *KDSRegionalService) SendOrderToKDSAsync() {
	go func() {
		if err := s.SendOrderToKDS(); err != nil {
			logger.Error.Printf("[order.kds.regional.go] Error en envío asíncrono al KDS: %v", err)
		}
	}()
}
