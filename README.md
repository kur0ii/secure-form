# Secure-Form

This project aims to create a secure login form with robust protection against attacks.

## Prerequisites

- `PHP`
- `database server` personally, I've chosen to use phpAdmins to create my database.
- `database structure` users(id,username,pwd,attempts,last_attempt,status_lock)

## Project Structure

The project is organized into several files, each with a specific role:

- **/img :** directory for images
- **config.php :** database configuration file
- **login.php :** file for the login form
- **index.html :** main file
- **register.php :** file for the registration form
- **styles.css :** style file for layout
- **database.sql :** file describing the database structuration
- **README.md :** this file

## How to Connect?

To connect, go to `register.php` once you have created your account go to `login.php`.

note: the username must be at least 5 characters long and the password must be at least 8 characters long, including at least one upper-case letter, one lower-case letter, one number and one special character.

## Author

Michael Diop Rogandji
