package business

import (
	"lib-shared/protos/lib_gen_proto"
	"new-order-store/internals/domain/business/argentina"
	"new-order-store/internals/domain/business/chile"
	"new-order-store/internals/domain/business/colombia"
	"new-order-store/internals/domain/business/ecuador"
	"new-order-store/internals/domain/business/venezuela"
	"new-order-store/internals/domain/execute"
	"new-order-store/internals/entity/enums/country"
	"new-order-store/internals/entity/maxpoint"
	"new-order-store/internals/entity/maxpoint/credential"
	"new-order-store/internals/entity/models"
	"new-order-store/internals/infrastructure/featureflag"
	"new-order-store/internals/infrastructure/grpc_foliador"
	"new-order-store/internals/infrastructure/natsmodule"
	"new-order-store/internals/infrastructure/natsmodulefolder"
	regional_kiosk "new-order-store/internals/infrastructure/regional-kiosk"
	"new-order-store/internals/infrastructure/services"
)

type OrderDispatcher struct {
	DatabaseCredential     *credential.DatabaseCredential
	Country                country.Country
	StoreData              *maxpoint.StoreData
	FeatureFlag            *featureflag.FeatureFlag
	NatsClient             *natsmodule.NatsStarter
	NatsFolder             *natsmodulefolder.NatsStarter
	ServicesChannel        *services.ChannelManager
	FoliadorService        *grpc_foliador.GrpcServiceFolio
	natsThirdPartyServices *natsmodulefolder.NatsStarter
	dataStrip              *models.EnvDataTirillas
	urlApiTurnero          *models.EnvDataTurnero
}

func NewOrderDispatcher(
	DatabaseCredential *credential.DatabaseCredential,
	country country.Country,
	data *maxpoint.StoreData,
	featureFlag *featureflag.FeatureFlag,
	natsClient *natsmodule.NatsStarter,
	NatsFolder *natsmodulefolder.NatsStarter,
	FoliadorService *grpc_foliador.GrpcServiceFolio,
	ServicesChannel *services.ChannelManager,
	natsThirdPartyServices *natsmodulefolder.NatsStarter,
	dataStrip *models.EnvDataTirillas,
	urlApiTurnero *models.EnvDataTurnero,
) OrderDispatcher {
	return OrderDispatcher{
		DatabaseCredential:     DatabaseCredential,
		Country:                country,
		StoreData:              data,
		FeatureFlag:            featureFlag,
		NatsClient:             natsClient,
		NatsFolder:             NatsFolder,
		FoliadorService:        FoliadorService,
		ServicesChannel:        ServicesChannel,
		natsThirdPartyServices: natsThirdPartyServices,
		dataStrip:              dataStrip,
		urlApiTurnero:          urlApiTurnero,
	}
}
func (receiver *OrderDispatcher) DispatchOrder(order *lib_gen_proto.Order) execute.OrderExecutorSql {
	/*if receiver.Country == country.ECUADOR {
		return ecuador.NewOrderStore(receiver.DatabaseCredential, order, receiver.StoreData, receiver.FeatureFlag, receiver.NatsClient)
	}*/
	regionalKiosk := regional_kiosk.NewRegionalKiosk(receiver.natsThirdPartyServices, order, receiver.Country, receiver.StoreData, receiver.urlApiTurnero)
	switch receiver.Country {
	case country.ARGENTINA:
		return argentina.NewOrderStore(receiver.DatabaseCredential, order, receiver.StoreData, receiver.FeatureFlag, receiver.NatsClient, receiver.NatsFolder, receiver.FoliadorService, regionalKiosk)
	case country.CHILE:
		return chile.NewOrderStore(receiver.DatabaseCredential, order, receiver.StoreData, receiver.FeatureFlag, receiver.NatsClient, receiver.NatsFolder, receiver.ServicesChannel, regionalKiosk)
	case country.COLOMBIA:
		return colombia.NewOrderStore(receiver.DatabaseCredential, order, receiver.StoreData, receiver.FeatureFlag, receiver.NatsClient, regionalKiosk)
	case country.ECUADOR:
		return ecuador.NewOrderStore(receiver.DatabaseCredential, order, receiver.StoreData, receiver.FeatureFlag, receiver.NatsClient, receiver.dataStrip, regionalKiosk)
	case country.VENEZUELA:
		return venezuela.NewOrderStore(receiver.DatabaseCredential, order, receiver.StoreData, receiver.FeatureFlag, receiver.NatsClient, regionalKiosk)
	default:
		return ecuador.NewOrderStore(receiver.DatabaseCredential, order, receiver.StoreData, receiver.FeatureFlag, receiver.NatsClient, receiver.dataStrip, regionalKiosk)
	}
	//return nil
}
