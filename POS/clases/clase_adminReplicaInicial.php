<?php

class ReplicaInicial extends sql {

    //private $lc_regs;
    //constructor de la clase
    function __construct() {
        parent ::__construct();
    }

    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {
            case "verificarjson":
                $lc_sql = " 
                 IF EXISTS ( SELECT  1
            FROM    Information_schema.Routines
            WHERE   Specific_schema = 'dbo'
                    AND specific_name = 'JsonNVarChar'
                    AND Routine_Type = 'FUNCTION' ) 
					BEGIN
						SELECT 1 as Respuesta;
					END
					ELSE 
						BEGIN
							Select 0 as Respuesta;
					END
                ";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("Respuesta" => $row['Respuesta']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
    }

}