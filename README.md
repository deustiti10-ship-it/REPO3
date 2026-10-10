# Aplicativo de gestión de envíos

Aplicación sencilla en PHP + MySQL para registrar, consultar, editar y eliminar envíos.

## Campos de cada envío
- `destinatario`: nombre de quien recibe.
- `direccion`: dirección de entrega.
- `descripcion`: detalle o contenido del envío.
- `creado_en`: fecha y hora de registro (automática).

## Archivos
- `index.php`: interfaz y operaciones CRUD.
- `config.php`: conexión PDO y creación automática de la tabla.
- `database.sql`: SQL opcional para crear la tabla manualmente.
- `README.md`: documentación.

## Instalación en AlwaysData
1. En el panel de AlwaysData, confirma que exista la base de datos `vanne_enviosgit` y que el usuario `vanne` tenga permisos sobre ella.
2. Sube `index.php` y `config.php` (y opcionalmente `database.sql`) a la carpeta web de tu sitio.
3. Verifica que el sitio tenga una versión de PHP compatible con PDO MySQL.
4. Abre la URL de tu sitio en el navegador. Al cargar `index.php`, `config.php` se conecta a la base de datos y ejecuta `CREATE TABLE IF NOT EXISTS envios`.
5. Si aparece un error de conexión, revisa en AlwaysData el host, el nombre de la base, el usuario y la contraseña configurados.

## Configuración de base de datos
Las credenciales solicitadas se encuentran en `config.php`. Por seguridad, no publiques ese archivo en repositorios públicos ni compartas el ZIP públicamente. Si las credenciales reales se han expuesto, cámbialas desde el panel del proveedor.

## Operaciones disponibles
- **Crear:** completa el formulario y pulsa “Registrar envío”.
- **Leer:** los envíos aparecen en la tabla inferior.
- **Actualizar:** pulsa “Editar”, modifica los datos y guarda.
- **Eliminar:** pulsa “Eliminar” y confirma la acción.

Todas las consultas que reciben datos usan sentencias preparadas PDO y los valores mostrados se escapan para reducir riesgos de inyección SQL y XSS.
