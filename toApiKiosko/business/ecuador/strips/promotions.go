package strips

import (
	"fmt"
	"lib-shared/protos/lib_gen_proto"
	"lib-shared/utils"
	"new-order-store/internals/entity/models"
	"new-order-store/internals/infrastructure/sqlserver"
	"new-order-store/internals/utils/validatorsql"
	"strings"
	"time"
)

type Promotion struct {
	Strip *TotpStrip
}

func NewPromotion(totpStrip *models.EnvDataTirillas) *Promotion {
	strip := NewTotpStrip(totpStrip)
	return &Promotion{Strip: strip}
}

func (o *Promotion) Promotions(connection *sqlserver.DatabaseSql, items *lib_gen_proto.Items, cdnId int) ([]*models.ResponsePromotionsKioskos, error) {
	arrayPromotions := make([]*models.ResponsePromotionsKioskos, 0)
	var dataStrip models.TirillasPromotionsKiosko

	objectExits := validatorsql.ObjectExitsDb(connection, "PROCEDURE", "CONFIG", "ObtenerConfiguracionTirillaPromocionKiosko")
	if !objectExits {
		return arrayPromotions, fmt.Errorf("[promociones]Error, el objeto config.ObtenerConfiguracionTirillaPromocionKiosko no existe, por favor revisar\n")
	}

	for _, product := range items.Product {
		dataTemporalPromotions := &models.ResponsePromotionsKioskos{}
		configStrig := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[config].[ObtenerConfiguracionTirillaPromocionKiosko]")
		configStrig.AddValueParameterized("cadena", cdnId)
		configStrig.AddValueParameterized("plu_producto", product.ProductId)

		rows, err := connection.IQuery(configStrig.GetStoreProcedure(), configStrig.GetValues())
		nameColumns, _ := rows.Columns()
		numColumns := len(nameColumns)
		for rows.Next() {
			var dateValidityFrom time.Time
			var dateValidityUntil time.Time
			if numColumns == 1 {
				err = rows.Scan(&dataStrip.Message)
				if err != nil {
					return nil, fmt.Errorf("[promociones]Error al obtener los datos del sp [config].[ObtenerConfiguracionTirillaPromocionKiosko] con pluId %v - %v\n:", product.ProductId, err)
				}
			} else {
				err = rows.Scan(
					&dataStrip.ProductPromotions,
					&dataStrip.RedemptionChannels,
					&dateValidityFrom,
					&dateValidityUntil,
					&dataStrip.Cities,
					&dataStrip.Title,
					&dataStrip.AppliesTitleBold,
					&dataStrip.Subtitle,
					&dataStrip.AppliessubtitleBold,
					&dataStrip.ProductDescription,
					&dataStrip.ProductPrice,
				)
				if err != nil {
					return nil, fmt.Errorf("[promociones]Error al obtener los datos del sp [config].[ObtenerConfiguracionTirillaPromocionKiosko] con pluId %v - %v\n:", product.ProductId, err)
				}
				dataStrip.TempValidityUntil = dateValidityUntil.Format("2006-01-02 15:04:05.000")
				dataStrip.TempValidityFrom = dateValidityFrom.Format("2006-01-02 15:04:05.000")
				dataStrip.ValidityUntil = dateValidityUntil.Format("2006-01-02 15:04")
				dataStrip.ValidityFrom = dateValidityFrom.Format("2006-01-02 15:04")
			}

		}
		if dataStrip.Message == nil && !utils.IsEmpty(dataStrip.ProductPromotions) {
			dataSentTotpStrip, err := o.Strip.CreateTotp(&dataStrip)
			if err != nil {
				return nil, fmt.Errorf("[promociones]error al crear el tirillas de promocion: %v", err)
			}
			dataRedeemable := strings.Split(dataSentTotpStrip.RedemptionChannels, ",")
			for i, data := range dataRedeemable {
				dataRedeemable[i] = strings.TrimSpace(data)
			}
			dataTemporalPromotions.Title = dataSentTotpStrip.Title
			dataTemporalPromotions.Subtitle = dataSentTotpStrip.Subtitle
			dataTemporalPromotions.Code = dataSentTotpStrip.Code
			dataTemporalPromotions.Redeemable = dataRedeemable
			dataTemporalPromotions.Product.ProductName = dataSentTotpStrip.ProductDescription
			dataTemporalPromotions.Product.Price = dataSentTotpStrip.ProductPrice
			dataTemporalPromotions.Validity.From = dataSentTotpStrip.ValidityFrom
			dataTemporalPromotions.Validity.To = dataSentTotpStrip.ValidityUntil
			arrayPromotions = append(arrayPromotions, dataTemporalPromotions)
		}

	}
	return arrayPromotions, nil
}
