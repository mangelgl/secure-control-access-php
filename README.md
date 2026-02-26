# 🛡️ Sistema de Control de Acceso Seguro en PHP

Un sistema de autenticación y control de acceso implementado en PHP, diseñado con un enfoque estricto en la seguridad web. Este repositorio proporciona una base robusta para gestionar inicios de sesión, previniendo activamente ataques comunes como el **Secuestro de Sesión (Session Hijacking)**, la Inyección SQL y ataques de fuerza bruta. Este repositorio es una práctica de seguridad web hecha en el curso de especialización de ciberseguridad. No es definitiva, todavía puede recibir actualizaciones.

## ✨ Características Principales

* **Gestión Segura de Cookies:** Configuración de cookies de sesión con la banderas `HttpOnly`.
* **Almacenamiento Seguro de Contraseñas:** Uso de los algoritmos nativos de PHP (`password_hash` y `password_verify`) utilizando Bcrypt.
* **Prevención de Inyección SQL:** Uso exclusivo de consultas preparadas a través de PDO.

## 🛠️ Requisitos del Sistema

* Docker (he utilizado la v29.0.3)
* Docker Compose (he utilizado la v2.40.3)
