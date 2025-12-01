<!DOCTYPE html>
<html>
<head>
    <title>Confirmación de Compra</title>
</head>
<body>
    <h1>¡Hola {{ $user->name }}!</h1>
    <p>Gracias por tu compra en nuestra librería. Aquí están los detalles de tu pedido:</p>
    
    <h2>Detalles del Libro:</h2>
    <ul>
        <li><strong>Nombre:</strong> {{ $book->name }}</li>
        <li><strong>Cantidad:</strong> 1</li>
        <li><strong>Precio Unitario:</strong> ${{ number_format($book->price, 2) }}</li>
    </ul>

    <p>¡Esperamos que disfrutes tu lectura!</p>
    <p>Saludos cordiales,</p>
    <p>El equipo de la Librería</p>
</body>
</html>