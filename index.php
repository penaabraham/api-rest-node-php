<?php
// http://localhost/index.php/usuarios/4
// Header Name: Accept
// Header Value: application/json

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

// 1. Obtenemos la URI y quitamos lo que esté después de un "?" (query string)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 2. Limpiamos y dividimos la ruta
$request = explode('/', trim($uri, '/'));

/**
 * Para http://localhost/index.php/usuarios/1
 * $request[0] será "index.php"
 * $request[1] será "usuarios"
 * $request[2] será "1"
 */

// 3. Buscamos el ID en la posición 2
$id = isset($request[2]) && is_numeric($request[2]) ? (int)$request[2] : null;

// Rutas
switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM TbUsuario WHERE id = ?");
            $stmt->execute([$id]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($usuario) {
                echo json_encode($usuario);
            } else {
                echo json_encode(['message' => 'Usuario no encontrado']);
            }
        } else {
            $stmt = $pdo->query("SELECT * FROM TbUsuario");
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($usuarios);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['nombre'], $data['usuario'], $data['psw'])) {
            $stmt = $pdo->prepare("INSERT INTO TbUsuario (nombre, usuario, psw) VALUES (?, ?, ?)");
            $stmt->execute([$data['nombre'], $data['usuario'], $data['psw']]);
            echo json_encode(['message' => 'Usuario creado']);
        } else {
            echo json_encode(['message' => 'Datos incompletos']);
        }
        break;

    case 'PUT':
        if ($id) {
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['nombre'], $data['usuario'], $data['psw'])) {
                $stmt = $pdo->prepare("UPDATE TbUsuario SET nombre = ?, usuario = ?, psw = ? WHERE id = ?");
                $stmt->execute([$data['nombre'], $data['usuario'], $data['psw'], $id]);
                if ($stmt->rowCount()) {
                    echo json_encode(['message' => 'Usuario actualizado']);
                } else {
                    echo json_encode(['message' => 'Usuario no encontrado o no se realizaron cambios']);
                }
            } else {
                echo json_encode(['message' => 'Datos incompletos']);
            }
        } else {
            echo json_encode(['message' => 'ID de usuario requerido']);
        }
        break;

    case 'DELETE':
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM TbUsuario WHERE id = ?");
            $stmt->execute([$id]);
            if ($stmt->rowCount()) {
                echo json_encode(['message' => 'Usuario eliminado']);
            } else {
                echo json_encode(['message' => 'Usuario no encontrado']);
            }
        } else {
            echo json_encode(['message' => 'ID de usuario requerido']);
        }
        break;

    default:
        echo json_encode(['message' => 'Método no soportado']);
        break;
}