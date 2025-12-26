package strips

import (
	"database/sql"
	"encoding/json"
	"errors"
	"fmt"
	"html"
	"lib-shared/protos/lib_gen_proto"
	"log"
	"new-order-store/internals/entity/maxpoint"
	"new-order-store/internals/infrastructure/sqlserver"
	"strconv"
	"strings"
	"time"
)

type AutomaticCoupon struct {
}

type Cupon struct {
	Tipo        string   `json:"tipo"`
	Descripcion string   `json:"descripcion"`
	Html        string   `json:"html"`
	Stores      []string `json:"stores"`
	Activo      bool     `json:"activo"`
	Rules       struct {
		InicioVigencia       string `json:"inicio_vigencia"`
		FinVigencia          string `json:"fin_vigencia"`
		MontoMinimo          string `json:"monto_minimo"`
		ProductoEspecifico   string `json:"producto_especifico"`
		FrecuenciaBeneficio  *int   `json:"frecuencia_beneficio"`
		ImpresionObligatoria bool   `json:"impresion_obligatoria"`
	} `json:"rules"`
	Alfanumerico   string `json:"alfanumerico,omitempty"`
	Beneficio      string `json:"beneficio,omitempty"`
	ProductoRegalo string `json:"producto_regalo,omitempty"`
	ID             int    `json:"id"`
}

type FrecuenciaCupon struct {
	Tipo             string `json:"tipo"`
	ID               int    `json:"id"`
	FrecuenciaActual *int   `json:"frecuencia_actual"`
}

type DynamicPromotionObject struct {
	Tipo string `json:"tipo"`
	Html string `json:"contenido"`
}

type CollectionData struct {
	ID          string
	Descripcion string
	lastUser    string
}

func NewAutomaticCoupon() *AutomaticCoupon {
	return &AutomaticCoupon{}
}

func (o *AutomaticCoupon) AutomaticCoupons(connection *sqlserver.DatabaseSql, storeData *maxpoint.StoreData, order *lib_gen_proto.Order) ([]DynamicPromotionObject, error) {
	//VALIDAR POLITICA Y OBTENER ID_COLECCION CADENA (SI NO EXISTE O NO ESTA ACTIVA NO SE REGRESA CUPON AUTOMATICO)
	query := `SELECT CAST(ID_ColeccionCadena AS VARCHAR(255)) AS ID_ColeccionCadena FROM ColeccionCadena 
	          WHERE Descripcion = @descripcion AND isActive = 1 AND cdn_id = @chain`
	row := connection.QueryRow(query, sql.Named("descripcion", "PROMOCIONES BENEFICIOS"), sql.Named("chain", storeData.ChainId))
	var idColeccionCadena string
	err := row.Scan(&idColeccionCadena)
	if err != nil {
		if errors.Is(err, sql.ErrNoRows) {
			return nil, fmt.Errorf("politica PROMOCIONES BENEFICIOS no existe o no esta activa: %w", err)
		}
		return nil, fmt.Errorf("error ejecutando query: %w", err)
	}
	//FIN VALIDACION

	//Inicio de obtener ids de coleccion de datos cadena
	var arrayCoupons []DynamicPromotionObject
	query2 := `SELECT CAST(ID_ColeccionDeDatosCadena AS VARCHAR(255)), Descripcion, CAST(lastUser AS VARCHAR(255)) FROM ColeccionDeDatosCadena WHERE ID_ColeccionCadena = @id AND isActive = 1`
	rows, err := connection.Query(query2, sql.Named("id", idColeccionCadena))
	if err != nil {
		return nil, fmt.Errorf("error obteniendo colecciones de datos: %w", err)
	}
	defer rows.Close()

	collections := make([]*CollectionData, 0)

	for rows.Next() {
		c := &CollectionData{}
		if err := rows.Scan(&c.ID, &c.Descripcion, &c.lastUser); err != nil {
			return nil, fmt.Errorf("error escaneando coleccion de datos: %w", err)
		}
		collections = append(collections, c)
	}
	//Fin obtener ids de coleccion de datos cadena

	if len(collections) == 0 {
		return nil, fmt.Errorf("no se encontraron los parametros correspondientes de politica PROMOCIONES BENEFICIOS")
	}
	//Comprobar que la politica de frecuencias existe, si es asi comprobar si esta llena o no, lo mismo con reinicio
	var variableVFrecuencia string
	frecuenciasMap := make(map[string]FrecuenciaCupon)
	hasContadorFrecuencias := false
	reiniciar_frecuencia := false
	for _, collection := range collections {
		if collection.Descripcion == "CONTADOR FRECUENCIAS" {
			hasContadorFrecuencias = true
			queryVar := `SELECT variableV FROM CadenaColeccionDeDatos WHERE ID_ColeccionDeDatosCadena = @idcddc AND ID_ColeccionCadena = @idcc`
			row := connection.QueryRow(queryVar,
				sql.Named("idcddc", collection.ID),
				sql.Named("idcc", idColeccionCadena),
			)
			err := row.Scan(&variableVFrecuencia)
			if err != nil {
				if !errors.Is(err, sql.ErrNoRows) {
					return nil, fmt.Errorf("error obteniendo variableV: %w", err)

				}
				//Si existe la politica pero no su dato se crea con un "{}"
				query := `INSERT INTO CadenaColeccionDeDatos (ID_ColeccionDeDatosCadena,ID_ColeccionCadena,cdn_id,variableV,replica,isActive,lastUser,mdl_id,lastUpdate)
						VALUES (@v1, @v2, @v3, @v4, @v5, @v6, @v7, @v8, GETDATE() )`
				_, err := connection.Exec(query,
					sql.Named("v1", collection.ID),
					sql.Named("v2", idColeccionCadena),
					sql.Named("v3", 10),
					sql.Named("v4", "{}"),
					sql.Named("v5", 0),
					sql.Named("v6", 1),
					sql.Named("v7", collection.lastUser),
					sql.Named("v8", 1),
				)
				if err != nil {
					return nil, fmt.Errorf("error al insertar datos: %w", err)
				}
				variableVFrecuencia = "{}"
			}
			if variableVFrecuencia == "" {
				variableVFrecuencia = "{}"
			}
			err = json.Unmarshal([]byte(variableVFrecuencia), &frecuenciasMap)
			if err != nil {
				return nil, fmt.Errorf("error parseando JSON de frecuencias: %w", err)
			}
		}
		if collection.Descripcion == "REINICIAR CONTADOR" {
			query := `SELECT variableB FROM CadenaColeccionDeDatos WHERE ID_ColeccionDeDatosCadena = @idcddc AND ID_ColeccionCadena = @idcc`
			row := connection.QueryRow(query,
				sql.Named("idcddc", collection.ID),
				sql.Named("idcc", idColeccionCadena),
			)
			var variableB sql.NullBool
			err := row.Scan(&variableB)
			if err != nil {
				if errors.Is(err, sql.ErrNoRows) {
					return nil, fmt.Errorf("no se ha encontrado la política de REINICIAR CONTADOR")
				}
				return nil, fmt.Errorf("error al ejecutar query: %w", err)
			}
			if variableB.Valid {
				if variableB.Bool {
					reiniciar_frecuencia = true
				}
			}
		}
	}

	if !hasContadorFrecuencias {
		return nil, fmt.Errorf("no está configurada o activada la política de CONTADOR FRECUENCIAS")
	}

	//Obtener datos de variableV
	for _, collection := range collections {
		queryVar := `SELECT variableV FROM CadenaColeccionDeDatos WHERE ID_ColeccionDeDatosCadena = @idcddc AND ID_ColeccionCadena = @idcc AND isActive = 1`
		row := connection.QueryRow(queryVar,
			sql.Named("idcddc", collection.ID),
			sql.Named("idcc", idColeccionCadena),
		)

		var cupones []Cupon
		var variableV string
		err := row.Scan(&variableV)
		if err != nil {
			if errors.Is(err, sql.ErrNoRows) {
				continue
			}
			return nil, fmt.Errorf("error obteniendo variableV: %w", err)
		}

		//Parsear el JSON de variableV
		if collection.Descripcion != "CONTADOR FRECUENCIAS" && collection.Descripcion != "REINICIAR CONTADOR" {
			if err := json.Unmarshal([]byte(variableV), &cupones); err != nil {
				return nil, fmt.Errorf("error parseando JSON de cupones: %w", err)
			}
		}

		// Validar cada cupón
		for _, cupon := range cupones {
			clave := fmt.Sprintf("%d-%s", cupon.ID, cupon.Tipo)
			var imprimir = false
			if !cupon.Activo {
				if reiniciar_frecuencia {
					if _, existe := frecuenciasMap[clave]; existe {
						delete(frecuenciasMap, clave)
					}
				}
				continue
			}

			// Validar fechas
			loc := time.Now().Location()
			layout := "2006-01-02"
			inicio, errInicio := time.ParseInLocation(layout, cupon.Rules.InicioVigencia, loc)
			fin, errFin := time.ParseInLocation(layout, cupon.Rules.FinVigencia, loc)
			if errInicio != nil || errFin != nil {
				continue
			}
			now := time.Now().In(loc)
			ahora := time.Date(now.Year(), now.Month(), now.Day(), 0, 0, 0, 0, loc)
			if ahora.Before(inicio) || ahora.After(fin) {
				continue
			}

			//Validar tienda
			aplicaTienda := false
			for _, tienda := range cupon.Stores {
				if tienda == "todos" {
					aplicaTienda = true
					break
				}
				id, err := strconv.Atoi(tienda)
				if err != nil {
					continue
				}
				if id == storeData.RestaurantId {
					aplicaTienda = true
					break
				}
			}
			if !aplicaTienda {
				continue
			}

			if !cupon.Rules.ImpresionObligatoria {
				// Producto específico
				if cupon.Rules.ProductoEspecifico != "" {
					encontrado := false
					for _, details := range order.Items.Product {
						productIdStr := strconv.FormatUint(uint64(details.ProductId), 10)
						if productIdStr == cupon.Rules.ProductoEspecifico {
							encontrado = true
							break
						}
					}
					if !encontrado {
						continue
					}
				}

				// Monto mínimo
				if cupon.Rules.MontoMinimo != "" {
					monto_minimo, err := strconv.ParseFloat(cupon.Rules.MontoMinimo, 64)
					if err != nil {
						continue
					}

					total_pago, err := strconv.ParseFloat(order.PaymentInfo.Total, 64)
					if err != nil {
						continue
					}

					if total_pago < monto_minimo {
						continue
					}
				}

				if cupon.Rules.FrecuenciaBeneficio != nil {
					frecuencia, ok := frecuenciasMap[clave]

					if ok {
						if frecuencia.FrecuenciaActual == nil {
							frecuencia.FrecuenciaActual = new(int)
						}
						*frecuencia.FrecuenciaActual += 1
						if !(*frecuencia.FrecuenciaActual%*cupon.Rules.FrecuenciaBeneficio == 0) {
							continue
						}
					} else {
						frecuencia = FrecuenciaCupon{
							Tipo:             cupon.Tipo,
							ID:               cupon.ID,
							FrecuenciaActual: new(int),
						}
						*frecuencia.FrecuenciaActual = 1
						if !(*frecuencia.FrecuenciaActual%*cupon.Rules.FrecuenciaBeneficio == 0) {
							continue
						}
					}
					frecuenciasMap[clave] = frecuencia
				} else {
					if reiniciar_frecuencia {
						clave := fmt.Sprintf("%d-%s", cupon.ID, cupon.Tipo)
						if _, existe := frecuenciasMap[clave]; existe {
							delete(frecuenciasMap, clave)
						}
					}
				}
				imprimir = true

			} else {
				imprimir = true
			}

			if imprimir {
				htmlCupon := cupon.Html
				if strings.Contains(htmlCupon, ":promocion") {
					switch strings.ToLower(cupon.Tipo) {
					case "regalo":
						htmlCupon = strings.ReplaceAll(htmlCupon, ":promocion", cupon.ProductoRegalo)
					case "alfanumerico":
						htmlCupon = strings.ReplaceAll(htmlCupon, ":promocion", cupon.Alfanumerico)
					}
				}
				htmlCupon = strings.ReplaceAll(htmlCupon, ":fechai", strings.ReplaceAll(cupon.Rules.InicioVigencia, "-", "/"))
				htmlCupon = strings.ReplaceAll(htmlCupon, ":fechaf", strings.ReplaceAll(cupon.Rules.FinVigencia, "-", "/"))
				arrayCoupons = append(arrayCoupons, DynamicPromotionObject{
					Tipo: cupon.Tipo,
					Html: html.UnescapeString(htmlCupon),
				})
			}
		}
	}

	// Serializar frecuenciasMap a JSON
	jsonFrecuencias, err := json.Marshal(frecuenciasMap)
	if err != nil {
		log.Fatal("Error serializando frecuencias:", err)
	}
	newVariableV := string(jsonFrecuencias)

	// Actualizar la base de datos
	for _, collection := range collections {
		if collection.Descripcion == "CONTADOR FRECUENCIAS" {
			query := `UPDATE CadenaColeccionDeDatos SET variableV = @v1, lastUser = @v2,
                  lastUpdate = GETDATE() WHERE ID_ColeccionDeDatosCadena = @w1 AND ID_ColeccionCadena = @w2`
			_, err := connection.Exec(query,
				sql.Named("v1", newVariableV),
				sql.Named("v2", collection.lastUser),
				sql.Named("w1", collection.ID),
				sql.Named("w2", idColeccionCadena),
			)
			if err != nil {
				return nil, fmt.Errorf("error al actualizar contador de frecuencias: %w", err)
			}
		}
		if collection.Descripcion == "REINICIAR CONTADOR" && reiniciar_frecuencia {
			query := `UPDATE CadenaColeccionDeDatos SET variableB = @v1, lastUser = @v2,
                  lastUpdate = GETDATE() WHERE ID_ColeccionDeDatosCadena = @w1 AND ID_ColeccionCadena = @w2`
			_, err := connection.Exec(query,
				sql.Named("v1", nil),
				sql.Named("v2", collection.lastUser),
				sql.Named("w1", collection.ID),
				sql.Named("w2", idColeccionCadena),
			)
			if err != nil {
				return nil, fmt.Errorf("error al actualizar politica de reiniciar contador: %w", err)
			}
		}
	}

	return arrayCoupons, nil
}
