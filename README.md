## Equipo

| Nombre completo | Carnet | Correo electrónico | Rol dentro del equipo |
|---|---|---|---|
| Yourgen Kraven Thommel Arevalo |0905-23-14003|ythommela@miumg.edu.gt |Coordinador|
| Karen Yamileth Jiménez Galicia |0905-23-7626|kjimenezg6@miumg.edu.gt |Desarrolladora|
| Enner Osvaldo Godoy Ramirez |0905-23-15908 |egodoyr2@miumg.edu.gt |Desarrollador|
| Oliver Isaac Godoy Salguero |0905-23-10816 |ogodoys@miumg.edu.gt |Desarrollador|

> **Líder del equipo:** _(Yourgen Kraven Thommel Arevalo)_

Proyecto del curso, Universidad Mariano Gálvez.
Coordinación del curso: wcordovz1@umg.edu.gt

---

## Proyecto en Jira

[Tablero del proyecto (SDGODA)](https://miumg-team-cuatro.atlassian.net/jira/software/projects/SDGODA/list?jql=project+%3D+SDGODA+ORDER+BY+cf%5B10019%5D+ASC&atlOrigin=eyJpIjoiYzM2MTRkMjg5NzBjNGYyZDgzOTVhOThmZDdhMDQ4NTUiLCJwIjoiaiJ9)

# Sistema de Gestión — Oficina de Agua

Sistema web para digitalizar el control de una oficina comunitaria de agua
potable: registro de lecturas de contadores casa por casa, cálculo automático de
consumo y monto según la tarifa vigente, generación de recibo imprimible, control
de pagos en oficina y estado de cuenta por cliente.

El problema central que resuelve es el **registro de lecturas en campo**: el
lector visita cada predio con el celular, ve qué contadores tiene pendientes del
período, ingresa la lectura y el sistema calcula el cobro sin intervención
manual.

## Stack

| Componente | Versión |
|---|---|
| PHP | 8.2 o superior |
| CodeIgniter | 4.7 |
| Base de datos | MySQL / MariaDB |
| Frontend | Bootstrap 5 (plantilla Soft UI Dashboard) |

## Roles

| Rol | Qué puede hacer |
|---|---|
| **Administrador** | Todo: usuarios, tarifas, períodos, clientes, contadores, lecturas, recibos, pagos y dashboard |
| **Secretaria** | Clientes, contadores, historial de lecturas, recibos, pagos y dashboard |
| **Lector** | Contadores pendientes de lectura, registro de la lectura y el recibo que genera |

El acceso se controla en dos capas: el menú lateral oculta lo que el rol no
necesita, y los grupos de rutas aplican los filtros `auth` y `role`, así que
escribir la URL directamente tampoco funciona.

## Flujo principal

1. El **Administrador** abre el período del mes y registra la tarifa vigente por
   tipo de servicio.
2. La **Secretaria** registra al cliente y le asigna un contador con su predio.
3. El **Lector** entra desde el navegador del celular, ve la lista de contadores
   pendientes agrupada por sector, elige uno.
4. El sistema muestra la última lectura registrada y el lector ingresa la actual.
   Se valida que no sea menor que la anterior.
5. Se calcula `consumo = actual − anterior`, se resuelve la tarifa vigente según
   el tipo de servicio del contador, y se calcula el monto.
6. Se emite el recibo con su número correlativo y queda imprimible.
7. La **Secretaria** busca el recibo pendiente y registra el pago.
8. El **dashboard** muestra el estado de cuenta por cliente.

### Cálculo del monto

Si el consumo entra dentro del volumen incluido del tipo de servicio, se cobra la
cuota mínima. Si lo excede, se cobra la cuota mínima más el excedente al precio
unitario de la tarifa de tipo `exceso`.

```
consumo ≤ volumen incluido  →  monto = cuota mínima
consumo > volumen incluido  →  monto = cuota mínima + (excedente × precio de exceso)
```

`volumen_incluido_litros` está en litros y las lecturas en metros cúbicos, por eso
se convierte dividiendo entre 1000.

## Instalación local

### Requisitos

- PHP 8.2 o superior
- Composer
- MySQL o MariaDB
- Git

En Windows, [Laragon](https://laragon.org/) trae PHP, MariaDB y Apache en un solo
instalador.

### Pasos

```bash
git clone https://github.com/KravenTA/proyecto-agua-keko.git
cd proyecto-agua-keko
composer install
```

Crear la base de datos:

```sql
CREATE DATABASE oficina_agua CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Copiar el archivo de configuración:

```bash
cp env .env
```

En `.env`, descomentar y ajustar estas líneas (quitarles el `#` del inicio):

```
CI_ENVIRONMENT = development

database.default.hostname = localhost
database.default.database = oficina_agua
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

En Laragon el usuario por defecto es `root` sin contraseña.

Crear las tablas y cargar datos de ejemplo:

```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

Levantar el servidor:

```bash
php spark serve
```

El sistema queda en `http://localhost:8080`.

### Credenciales de desarrollo

| Correo | Rol | Contraseña |
|---|---|---|
| admin@oficinaagua.test | Administrador | admin123 |
| secretaria@oficinaagua.test | Secretaria | admin123 |
| lector@oficinaagua.test | Lector | admin123 |

**Estas credenciales son solo para desarrollo. Cambiarlas antes de desplegar.**

### Datos de ejemplo

`DatabaseSeeder` carga 25 clientes, 5 sectores, 25 servicios con sus contadores,
4 tarifas (una por tipo de servicio), dos períodos y las lecturas del período
cerrado más la mitad del abierto. Eso deja contadores pendientes para poder
probar el flujo del lector de inmediato.

Los seeders son repetibles: correrlos dos veces no duplica datos.

**Los montos de las tarifas son inventados** y sirven solo para probar. Las
tarifas reales las define la junta de la oficina.

## Estructura del proyecto

```
app/
├── Config/
│   ├── Routes.php          Rutas agrupadas por módulo, con filtros por rol
│   ├── Filters.php         Alias de auth y role
│   └── Pager.php           Plantilla de paginación para Bootstrap 5
├── Controllers/
│   ├── Auth.php            Login y logout
│   ├── Dashboard.php       Estado de cuenta por cliente
│   ├── Usuarios.php        Gestión de usuarios y roles
│   ├── Periodos.php        Apertura y cierre de períodos
│   ├── Tarifas.php         Tarifas con vigencia
│   ├── Clientes.php        Clientes y sus documentos
│   ├── Contadores.php      Contadores y predios
│   ├── Lecturas.php        Pendientes, registro e historial
│   ├── Recibos.php         Recibo imprimible y listado
│   └── Pagos.php           Registro de pagos
├── Filters/
│   ├── AuthFilter.php      Exige sesión iniciada
│   └── RoleFilter.php      Exige uno de los roles indicados
├── Models/                 Un modelo por entidad
├── Database/
│   ├── Migrations/         26 migraciones
│   └── Seeds/              10 seeders con datos de ejemplo
└── Views/
    ├── layouts/            header, footer, sidenav y barra móvil
    └── <módulo>/           index, form y _tabla por módulo

public/assets/css/oficina-agua.css   Ajustes propios sobre Soft UI
```

### Patrón de los listados

Los listados con búsqueda siguen la misma estructura: la tabla vive en una vista
parcial `_tabla.php`, que se usa tanto en la carga inicial como en las respuestas
del buscador. El controlador tiene un método `index()` que devuelve la página
completa y un `tabla()` que devuelve solo la parcial, y el JavaScript reemplaza
el contenedor sin recargar.

### Uso desde el celular

El flujo del Lector está pensado para el teléfono: las pantallas de pendientes y
registro usan tarjetas en lugar de tablas, agrupadas por sector, y el campo de
lectura abre el teclado numérico.

Las pantallas de oficina mantienen tablas con desplazamiento horizontal. En las
más anchas (clientes, contadores y lecturas) la primera columna queda fija al
desplazar, mediante la clase `tabla-ancha` en el contenedor.

## Base de datos

Entidades principales:

- `roles`, `usuarios` — acceso al sistema
- `clientes` — datos y documentos de respaldo
- `sectores`, `servicios` — el predio: liga un cliente con un sector y una dirección
- `contadores` — el medidor físico instalado en un servicio
- `periodos` — el mes de facturación; solo uno puede estar abierto
- `tarifas` — por tipo de servicio, con vigencia histórica
- `lecturas` — lectura anterior, actual, consumo y monto calculado
- `recibos` — documento emitido por lectura, con número correlativo y estado
- `pagos`, `depositos` — cobro
- `log_errores` y tablas de auditoría — trazabilidad

Un contador no apunta directamente a un cliente: la cadena es
`clientes → servicios → contadores`. Eso permite que un cliente tenga varios
predios y que un contador se reemplace en el mismo predio sin perder el
histórico.

## Despliegue

El despliegue se hace en una instancia EC2 con Apache, PHP 8.2 y MariaDB.

> Esta sección queda pendiente de completar con los pasos reales cuando se
> realice el despliegue (SDGODA-60). Abajo, los puntos a tener en cuenta.

- Cambiar `CI_ENVIRONMENT` a `production` en el `.env` del servidor
- Ajustar `app.baseURL` a la URL pública de la instancia
- Cambiar las contraseñas de los usuarios que crea el seeder
- Dar permisos de escritura a `writable/`
- Configurar Apache para servir desde `public/`, no desde la raíz del proyecto
- El `.env` no se versiona: hay que crearlo directamente en el servidor
- Correr `php spark migrate` y `php spark db:seed DatabaseSeeder` sobre la base
  del servidor

## Flujo de trabajo del equipo

Se trabaja con ramas y pull requests. Nunca directo sobre `master`.

```bash
git checkout master
git pull
git checkout -b feature/SDGODA-XX-descripcion-corta
```

Los commits usan prefijos: `feat:` para funcionalidad nueva, `fix:` para
correcciones, `chore:` para configuración y mantenimiento, `refactor:` para
reorganizar sin cambiar comportamiento, `style:` para formato, `docs:` para
documentación.

Antes de abrir el PR conviene traer master a la rama para resolver los conflictos
uno mismo:

```bash
git fetch origin
git merge origin/master
```