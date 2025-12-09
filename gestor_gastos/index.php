<?php
include("includes/header.php");

// Cargar imágenes del carrusel
$imgDir = "imagenes";
$exts = ["jpg", "jpeg", "png", "webp", "gif"];
$slides = [];

if (is_dir($imgDir)) {
  foreach (scandir($imgDir) as $f) {
    if ($f === "." || $f === "..") continue;
    $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
    if (!in_array($ext, $exts)) continue;
    $slides[] = $imgDir . "/" . $f;
  }
}

if (!$slides) {
  $slides = ["imagenes/menu/euro.gif"];
}

include("vistas/index_vista.php");
include("includes/footer.php");
