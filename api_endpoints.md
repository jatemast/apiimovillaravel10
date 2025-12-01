# Ejemplos de Consumo de la API de Librería (Laravel 10)

**URL Base de la API:** `http://127.0.0.1:8000/api`

---

### **1. Registro de Usuario (Rol "user" por defecto)**

Este endpoint creará un nuevo usuario con el rol `user`.

```bash
curl -X POST "http://127.0.0.1:8000/api/register" \
-H "Content-Type: application/json" \
-d '{
    "name": "Usuario Prueba",
    "email": "usuario@example.com",
    "password": "password",
    "password_confirmation": "password",
    "latitude": "40.7128",
    "longitude": "-74.0060"
}'
```

**Respuesta esperada (ejemplo):**
```json
{
    "message": "User registered successfully",
    "token": "YOUR_AUTH_TOKEN_HERE"
}
```
**Nota:** Deberías recibir un correo de bienvenida en `usuario@example.com` si la configuración de correo está correcta.

---

### **2. Registro de Administrador (para crear libros)**

Para probar las funcionalidades de administrador, necesitamos un usuario con el rol `admin`. Por simplicidad, puedes modificar manualmente el rol de un usuario existente en la base de datos (por ejemplo, el usuario creado anteriormente) a `'admin'`, o crear un usuario con el rol `admin` directamente en la base de datos para las pruebas. Por ejemplo, si el `id` del usuario creado es 1, podrías ejecutar en tu consola MySQL:

```sql
UPDATE users SET role = 'admin' WHERE id = 1;
```

---

### **3. Login de Usuario/Administrador**

Este endpoint te proporcionará un token de autenticación que usarás para las solicitudes protegidas.

```bash
curl -X POST "http://127.0.0.1:8000/api/login" \
-H "Content-Type: application/json" \
-d '{
    "email": "usuario@example.com",
    "password": "password"
}'
```

**Respuesta esperada (ejemplo):**
```json
{
    "message": "Logged in successfully",
    "token": "YOUR_AUTH_TOKEN_HERE",
    "user": {
        "id": 1,
        "name": "Usuario Prueba",
        "email": "usuario@example.com",
        "email_verified_at": null,
        "latitude": "40.7128",
        "longitude": "-74.0060",
        "created_at": "2023-10-27T10:00:00.000000Z",
        "updated_at": "2023-10-27T10:00:00.000000Z"
    }
}
```
**Guarda el `token` devuelto, lo necesitarás para las siguientes solicitudes. Si te logueas con un usuario administrador, guarda ese token.**

---

### **4. Listar Todos los Libros (Usuarios y Administradores)**

Cualquier usuario autenticado puede ver la lista de libros. Reemplaza `YOUR_AUTH_TOKEN_HERE` con el token obtenido en el paso de login.

```bash
curl -X GET "http://127.0.0.1:8000/api/books" \
-H "Accept: application/json" \
-H "Authorization: Bearer YOUR_AUTH_TOKEN_HERE"
```

**Respuesta esperada (ejemplo):**
```json
[] // O una lista de libros si ya has creado alguno
```

---

### **5. Buscar Libros por Nombre (Usuarios y Administradores)**

Cualquier usuario autenticado puede buscar libros por nombre. Reemplaza `YOUR_AUTH_TOKEN_HERE` con el token obtenido en el paso de login.

```bash
curl -X GET "http://127.0.0.1:8000/api/books/search?query=viaje" \
-H "Accept: application/json" \
-H "Authorization: Bearer YOUR_AUTH_TOKEN_HERE"
```

**Respuesta esperada (ejemplo):**
```json
[
    {
        "id": 1,
        "name": "El Gran Viaje",
        "quantity": 9,
        "price": "25.50",
        "image_url": "https://i.ibb.co/...",
        "created_at": "2023-10-27T10:30:00.000000Z",
        "updated_at": "2023-10-27T10:30:00.000000Z"
    }
]
```

---

### **6. Crear un Nuevo Libro (Solo Administrador)**

Solo un usuario con rol `admin` puede crear libros. Reemplaza `YOUR_ADMIN_AUTH_TOKEN_HERE` con el token de tu usuario administrador. La imagen debe ser una cadena base64. Puedes usar un conversor online para obtener el base64 de una imagen pequeña.

```bash
curl -X POST "http://127.0.0.1:8000/api/books" \
-H "Accept: application/json" \
-H "Authorization: Bearer YOUR_ADMIN_AUTH_TOKEN_HERE" \
-H "Content-Type: application/json" \
-d '{
    "name": "El Gran Viaje",
    "quantity": 10,
    "price": 25.50,
    "image": "R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
}'
```

**Respuesta esperada (ejemplo):**
```json
{
    "name": "El Gran Viaje",
    "quantity": 10,
    "price": "25.50",
    "image_url": "https://i.ibb.co/...",
    "updated_at": "2023-10-27T10:30:00.000000Z",
    "created_at": "2023-10-27T10:30:00.000000Z",
    "id": 1
}
```
**Guarda el `id` del libro creado.**

---

### **6. Mostrar un Libro Específico (Usuarios y Administradores)**

Cualquier usuario autenticado puede ver los detalles de un libro específico. Reemplaza `YOUR_AUTH_TOKEN_HERE` y `BOOK_ID` (por ejemplo, 1).

```bash
curl -X GET "http://127.0.0.1:8000/api/books/BOOK_ID" \
-H "Accept: application/json" \
-H "Authorization: Bearer YOUR_AUTH_TOKEN_HERE"
```

---

### **7. Actualizar un Libro Existente (Solo Administrador)**

Solo un usuario con rol `admin` puede actualizar libros. Reemplaza `YOUR_ADMIN_AUTH_TOKEN_HERE` y `BOOK_ID`.

```bash
curl -X PUT "http://127.0.0.1:8000/api/books/BOOK_ID" \
-H "Accept: application/json" \
-H "Authorization: Bearer YOUR_ADMIN_AUTH_TOKEN_HERE" \
-H "Content-Type: application/json" \
-d '{
    "name": "El Gran Viaje Edición Revisada",
    "price": 27.99
}'
```

---

### **8. Simular la Compra de un Libro (Solo Usuario)**

Solo un usuario con rol `user` puede comprar libros. Reemplaza `YOUR_USER_AUTH_TOKEN_HERE` (token de un usuario con rol `user`) y `BOOK_ID`.

```bash
curl -X POST "http://127.0.0.1:8000/api/books/BOOK_ID/purchase" \
-H "Accept: application/json" \
-H "Authorization: Bearer YOUR_USER_AUTH_TOKEN_HERE"
```

**Respuesta esperada (ejemplo):**
```json
{
    "message": "Compra realizada con éxito. Se ha enviado un ticket a su correo."
}
```
**Nota:** Deberías recibir un correo de confirmación de compra en el correo del usuario si la configuración de correo está correcta.

---

### **9. Eliminar un Libro (Solo Administrador)**

Solo un usuario con rol `admin` puede eliminar libros. Reemplaza `YOUR_ADMIN_AUTH_TOKEN_HERE` y `BOOK_ID`.

```bash
curl -X DELETE "http://127.0.0.1:8000/api/books/BOOK_ID" \
-H "Accept: application/json" \
-H "Authorization: Bearer YOUR_ADMIN_AUTH_TOKEN_HERE"
```

---

### **10. Logout de Usuario/Administrador**

Invalida el token de autenticación actual.

```bash
curl -X POST "http://127.0.0.1:8000/api/logout" \
-H "Accept: application/json" \
-H "Authorization: Bearer YOUR_AUTH_TOKEN_HERE"
```

**Respuesta esperada (ejemplo):**
```json
{
    "message": "Logged out successfully"
}