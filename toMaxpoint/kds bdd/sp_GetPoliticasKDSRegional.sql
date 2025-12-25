CREATE OR ALTER PROCEDURE sp_GetPoliticasKDSRegional
    @rst_id      INT,
    @IDEstacion  UNIQUEIDENTIFIER
AS
BEGIN
    SET NOCOUNT ON;

    /* Variables de salida */
    DECLARE
        @URL                       VARCHAR(200) = '',
        @EMAIL                     VARCHAR(200) = '',
        @PASSWORD                  VARCHAR(200) = '',
        @CANALES_EXCLUIDOS         VARCHAR(200) = '',
        @ACTIVO                   BIT = 0,
        @IMPRESION_A_TIEMPO_REAL   BIT = 0;

    /* ============================
       1) URL
       ============================ */
    SELECT TOP (1)
        @URL = ISNULL(rcd.variableV, '')
    FROM ColeccionRestaurante cr
    INNER JOIN ColeccionDeDatosRestaurante cdr
        ON cdr.ID_ColeccionRestaurante = cr.ID_ColeccionRestaurante
    INNER JOIN RestauranteColeccionDeDatos rcd
        ON rcd.ID_ColeccionRestaurante = cr.ID_ColeccionRestaurante
       AND rcd.ID_ColeccionDeDatosRestaurante = cdr.ID_ColeccionDeDatosRestaurante
    WHERE cr.Descripcion = 'KDS REGIONAL'
      AND cr.isActive = 1
      AND cdr.isActive = 1
      AND rcd.isActive = 1
      AND rcd.variableB = 1
      AND cdr.Descripcion = 'URL'
      AND rcd.rst_id = @rst_id
    ORDER BY rcd.ID_ColeccionDeDatosRestaurante DESC; -- ajustar si existe fecha

    /* ============================
       2) EMAIL
       ============================ */
    SELECT TOP (1)
        @EMAIL = ISNULL(rcd.variableV, '')
    FROM ColeccionRestaurante cr
    INNER JOIN ColeccionDeDatosRestaurante cdr
        ON cdr.ID_ColeccionRestaurante = cr.ID_ColeccionRestaurante
    INNER JOIN RestauranteColeccionDeDatos rcd
        ON rcd.ID_ColeccionRestaurante = cr.ID_ColeccionRestaurante
       AND rcd.ID_ColeccionDeDatosRestaurante = cdr.ID_ColeccionDeDatosRestaurante
    WHERE cr.Descripcion = 'KDS REGIONAL'
      AND cr.isActive = 1
      AND cdr.isActive = 1
      AND rcd.isActive = 1
      AND rcd.variableB = 1
      AND cdr.Descripcion = 'EMAIL'
      AND rcd.rst_id = @rst_id
    ORDER BY rcd.ID_ColeccionDeDatosRestaurante DESC;

    /* ============================
       3) PASSWORD
       ============================ */
    SELECT TOP (1)
        @PASSWORD = ISNULL(rcd.variableV, '')
    FROM ColeccionRestaurante cr
    INNER JOIN ColeccionDeDatosRestaurante cdr
        ON cdr.ID_ColeccionRestaurante = cr.ID_ColeccionRestaurante
    INNER JOIN RestauranteColeccionDeDatos rcd
        ON rcd.ID_ColeccionRestaurante = cr.ID_ColeccionRestaurante
       AND rcd.ID_ColeccionDeDatosRestaurante = cdr.ID_ColeccionDeDatosRestaurante
    WHERE cr.Descripcion = 'KDS REGIONAL'
      AND cr.isActive = 1
      AND cdr.isActive = 1
      AND rcd.isActive = 1
      AND rcd.variableB = 1
      AND cdr.Descripcion = 'PASSWORD'
      AND rcd.rst_id = @rst_id
    ORDER BY rcd.ID_ColeccionDeDatosRestaurante DESC;

    /* ============================
       4) CANALES EXCLUIDOS
       ============================ */
    SELECT TOP (1)
        @CANALES_EXCLUIDOS = ISNULL(rcd.variableV, '')
    FROM ColeccionRestaurante cr
    INNER JOIN ColeccionDeDatosRestaurante cdr
        ON cdr.ID_ColeccionRestaurante = cr.ID_ColeccionRestaurante
    INNER JOIN RestauranteColeccionDeDatos rcd
        ON rcd.ID_ColeccionRestaurante = cr.ID_ColeccionRestaurante
       AND rcd.ID_ColeccionDeDatosRestaurante = cdr.ID_ColeccionDeDatosRestaurante
    WHERE cr.Descripcion = 'KDS REGIONAL'
      AND cr.isActive = 1
      AND cdr.isActive = 1
      AND rcd.isActive = 1
      AND rcd.variableB = 1
      AND cdr.Descripcion = 'CANALES EXCLUIDOS'
      AND rcd.rst_id = @rst_id
    ORDER BY rcd.ID_ColeccionDeDatosRestaurante DESC;

    /* ============================
       5) ACTIVO (Estación)
       ============================ */
    SELECT TOP (1)
        @ACTIVO = ISNULL(CONVERT(BIT, ecd.variableB), 0)
    FROM ColeccionEstacion ce
    INNER JOIN ColeccionDeDatosEstacion cde
        ON cde.ID_ColeccionEstacion = ce.ID_ColeccionEstacion
    INNER JOIN EstacionColeccionDeDatos ecd
        ON ecd.ID_ColeccionEstacion = ce.ID_ColeccionEstacion
       AND ecd.ID_ColeccionDeDatosEstacion = cde.ID_ColeccionDeDatosEstacion
    WHERE ce.Descripcion = 'KDS REGIONAL'
      AND ce.isActive = 1
      AND cde.isActive = 1
      AND ecd.isActive = 1
      AND ecd.variableB = 1
      AND cde.Descripcion = 'ACTIVO'
      AND ecd.IDEstacion = @IDEstacion
    ORDER BY ecd.ID_ColeccionDeDatosEstacion DESC;

    /* ============================
       6) IMPRESION A TIEMPO REAL
       ============================ */
    SELECT TOP (1)
        @IMPRESION_A_TIEMPO_REAL = ISNULL(CONVERT(BIT, ecd.variableB), 0)
    FROM ColeccionEstacion ce
    INNER JOIN ColeccionDeDatosEstacion cde
        ON cde.ID_ColeccionEstacion = ce.ID_ColeccionEstacion
    INNER JOIN EstacionColeccionDeDatos ecd
        ON ecd.ID_ColeccionEstacion = ce.ID_ColeccionEstacion
       AND ecd.ID_ColeccionDeDatosEstacion = cde.ID_ColeccionDeDatosEstacion
    WHERE ce.Descripcion = 'KDS REGIONAL'
      AND ce.isActive = 1
      AND cde.isActive = 1
      AND ecd.isActive = 1
      AND ecd.variableB = 1
      AND cde.Descripcion = 'IMPRESION A TIEMPO REAL'
      AND ecd.IDEstacion = @IDEstacion
    ORDER BY ecd.ID_ColeccionDeDatosEstacion DESC;

    /* ============================
       Resultado final
       ============================ */
    SELECT
        @URL                     AS URL,
        @EMAIL                   AS EMAIL,
        @PASSWORD                AS PASSWORD,
        @CANALES_EXCLUIDOS       AS CANALES_EXCLUIDOS,
        @ACTIVO                  AS ACTIVO,
        @IMPRESION_A_TIEMPO_REAL AS IMPRESION_A_TIEMPO_REAL;
END;
GO