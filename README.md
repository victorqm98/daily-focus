# FocusDaily

Una plataforma de diario personal compartido con amigos íntimos donde hay un intercambio: para ver los posts de tus amigos, primero debes escribir el tuyo.

## Características Principales

- **Registro y autenticación de usuarios**
- **Sistema de amistades limitado** (máximo 5 amigos)
- **Post diario único** (un post por día por usuario)
- **Acceso condicional** (debes postear para ver posts de amigos)
- **Posts temporales** (se archivan automáticamente después de 24 horas)
- **Búsqueda de usuarios** para encontrar amigos
- **Arquitectura hexagonal** con PHP y Symfony

## Reglas de Negocio

1. Un usuario puede tener máximo 5 amigos
2. Solo se puede escribir un post por día
3. Para ver los posts de tus amigos, primero debes escribir tu post del día
4. Los posts no se pueden editar ni borrar (para evitar trampa)
5. Los posts se archivan automáticamente después de 24 horas
6. Se pueden buscar usuarios por nombre de usuario

## Installation

### Clone the project

```bash
git clone git@github.com:victorqm98/daily-focus.git
```

### Install and Run the project

```bash
make installation
```

### Connect the database

- Database: daily-focus
- UserName: challenge
- Password: challenge

## API Endpoints

### User Management
- `POST /api/users/register` - Registrar nuevo usuario
- `POST /api/users/login` - Iniciar sesión
- `GET /api/users/search?username=` - Buscar usuarios

### Friendship Management
- `POST /api/friendships/request` - Enviar solicitud de amistad
- `POST /api/friendships/accept` - Aceptar solicitud de amistad

### Post Management
- `POST /api/posts` - Crear post diario
- `GET /api/posts/friends?user_id=` - Ver posts de amigos (requiere haber posteado)

### Health Check
- `GET /health` - Estado del servicio

## Tests

```bash
make tests
```

## Arquitectura

El proyecto sigue arquitectura hexagonal (puertos y adaptadores) con:

- **Dominio**: Lógica de negocio pura
- **Aplicación**: Casos de uso y comandos
- **Infraestructura**: Implementaciones de repositorios, controladores
- **Tests**: Cobertura de dominio y casos de uso

### Estructura de Directorios

```
src/
├── User/
│   ├── Domain/
│   ├── Application/
│   └── Infrastructure/
├── Friendship/
│   ├── Domain/
│   ├── Application/
│   └── Infrastructure/
├── Post/
│   ├── Domain/
│   ├── Application/
│   └── Infrastructure/
└── Shared/
    └── Domain/
        └── ValueObjects/
```
