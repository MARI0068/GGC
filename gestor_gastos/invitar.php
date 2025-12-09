<?php
session_start();
require_once "includes/conexion.php";

// Cargar PHPMailer
require_once __DIR__ . "/PHPMailer/src/PHPMailer.php";
require_once __DIR__ . "/PHPMailer/src/SMTP.php";
require_once __DIR__ . "/PHPMailer/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Si no hay sesión, al login
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}
$usuario_id = (int)$_SESSION["usuario_id"];

// Grupo recibido por GET
$grupo_id = isset($_GET["grupo_id"]) ? (int)$_GET["grupo_id"] : 0;
if ($grupo_id <= 0) {
    header("Location: grupos.php");
    exit;
}

// Comprobar que el usuario es propietario del grupo
$sql = "SELECT 1 
        FROM usuarios_grupos 
        WHERE grupo_id = ? AND usuario_id = ? AND rol = 'propietario'";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $grupo_id, $usuario_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    header("Location: grupos.php");
    exit;
}

// Variables para la vista
$mensaje = "";
$ok      = false;

// Procesar invitación
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "El correo introducido no es válido.";
    } else {

        // 1. Buscar usuario por email
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        $st  = $conexion->prepare($sql);
        $st->bind_param("s", $email);
        $st->execute();
        $res = $st->get_result();

        if ($res->num_rows === 1) {

            $fila        = $res->fetch_assoc();
            $usuario_inv = (int)$fila["id"];

            // 2. Comprobar si ya pertenece al grupo
            $sql = "SELECT 1 
                    FROM usuarios_grupos 
                    WHERE grupo_id = ? AND usuario_id = ?";
            $st = $conexion->prepare($sql);
            $st->bind_param("ii", $grupo_id, $usuario_inv);
            $st->execute();
            $ya = $st->get_result();

            if ($ya->num_rows > 0) {

                $mensaje = "Ese usuario ya pertenece al grupo.";
            } else {

                // 3. Insertar en usuarios_grupos
                $sql = "INSERT INTO usuarios_grupos (grupo_id, usuario_id, rol) 
                        VALUES (?, ?, 'miembro')";
                $st  = $conexion->prepare($sql);
                $st->bind_param("ii", $grupo_id, $usuario_inv);

                if ($st->execute()) {

                    $ok      = true;
                    $mensaje = "Usuario invitado correctamente.";

                    // 4. Intentar enviar email real
                    try {
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'ggc.invitar@gmail.com';
                        $mail->Password   = 'nooonyuygkuxjrss'; // contraseña de aplicación SIN espacios
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        $mail->setFrom('ggc.invitar@gmail.com', 'Gestor de Gastos Compartidos');
                        $mail->addAddress($email);

                        // --- CARGAR DATOS PARA PERSONALIZAR LA PLANTILLA ---
                        $nombreGrupo  = 'tu grupo';
                        $nombreInvita = 'un usuario';

                        $infoSql = "SELECT g.nombre AS nombre_grupo, u.nombre AS nombre_invitador
                FROM grupos g
                JOIN usuarios u ON u.id = ?
                WHERE g.id = ?";
                        $infoSt = $conexion->prepare($infoSql);
                        $infoSt->bind_param("ii", $usuario_id, $grupo_id);
                        $infoSt->execute();
                        $infoRes = $infoSt->get_result();
                        if ($infoRow = $infoRes->fetch_assoc()) {
                            $nombreGrupo  = $infoRow['nombre_grupo'];
                            $nombreInvita = $infoRow['nombre_invitador'];
                        }

                        // --- CARGAR PLANTILLA HTML DESDE ARCHIVO ---
                        $plantilla = __DIR__ . '/plantillas/email_invitacion.html';

                        if (!file_exists($plantilla)) {
                            // Mensaje de depuración en la web si no se encuentra
                            $mensaje .= " (No se encontró la plantilla de email en: $plantilla)";
                            // Como emergencia, texto plano:
                            $bodyHtml = 'Has sido invitado al Gestor de Gastos Compartidos. Accede en <a href="https://ggc.infinityfree.me/login.php">https://ggc.infinityfree.me/login.php</a>';
                        } else {
                            $bodyHtml = file_get_contents($plantilla);
                            // Reemplazar marcadores
                            $bodyHtml = str_replace(
                                ['{{NOMBRE_INVITA}}', '{{NOMBRE_GRUPO}}'],
                                [$nombreInvita, $nombreGrupo],
                                $bodyHtml
                            );
                        }

                        $mail->isHTML(true);
                        $mail->CharSet = 'UTF-8';
                        $mail->Encoding = 'base64';

                        $mail->Subject = 'Invitación al Gestor de Gastos Compartidos';
                        $mail->Body    = $bodyHtml;
                        $mail->AltBody = "Hola,\n\n{$nombreInvita} te ha invitado al grupo \"{$nombreGrupo}\" en el Gestor de Gastos Compartidos.\n\nAccede aquí: https://ggc.infinityfree.me/login.php";

                        $mail->send();
                    } catch (Exception $e) {
                        $mensaje .= " (No se pudo enviar el correo de invitación.)";
                        // Para depurar, si quieres:
                        // $mensaje .= " Detalle: " . $mail->ErrorInfo;
                    }
                } else {
                    $mensaje = "Error al invitar usuario.";
                }
            }
        } else {
            $mensaje = "No existe ningún usuario registrado con ese correo.";
        }
    }
}

// Cargar vista (formulario de invitación)
require __DIR__ . "/vistas/invitar_vista.php";
