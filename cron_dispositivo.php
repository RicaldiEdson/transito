<?php

include("database/conexion.php");

$sql = "

UPDATE dispositivos

SET estado='Offline'

WHERE ultima_conexion IS NULL

OR TIMESTAMPDIFF
(
MINUTE,
ultima_conexion,
NOW()
)
>= 2

";

$conexion
->query($sql);

echo
"Cron ejecutado";

?>