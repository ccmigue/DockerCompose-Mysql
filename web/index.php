<?php
// Iniciar buffer de salida
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pokémon List</title>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
        }
    </style>
</head>
<body>
    <h1>Pokémon Registro</h1>
    <form action="index.php" method="post">
        <input type="hidden" name="id" id="id">
        <label for="name">Nombre:</label>
        <input type="text" name="name" id="name" required>
        <label for="type">Tipo:</label>
        <input type="text" name="type" id="type" required>
        <button type="submit" name="action" value="create">Crear</button>
        <button type="submit" name="action" value="update">Actualizar</button>
        <button type="submit" name="action" value="delete">Eliminar</button>
    </form>

    <h2>Lista de Pokémon</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Fecha de Registro</th>
            <th>Acción</th>
        </tr>
        <?php
        $servername = "db";
        $username = "user";
        $password = "user_password";
        $dbname = "my_database";

        // Crear conexión
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verificar conexión
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Crear tabla si no existe
        $sql = "CREATE TABLE IF NOT EXISTS pokemon (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            type VARCHAR(30) NOT NULL,
            reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        $conn->query($sql);

        // Manejar acciones del formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            $name = isset($_POST['name']) ? $_POST['name'] : '';
            $type = isset($_POST['type']) ? $_POST['type'] : '';
            $action = isset($_POST['action']) ? $_POST['action'] : '';

            if ($action == 'create' && !empty($name) && !empty($type)) {
                $sql = "INSERT INTO pokemon (name, type) VALUES ('$name', '$type')";
                $conn->query($sql);
            } elseif ($action == 'update' && !empty($id) && !empty($name) && !empty($type)) {
                $sql = "UPDATE pokemon SET name='$name', type='$type' WHERE id=$id";
                $conn->query($sql);
            } elseif ($action == 'delete' && !empty($id)) {
                $sql = "DELETE FROM pokemon WHERE id=$id";
                $conn->query($sql);
            }

            // Redireccionar después de la operación POST para evitar resubmisión del formulario
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }

        // Leer datos de la tabla
        $sql = "SELECT id, name, type, reg_date FROM pokemon";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["name"] . "</td>";
                echo "<td>" . $row["type"] . "</td>";
                echo "<td>" . $row["reg_date"] . "</td>";
                echo "<td><button onclick='editPokemon(" . $row["id"] . ", \"" . $row["name"] . "\", \"" . $row["type"] . "\")'>Editar</button></td>";
                echo "</tr>";
            }
        }

        $conn->close();
        ?>
    </table>

    <script>
        function editPokemon(id, name, type) {
            document.getElementById('id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('type').value = type;
        }
    </script>
</body>
</html>

<?php
// Limpiar el buffer de salida y enviar la salida
ob_end_flush();
?>