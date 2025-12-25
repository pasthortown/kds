/* /////////////////////////////////////////////////////////////////////////////////////////////
///////FECHA CREACION: 02/09/2015 //////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Jorge Tinoco //////////////////////////////////////////////////////////
///////DESCRIPCION: Carga Orden Pedido Pendiente ///////////////////////////////////////////////
///////TABLAS: Menu, Precio_Plu, Menu_Agrupacion, Plus, Menu_AgrupacionProducto ////////////////
///////////////////////////////////////////////////////////////////////////////////////////// */
-- =================================================
-- modificado por		    : Mychael Castro
-- Fecha de creacion	    : 12:53 26/4/2017
-- Descripción				: Add campo dop_cuenta al Query para el split de cuenta 
-- Tablas involucradas		:    
-- ================================================= 
-- modificado por		: Daniel Llerena
-- Fecha de creacion	: 22/08/2018
-- Descripción			: Se agrega los campos tipoBeneficioCupon, colorBeneficioCupon los mismo que son utilizados
--						: para colorear el producto beneficiado por el canje de cupones
-- =================================================
-- Ùltima modificación	: Juan Estévez
-- Fecha				: 12/10/2018
-- =================================================
-- modificado por		: Claude Code / KDS System
-- Fecha				: 25/12/2024
-- Descripción			: Se agrega campo notasKDS para obtener notas especiales del KDS por plu_id
-- =================================================

CREATE   PROCEDURE [pedido].[ORD_cargar_ordenpedido_pendiente] 
(
	@rst_id INT
	, @IDCabeceraOrdenPedido VARCHAR(40)
	, @IDCategoria			 VARCHAR(40)
	, @NumeroCuenta			 SMALLINT	=  1
) 
AS 
BEGIN
	
	SET NOCOUNT ON;

	DECLARE @menuActivo		VARCHAR(40)
			, @tiendaActiva	VARCHAR(40)
			, @idCadena		INT
			, @status_activo VARCHAR(40)

	SET @menuActivo		= config.fn_estado('Menu','Activo');
	SET @tiendaActiva	= (config.fn_estado('Restaurante','Activo')); 
	SET @status_activo	= config.fn_estado('Cupon','Activo');
	SET @idCadena		= (SELECT cdn_id FROM Restaurante WHERE rst_id = @rst_id AND IDStatus = @tiendaActiva); 

	SELECT	1 AS tipo
			, CAST(p.plu_id AS VARCHAR(40))					AS plu_id
			, map.magp_desc_impresion +' '+
				CASE WHEN c.cla_Nombre = 'Llevar'
				THEN '[LLEVAR]'
				ELSE ''
				END											AS magp_desc_impresion
			, dop.dop_cantidad
			, dop.IDDetalleOrdenPedido						AS dop_id
			, ROUND(dop.dop_iva, 2)							AS dop_iva
			, ROUND(dop.dop_total, 2)						AS dop_total
			, ROUND(dop.dop_total, 2)						AS dop_precio_unitario
			, p.plu_impuesto
			, CASE WHEN p.plu_anulacion IS NULL
				THEN 0
				ELSE p.plu_anulacion
				END											AS plu_anulacion
			, CASE WHEN p.plu_gramo IS NULL
				THEN 0
				ELSE p.plu_gramo
				END											AS plu_gramo
			, p.IDClasificacion								AS cla_id
			, dop.dop_varchar1								AS ancestro
			, dop.dop_creacionfecha
			, dop.dop_cuenta
			, Detalle_Orden_PedidoVarchar1					AS Detalle_Orden_PedidoVarchar1
			, pt.puntos
			, ISNULL(dop.Detalle_Orden_PedidoVarchar4, 0)	AS tipoBeneficioCupon
			, dop.IDDetalleOrdenPedidoPadre
			, ISNULL(nkds.notasKDS, '')						AS notasKDS
	INTO #listaPedido
	FROM	Cabecera_Orden_Pedido AS odp WITH (NOLOCK)
			INNER JOIN Detalle_Orden_Pedido AS dop WITH (NOLOCK) ON odp.IDCabeceraOrdenPedido = dop.IDCabeceraOrdenPedido
			INNER JOIN Menu_Agrupacionproducto AS map WITH (NOLOCK) ON map.plu_id = dop.plu_id
			INNER JOIN Plus AS p WITH (NOLOCK) ON p.plu_id = dop.plu_id
			INNER JOIN Precio_Plu AS pp WITH (NOLOCK) ON pp.plu_id = p.plu_id
			INNER JOIN Clasificacion AS c WITH (NOLOCK) ON c.IDClasificacion = p.IDClasificacion
			LEFT OUTER JOIN [webservices].[FIDELIZACION_Puntos] pt ON pt.IDProducto = p.plu_id
			OUTER APPLY (
				SELECT TOP 1 pcd.variableV AS notasKDS
				FROM ColeccionPlus cp WITH (NOLOCK)
				INNER JOIN ColeccionDeDatosPlus cdp WITH (NOLOCK) ON cdp.ID_ColeccionPlus = cp.ID_ColeccionPlus
				INNER JOIN PlusColeccionDeDatos pcd WITH (NOLOCK) ON pcd.ID_ColeccionPlus = cp.ID_ColeccionPlus
					AND pcd.ID_ColeccionDeDatosPlus = cdp.ID_ColeccionDeDatosPlus
				WHERE cp.Descripcion = 'CONTENIDO KDS'
					AND cp.isActive = 1
					AND cdp.isActive = 1
					AND pcd.plu_id = p.plu_id
			) nkds
	WHERE	dop.IDCabeceraOrdenPedido = @IDCabeceraOrdenPedido
			AND dop.dop_estado  <> 0
			AND dop.dop_anulacion = 1
			AND dop.dop_cuenta = @NumeroCuenta
			AND pp.IDCategoria = @IDCategoria
			AND map.IDStatus = @menuActivo
	UNION
	SELECT	0
			, CAST(t.IDTextoDetalleOrdenPedido AS VARCHAR(40))
			, txt_dop_descripcion
			, 0
			, t.IDDetalleOrdenPedido
			, 0
			, 0
			, 0
			, 0
			, 0
			, 0
			, NULL
			, dop.dop_varchar1 AS ancestro
			, dop.dop_creacionfecha
			, dop.dop_cuenta
			, '' AS parametro
			, '' AS parametro2
			, 0	AS tipoBeneficioCupon
			, dop.IDDetalleOrdenPedidoPadre
			, '' AS notasKDS
	FROM	Texto_Detalle_Orden_Pedido AS t WITH (NOLOCK)
			INNER JOIN Detalle_Orden_Pedido AS dop WITH (NOLOCK) ON dop.IDDetalleOrdenPedido = t.IDDetalleOrdenPedido
			INNER JOIN Cabecera_Orden_Pedido AS cop WITH (NOLOCK) ON cop.IDCabeceraOrdenPedido = dop.IDCabeceraOrdenPedido
	WHERE	cop.IDCabeceraOrdenPedido = @IDCabeceraOrdenPedido
			AND dop.dop_estado <> 0
			AND dop.dop_anulacion = 1
			AND dop.dop_cuenta = @NumeroCuenta
	UNION
	SELECT *
			FROM(
				SELECT
					0 AS tipo
					, CAST(pc.Id_Promociones AS VARCHAR(40))				AS plu_id
					, CONCAT('<center><h6><strong>Cupon:',pc.Nombre_Promocion,'</strong></h6></center>')	AS magp_desc_impresion
					, 0													AS dop_cantidad
					, pc.Id_Promociones									AS dop_id
					, 0													AS dop_iva
					, 0													AS dop_total
					, 0													AS dop_precio_unitario
					, 0													AS dop_impuesto
					, 0													AS plu_anulacion
					, 0													AS plu_gramo
					, NULL												AS cla_id
					, CAST(pc.Id_Promociones AS VARCHAR(40))			AS ancestro
					, pc.Fecha_Canje									AS dop_creacionfecha
					, @NumeroCuenta										AS dop_cuenta
					, ''												AS parametro
					, ''												AS parametro2
					, 1													AS tipoBeneficioCupon
					, NULL												AS IDDetalleOrdenPedidoPadre
					, ''												AS notasKDS
				FROM Promociones_Canjeados	pc
				WHERE pc.IDCabeceraOrdenPedido = @IDCabeceraOrdenPedido
				AND pc.NumeroCuenta = @NumeroCuenta
				AND pc.IDStatus = @status_activo

				GROUP BY pc.Id_Promociones,pc.Nombre_Promocion,pc.Fecha_Canje
			)X;
			
-- tipoBeneficioCupon = 1 es canje de cupon por porcentaje
-- tipoBeneficioCupon = 2 es canje de cupon por valor fijo  
-- tipoBeneficioCupon = 3 es canje de cupon que regala producto  

	SELECT	lp.*
		  ,CASE WHEN lp.tipoBeneficioCupon = bc.tipo_beneficio THEN tipo_beneficio_color
		   ELSE '#FFFFFF' END AS colorBeneficioCupon
	FROM	#listaPedido AS lp WITH(NOLOCK)
	LEFT JOIN [promociones].[fn_ColeccionCadena_BeneficiosCupones] (@idCadena) AS bc ON bc.tipo_beneficio = lp.tipoBeneficioCupon
	ORDER BY dop_creacionfecha ASC, tipo DESC;
															   
	DROP TABLE #listaPedido;

END