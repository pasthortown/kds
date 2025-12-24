<?php

require "../../../../system/conexion/clase_sql.php";
require "../../../../clases/clase_dragonTailConfig.php";
function getCodApp($cfac_id) {
    return  DragonTailConfig::getCodAppFromCfac($cfac_id);
}
