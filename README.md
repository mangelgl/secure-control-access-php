# 🛡️ Sistema de Control de Acceso Seguro en PHP

Un sistema de autenticación y control de acceso implementado en PHP, diseñado con un enfoque estricto en la seguridad web. Este repositorio proporciona una base robusta para gestionar inicios de sesión, previniendo activamente ataques comunes como el **Secuestro de Sesión (Session Hijacking)**, la Inyección SQL y ataques de fuerza bruta. 

Este repositorio es una práctica de seguridad web hecha en el curso de especialización de ciberseguridad. No es definitiva, todavía puede recibir actualizaciones.

## ✨ Características Principales

El sistema está defendido mediante una estrategia de "Defensa en Profundidad" (Defense in Depth), dividiendo la seguridad en dos capas:

### 🔒 Seguridad a Nivel de Aplicación (PHP)
* **Gestión Segura de Cookies:** Configuración de cookies de sesión con las banderas `HttpOnly` y `SameSite`.
* **Almacenamiento Seguro de Contraseñas:** Uso de los algoritmos nativos de PHP (`password_hash` y `password_verify`) utilizando Bcrypt.
* **Gestión del tiempo de vida de las Cookies:** Cookies con un tiempo de vida máximo de 6 horas y 24 minutos de inactividad.
* **Prevención de Inyección SQL:** Uso exclusivo de consultas preparadas a través de PDO.

### 🧱 Seguridad a Nivel de Infraestructura (Proxy Inverso Nginx + WAF)
* **Web Application Firewall (WAF):** Integración de ModSecurity con el conjunto de reglas OWASP CRS (Core Rule Set) para detectar y bloquear proactivamente ataques de capa 7 (LFI, RCE, SQLi avanzado, Path Traversal).
* **Rate Limiting:** Limitación de tasa de peticiones (Rate Limit) por IP para mitigar ataques de Denegación de Servicio (DoS) y ataques automatizados de fuerza bruta.
* **Cabeceras de Seguridad HTTP:** Implementación de políticas estrictas incluyendo `Content-Security-Policy` (CSP), `X-Frame-Options` (anti-Clickjacking), `X-Content-Type-Options` y `Referrer-Policy`.
* **Hardening del Servidor:** Ocultación de firmas y versiones del servidor (`server_tokens off`) para dificultar el reconocimiento por parte de atacantes.
* **Logs Estructurados para SIEM:** Registros de acceso y de ModSecurity configurados en formato JSON, optimizados para su ingesta y análisis en herramientas de auditoría o SIEM.

## 🛠️ Requisitos del Sistema

* Docker (he utilizado la v29.0.3)
* Docker Compose (he utilizado la v2.40.3)
