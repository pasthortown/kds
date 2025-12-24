<?php

require "../../../../system/conexion/clase_sql.php";
require"../../../../clases/clase_dragontailApiService.php";

function getOrder() {
    var_dump(DragonTailApiService::getEndPoint('getOrder'));
}
