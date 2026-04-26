-- Script base del proyecto BookRoulette.

CREATE DATABASE IF NOT EXISTS bookroulette;
USE bookroulette;

CREATE TABLE IF NOT EXISTS libros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255),
    autor VARCHAR(255),
    genero VARCHAR(100),
    descripcion TEXT,
    tono INT,
    profundidad INT,
    energia INT,
    isbn_libro VARCHAR(13) UNIQUE,
    enlace_compra VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('usuario', 'admin') NOT NULL DEFAULT 'usuario',
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS favoritos (
    id_favorito INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_libro INT NOT NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_favorito (id_usuario, id_libro),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_libro) REFERENCES libros(id) ON DELETE CASCADE
);

INSERT INTO libros (titulo, autor, genero, descripcion, tono, profundidad, energia, isbn_libro, enlace_compra)
VALUES
    ('Dracula', 'Bram Stoker', 'terror', 'Clasico gotico con ambiente oscuro y tension constante.', 2, 9, 8, NULL, 'https://www.casadellibro.com/'),
    ('El Hobbit', 'J.R.R. Tolkien', 'fantasia', 'Aventura fantastica con viaje, humor y crecimiento del protagonista.', 8, 6, 5, NULL, 'https://www.casadellibro.com/'),
    ('Harry Potter', 'J.K. Rowling', 'fantasia', 'Magia, amistad y descubrimiento personal en un internado muy especial.', 9, 5, 6, NULL, 'https://www.casadellibro.com/'),
    ('It', 'Stephen King', 'terror', 'Novela intensa de terror psicologico y traumas de infancia.', 3, 10, 9, NULL, 'https://www.casadellibro.com/'),
    ('Dune', 'Frank Herbert', 'ciencia ficcion', 'Politica, supervivencia y poder en un universo futurista complejo.', 4, 10, 7, NULL, 'https://www.casadellibro.com/'),
    ('Orgullo y prejuicio', 'Jane Austen', 'romance', 'Relacion sentimental con ironia social y personajes muy marcados.', 8, 7, 4, NULL, 'https://www.casadellibro.com/'),
    ('Sherlock Holmes: Estudio en escarlata', 'Arthur Conan Doyle', 'misterio', 'Investigacion detectivesca con ritmo agil y observacion constante.', 5, 7, 8, NULL, 'https://www.casadellibro.com/'),
    ('La isla del tesoro', 'Robert Louis Stevenson', 'aventura', 'Historia clasica de viajes, mapas y busqueda de tesoros.', 7, 5, 9, NULL, 'https://www.casadellibro.com/');
