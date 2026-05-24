-- Ampliacion idempotente del catalogo para bases de datos ya creadas.
-- Inserta solo los libros que no existan ya con el mismo titulo y autor.

USE bookroulette;

CREATE TEMPORARY TABLE catalogo_extra (
    titulo VARCHAR(255),
    autor VARCHAR(255),
    genero VARCHAR(100),
    descripcion TEXT,
    tono INT,
    profundidad INT,
    energia INT,
    isbn_libro VARCHAR(13),
    enlace_compra VARCHAR(255)
);

INSERT INTO catalogo_extra (titulo, autor, genero, descripcion, tono, profundidad, energia, isbn_libro, enlace_compra)
VALUES
    ('Dracula', 'Bram Stoker', 'terror', 'Clasico gotico con ambiente oscuro y tension constante.', 2, 9, 8, NULL, 'https://www.casadellibro.com/'),
    ('Frankenstein', 'Mary Shelley', 'terror', 'Historia gotica sobre ciencia, soledad y responsabilidad moral.', 3, 10, 6, NULL, 'https://www.casadellibro.com/'),
    ('El resplandor', 'Stephen King', 'terror', 'Terror psicologico en un hotel aislado con tension creciente.', 2, 8, 8, NULL, 'https://www.casadellibro.com/'),
    ('It', 'Stephen King', 'terror', 'Novela intensa de terror psicologico y traumas de infancia.', 3, 10, 9, NULL, 'https://www.casadellibro.com/'),
    ('La llamada de Cthulhu', 'H.P. Lovecraft', 'terror', 'Horror cosmico breve, inquietante y lleno de misterio.', 2, 8, 7, NULL, 'https://www.casadellibro.com/'),
    ('Nuestra parte de noche', 'Mariana Enriquez', 'terror', 'Terror literario, familiar y politico con una atmosfera opresiva.', 2, 10, 7, NULL, 'https://www.casadellibro.com/'),
    ('El Hobbit', 'J.R.R. Tolkien', 'fantasia', 'Aventura fantastica con viaje, humor y crecimiento del protagonista.', 8, 6, 5, NULL, 'https://www.casadellibro.com/'),
    ('Harry Potter y la piedra filosofal', 'J.K. Rowling', 'fantasia', 'Magia, amistad y descubrimiento personal en un internado muy especial.', 9, 5, 6, NULL, 'https://www.casadellibro.com/'),
    ('El nombre del viento', 'Patrick Rothfuss', 'fantasia', 'Fantasia de aprendizaje, talento y memoria con tono melancolico.', 7, 8, 6, NULL, 'https://www.casadellibro.com/'),
    ('La historia interminable', 'Michael Ende', 'fantasia', 'Viaje imaginativo, emotivo y luminoso sobre fantasia y deseo.', 8, 7, 5, NULL, 'https://www.casadellibro.com/'),
    ('Un mago de Terramar', 'Ursula K. Le Guin', 'fantasia', 'Fantasia introspectiva sobre poder, equilibrio y madurez.', 6, 9, 5, NULL, 'https://www.casadellibro.com/'),
    ('Juego de tronos', 'George R.R. Martin', 'fantasia', 'Intriga politica, conflictos familiares y fantasia de tono adulto.', 4, 9, 8, NULL, 'https://www.casadellibro.com/'),
    ('Dune', 'Frank Herbert', 'ciencia ficcion', 'Politica, supervivencia y poder en un universo futurista complejo.', 4, 10, 7, NULL, 'https://www.casadellibro.com/'),
    ('Fundacion', 'Isaac Asimov', 'ciencia ficcion', 'Ideas historicas y cientificas aplicadas al futuro de una civilizacion.', 6, 9, 5, NULL, 'https://www.casadellibro.com/'),
    ('El marciano', 'Andy Weir', 'ciencia ficcion', 'Supervivencia espacial con humor, ingenio y ritmo agil.', 8, 6, 8, NULL, 'https://www.casadellibro.com/'),
    ('Neuromante', 'William Gibson', 'ciencia ficcion', 'Cyberpunk oscuro, urbano y veloz sobre tecnologia y poder.', 3, 8, 9, NULL, 'https://www.casadellibro.com/'),
    ('Solaris', 'Stanislaw Lem', 'ciencia ficcion', 'Contacto extraterrestre y meditacion filosofica sobre la memoria.', 4, 10, 4, NULL, 'https://www.casadellibro.com/'),
    ('La mano izquierda de la oscuridad', 'Ursula K. Le Guin', 'ciencia ficcion', 'Ciencia ficcion antropologica, politica y profundamente reflexiva.', 5, 10, 4, NULL, 'https://www.casadellibro.com/'),
    ('Orgullo y prejuicio', 'Jane Austen', 'romance', 'Relacion sentimental con ironia social y personajes muy marcados.', 8, 7, 4, NULL, 'https://www.casadellibro.com/'),
    ('Jane Eyre', 'Charlotte Bronte', 'romance', 'Romance gotico, emocional y de crecimiento personal.', 6, 9, 5, NULL, 'https://www.casadellibro.com/'),
    ('Como agua para chocolate', 'Laura Esquivel', 'romance', 'Amor, cocina y realismo magico con energia emocional.', 8, 7, 5, NULL, 'https://www.casadellibro.com/'),
    ('Bajo la misma estrella', 'John Green', 'romance', 'Historia romantica contemporanea, emotiva y vulnerable.', 6, 7, 4, NULL, 'https://www.casadellibro.com/'),
    ('Normal People', 'Sally Rooney', 'romance', 'Relacion intima y moderna con mucha tension emocional.', 5, 8, 3, NULL, 'https://www.casadellibro.com/'),
    ('Rojo, blanco y sangre azul', 'Casey McQuiston', 'romance', 'Comedia romantica actual, luminosa y de lectura agil.', 9, 5, 6, NULL, 'https://www.casadellibro.com/'),
    ('Sherlock Holmes: Estudio en escarlata', 'Arthur Conan Doyle', 'misterio', 'Investigacion detectivesca con ritmo agil y observacion constante.', 5, 7, 8, NULL, 'https://www.casadellibro.com/'),
    ('Asesinato en el Orient Express', 'Agatha Christie', 'misterio', 'Caso clasico de deduccion con sospechosos y giro final.', 6, 7, 6, NULL, 'https://www.casadellibro.com/'),
    ('El nombre de la rosa', 'Umberto Eco', 'misterio', 'Misterio medieval, filosofico y denso con investigacion intelectual.', 4, 10, 5, NULL, 'https://www.casadellibro.com/'),
    ('La chica del tren', 'Paula Hawkins', 'misterio', 'Thriller psicologico contemporaneo con narracion inquietante.', 4, 7, 8, NULL, 'https://www.casadellibro.com/'),
    ('Los hombres que no amaban a las mujeres', 'Stieg Larsson', 'misterio', 'Investigacion oscura, social y con ritmo de thriller.', 3, 8, 9, NULL, 'https://www.casadellibro.com/'),
    ('El misterio de la cripta embrujada', 'Eduardo Mendoza', 'misterio', 'Misterio disparatado, ligero y humoristico con ritmo rapido.', 8, 4, 7, NULL, 'https://www.casadellibro.com/'),
    ('La isla del tesoro', 'Robert Louis Stevenson', 'aventura', 'Historia clasica de viajes, mapas y busqueda de tesoros.', 7, 5, 9, NULL, 'https://www.casadellibro.com/'),
    ('Los tres mosqueteros', 'Alexandre Dumas', 'aventura', 'Duelos, amistad y conspiraciones con energia clasica.', 8, 5, 9, NULL, 'https://www.casadellibro.com/'),
    ('Viaje al centro de la Tierra', 'Jules Verne', 'aventura', 'Exploracion cientifica y fantastica con asombro constante.', 7, 5, 8, NULL, 'https://www.casadellibro.com/'),
    ('La vuelta al mundo en 80 dias', 'Jules Verne', 'aventura', 'Aventura optimista, dinamica y viajera alrededor del mundo.', 8, 4, 9, NULL, 'https://www.casadellibro.com/'),
    ('El conde de Montecristo', 'Alexandre Dumas', 'aventura', 'Venganza, intriga y transformacion personal en una trama amplia.', 5, 8, 8, NULL, 'https://www.casadellibro.com/'),
    ('Moby Dick', 'Herman Melville', 'aventura', 'Viaje obsesivo, simbolico y exigente en alta mar.', 4, 10, 6, NULL, 'https://www.casadellibro.com/'),
    ('1984', 'George Orwell', 'clasico', 'Distopia politica oscura sobre vigilancia, lenguaje y poder.', 2, 10, 6, NULL, 'https://www.casadellibro.com/'),
    ('Matar a un ruisenor', 'Harper Lee', 'clasico', 'Novela humana sobre justicia, infancia y prejuicio.', 7, 9, 4, NULL, 'https://www.casadellibro.com/'),
    ('Cien anos de soledad', 'Gabriel Garcia Marquez', 'clasico', 'Saga familiar exuberante, magica y profundamente simbolica.', 7, 10, 5, NULL, 'https://www.casadellibro.com/'),
    ('El principito', 'Antoine de Saint-Exupery', 'clasico', 'Fabula breve, luminosa y melancolica sobre afecto y mirada.', 8, 7, 3, NULL, 'https://www.casadellibro.com/'),
    ('Fahrenheit 451', 'Ray Bradbury', 'clasico', 'Distopia literaria con tension, rebeldia y amor por los libros.', 4, 9, 7, NULL, 'https://www.casadellibro.com/'),
    ('La metamorfosis', 'Franz Kafka', 'clasico', 'Relato breve, extrano y angustioso sobre identidad y exclusion.', 2, 9, 4, NULL, 'https://www.casadellibro.com/'),
    ('Sapiens', 'Yuval Noah Harari', 'ensayo', 'Recorrido accesible por historia, cultura y evolucion humana.', 6, 8, 4, NULL, 'https://www.casadellibro.com/'),
    ('El infinito en un junco', 'Irene Vallejo', 'ensayo', 'Ensayo literario, calido y curioso sobre la historia de los libros.', 8, 8, 3, NULL, 'https://www.casadellibro.com/'),
    ('Pensar rapido, pensar despacio', 'Daniel Kahneman', 'ensayo', 'Exploracion de sesgos, decisiones y mecanismos de pensamiento.', 5, 10, 3, NULL, 'https://www.casadellibro.com/'),
    ('Una breve historia de casi todo', 'Bill Bryson', 'ensayo', 'Divulgacion cientifica curiosa, entretenida y de gran alcance.', 8, 7, 5, NULL, 'https://www.casadellibro.com/'),
    ('El arte de amar', 'Erich Fromm', 'ensayo', 'Reflexion breve sobre amor, madurez y vinculos humanos.', 7, 9, 2, NULL, 'https://www.casadellibro.com/'),
    ('Mujeres que corren con los lobos', 'Clarissa Pinkola Estes', 'ensayo', 'Ensayo simbolico y emocional sobre arquetipos y relatos.', 6, 9, 4, NULL, 'https://www.casadellibro.com/'),
    ('Nada', 'Carmen Laforet', 'contemporanea', 'Retrato intimo, oscuro y sensible de juventud y desencanto.', 3, 9, 4, NULL, 'https://www.casadellibro.com/'),
    ('Tokio blues', 'Haruki Murakami', 'contemporanea', 'Novela melancolica sobre juventud, deseo y perdida.', 5, 8, 3, NULL, 'https://www.casadellibro.com/'),
    ('La sombra del viento', 'Carlos Ruiz Zafon', 'contemporanea', 'Misterio literario, gotico y emotivo en la Barcelona de posguerra.', 5, 8, 7, NULL, 'https://www.casadellibro.com/'),
    ('Patria', 'Fernando Aramburu', 'contemporanea', 'Drama social y familiar sobre memoria, violencia y convivencia.', 4, 10, 4, NULL, 'https://www.casadellibro.com/'),
    ('Los detectives salvajes', 'Roberto Bolano', 'contemporanea', 'Viaje literario, fragmentario y vitalista por poesia y juventud.', 6, 9, 6, NULL, 'https://www.casadellibro.com/'),
    ('La ridicula idea de no volver a verte', 'Rosa Montero', 'contemporanea', 'Duelo, memoria y pensamiento con tono cercano y reflexivo.', 6, 8, 3, NULL, 'https://www.casadellibro.com/');

INSERT INTO libros (titulo, autor, genero, descripcion, tono, profundidad, energia, isbn_libro, enlace_compra)
SELECT titulo, autor, genero, descripcion, tono, profundidad, energia, isbn_libro, enlace_compra
FROM catalogo_extra extra
WHERE NOT EXISTS (
    SELECT 1
    FROM libros actual
    WHERE actual.titulo = extra.titulo
      AND actual.autor = extra.autor
);

DROP TEMPORARY TABLE catalogo_extra;
