<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['my_file'])) {
        $tmp = $_FILES['my_file']['tmp_name'];
        $name = basename($_FILES['my_file']['name']);
        $dest = __DIR__ . '/uploads/' . $name;

        if (!is_uploaded_file($tmp)) {
            die('El archivo no fue subido correctamente (is_uploaded_file falló)');
        }

        if (move_uploaded_file($tmp, $dest)) {
            echo "Archivo subido correctamente a: $dest";
        } else {
            echo "Error al mover el archivo.";
        }
    }
}
?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="my_file">
    <button type="submit">Subir</button>
</form>
