// http://localhost:3000/usuarios/3

// index.js
const express = require("express");
const mysql = require("mysql2");
const bodyParser = require("body-parser");
const config = require("./config");
// Crear la conexión a la base de datos
const pool = mysql.createPool(config);
// Inicializar la aplicación Express
const app = express();
// Middleware para parsear cuerpos JSON
app.use(bodyParser.json());
// Ruta GET para obtener todos los usuarios
app.get("/usuarios", (req, res) => {
  pool.query("SELECT * FROM TbUsuario", (err, results) => {
    if (err) {
      return res.status(500).json({ error: err.message });
    }
    res.json(results);
  });
});
// Ruta GET para obtener un usuario por ID
app.get("/usuarios/:id", (req, res) => {
  const { id } = req.params;
  pool.query("SELECT * FROM TbUsuario WHERE id = ?", [id], (err, results) => {
    if (err) {
      return res.status(500).json({ error: err.message });
    }
    if (results.length === 0) {
      return res.status(404).json({ message: "Usuario no encontrado" });
    }
    res.json(results[0]);
  });
});
// Ruta POST para crear un nuevo usuario
app.post("/usuarios", (req, res) => {
  const { nombre, usuario, psw } = req.body;
  const sql = "INSERT INTO TbUsuario (nombre, usuario, psw) VALUES (?, ?, ?)";
  pool.query(sql, [nombre, usuario, psw], (err, results) => {
    if (err) {
      return res.status(500).json({ error: err.message });
    }
    res.status(201).json({
      message: "Usuario creado",
      usuarioId: results.insertId,
    });
  });
});
// Ruta PUT para actualizar un usuario por ID
app.put("/usuarios/:id", (req, res) => {
  const { id } = req.params;
  const { nombre, usuario, psw } = req.body;
  const sql =
    "UPDATE TbUsuario SET nombre = ?, usuario = ?, psw = ? WHERE id = ?";
  pool.query(sql, [nombre, usuario, psw, id], (err, results) => {
    if (err) {
      return res.status(500).json({ error: err.message });
    }
    if (results.affectedRows === 0) {
      return res.status(404).json({ message: "Usuario no encontrado" });
    }
    res.json({ message: "Usuario actualizado" });
  });
});
// Ruta DELETE para eliminar un usuario por ID
app.delete("/usuarios/:id", (req, res) => {
  const { id } = req.params;
  const sql = "DELETE FROM TbUsuario WHERE id = ?";
  pool.query(sql, [id], (err, results) => {
    if (err) {
      return res.status(500).json({ error: err.message });
    }
    if (results.affectedRows === 0) {
      return res.status(404).json({ message: "Usuario no encontrado" });
    }
    res.json({ message: "Usuario eliminado" });
  });
});
// Iniciar el servidor
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Servidor corriendo en http://localhost:${PORT}`);
});
