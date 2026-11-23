<?php
include "conexion.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $id_atleta=intval($_POST['id_atleta']);
    $ritmos=$_POST['ritmos'];
}
foreach ($ritmos as $distancia=>$ritmo){
    $distancia+floatval($distancia);
    $ritmo=htmlspecialchars($ritmo);

    $sql= "INSERT INTO ritmos (id_atleta, distancia, ritmo)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE ritmo = ?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("iiss", $id_atleta, $distancia, $ritmo, $ritmo);

    if(!$stmt->execute()){
        die("Error al guardar los ritmos: " .$stmt->error);
    }
}

header("Location: progreso.php?id=$id_atleta");
?>
