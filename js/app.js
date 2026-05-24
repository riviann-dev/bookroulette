import { getBooks, getFavorites, getSession, removeFavorite, saveFavorite } from "./api.js";
import { calcularPuntuacion, obtenerTop } from "./scoring.js";
import { crearRuleta, girarRuleta } from "./roulette.js";

// Estado principal de la pantalla.
// opcionesActuales son los libros que entran en la ruleta despues de puntuar.
// ultimaGanadora guarda el libro que salio al girar, para poder marcarlo favorito.
let opcionesActuales = [];
let ultimaGanadora = null;
let session = { loggedIn: false, user: null };

// Referencias a elementos del HTML que se actualizan desde JavaScript.
const resultNode = document.getElementById("resultado");
const statusNode = document.getElementById("statusMessage");
const optionsListNode = document.getElementById("optionsList");
const favoritesListNode = document.getElementById("favoritesList");
const favoriteButton = document.getElementById("favoriteButton");

function setStatus(message) {
    // Mostramos mensajes cortos en pantalla en lugar de usar alert().
    if (statusNode) {
        statusNode.textContent = message;
    }
}

function renderOptions(libros) {
    // Pinta la lista de libros que han sido seleccionados para la ruleta.
    if (!optionsListNode) {
        return;
    }

    if (!libros.length) {
        optionsListNode.innerHTML = "<p class='empty-state'>No hay opciones para esos criterios todavia.</p>";
        return;
    }

    // Convertimos cada libro en una tarjeta sencilla con score, titulo, autor y genero.
    optionsListNode.innerHTML = libros.map((libro) => `
        <article class="book-option">
            <p class="book-rank">Score ${libro.score}</p>
            <h3>${libro.titulo}</h3>
            <p>${libro.autor}</p>
            <span>${libro.genero}</span>
        </article>
    `).join("");
}

function renderResult(libro) {
    // Pinta la ficha final del libro ganador despues del giro.
    // Algunos campos son opcionales, por eso se comprueba si existen antes de mostrarlos.
    resultNode.innerHTML = `
        <h2>${libro.titulo}</h2>
        <p class="result-author">${libro.autor}</p>
        <div class="metric-row">
            ${libro.isbn_libro ? `<span>ISBN: ${libro.isbn_libro}</span>` : ""}
            <span>Tono: ${libro.tono}</span>
            <span>Profundidad: ${libro.profundidad}</span>
            <span>Energia: ${libro.energia}</span>
        </div>
        <p class="helper-text">Genero: ${libro.genero}</p>
        ${libro.descripcion ? `<p class="result-description">${libro.descripcion}</p>` : ""}
        ${libro.enlace_compra ? `<a class="buy-link" href="${libro.enlace_compra}" target="_blank" rel="noopener noreferrer">Ver libro o comprar</a>` : ""}
    `;
}

async function renderFavorites() {
    // Si no hay zona de favoritos o el usuario no esta logueado, no hacemos nada.
    if (!favoritesListNode || !session.loggedIn) {
        return;
    }

    try {
        // Pedimos los favoritos a la API y los pintamos debajo de la ruleta.
        const favorites = await getFavorites();

        if (!favorites.length) {
            favoritesListNode.innerHTML = "<p class='empty-state'>Aun no has guardado libros favoritos.</p>";
            return;
        }

        // Cada boton "Quitar" lleva el id del favorito para poder eliminarlo despues.
        favoritesListNode.innerHTML = favorites.map((favorite) => `
            <article class="favorite-item">
                <div>
                    <h3>${favorite.titulo}</h3>
                    <p>${favorite.autor} - ${favorite.genero}</p>
                </div>
                <button class="ghost-btn remove-favorite" data-id="${favorite.id_favorito}" type="button">Quitar</button>
            </article>
        `).join("");
    } catch (error) {
        // Si hay un problema con la sesion o la API, lo mostramos en la misma seccion.
        favoritesListNode.innerHTML = `<p class='empty-state'>${error.message}</p>`;
    }
}

window.generarOpciones = async function () {
    // Leemos las opciones elegidas por el usuario en los selectores.
    const genero = document.getElementById("genero").value;
    const mood = document.getElementById("mood").value;

    if (!genero || !mood) {
        setStatus("Selecciona genero y mood para generar la recomendacion.");
        return;
    }

    try {
        // Traemos el catalogo completo y dejamos que scoring.js calcule las mejores opciones.
        const books = await getBooks();

        // 1. Calculamos la puntuacion de todos los libros.
        const scored = calcularPuntuacion(books, genero, mood);

        // 2. Nos quedamos con las mejores opciones para la ruleta.
        opcionesActuales = obtenerTop(scored);

        if (opcionesActuales.length === 0) {
            setStatus("No hay libros disponibles con esos criterios.");
            renderOptions([]);
            return;
        }

        // 3. Dibujamos la ruleta y la lista lateral con esas opciones.
        crearRuleta(opcionesActuales);
        renderOptions(opcionesActuales);
        setStatus("Opciones generadas. Ahora puedes girar la ruleta.");

        // Reiniciamos el resultado anterior para que no se pueda guardar un libro viejo.
        ultimaGanadora = null;
        resultNode.innerHTML = `
            <h2>Todo listo para girar</h2>
            <p>La ruleta usara las mejores opciones segun tu seleccion.</p>
        `;
        favoriteButton?.classList.add("hidden");
    } catch (error) {
        // Cualquier error de la API se comunica en el texto de estado.
        setStatus(error.message);
    }
};

window.spin = function () {
    // No tiene sentido girar si antes no se generaron opciones.
    if (opcionesActuales.length === 0) {
        setStatus("Primero genera opciones antes de girar la ruleta.");
        return;
    }

    // Elegimos al azar una posicion dentro de las opciones ya puntuadas.
    const index = Math.floor(Math.random() * opcionesActuales.length);

    // El mismo indice se usa para girar la ruleta y mostrar el resultado.
    girarRuleta(index, opcionesActuales.length);
    setStatus("Girando la ruleta...");

    setTimeout(() => {
        // Esperamos lo mismo que dura la animacion CSS para mostrar el resultado final.
        const ganador = opcionesActuales[index];
        ultimaGanadora = ganador;
        renderResult(ganador);
        setStatus("Ya tienes recomendacion.");
        favoriteButton?.classList.remove("hidden");
    }, 4000);
};

// Conectamos los botones principales con sus funciones.
document.getElementById("generateButton")?.addEventListener("click", window.generarOpciones);
document.getElementById("spinButton")?.addEventListener("click", window.spin);

favoriteButton?.addEventListener("click", async () => {
    // Solo se puede guardar favorito cuando ya hay un libro ganador.
    if (!ultimaGanadora) {
        setStatus("Gira la ruleta antes de guardar un favorito.");
        return;
    }

    try {
        const response = await saveFavorite(ultimaGanadora.id);
        setStatus(response.message || "Libro guardado.");
        // Recargamos favoritos para que el nuevo libro aparezca al momento.
        await renderFavorites();
    } catch (error) {
        setStatus(error.message);
    }
});

favoritesListNode?.addEventListener("click", async (event) => {
    // Delegacion de eventos: escuchamos el click en toda la lista.
    // Esto evita crear un listener distinto para cada boton de favoritos.
    const button = event.target.closest(".remove-favorite");
    if (!button) {
        return;
    }

    try {
        await removeFavorite(Number(button.dataset.id));
        setStatus("Favorito eliminado.");
        // Volvemos a pintar la lista para reflejar el borrado.
        await renderFavorites();
    } catch (error) {
        setStatus(error.message);
    }
});

window.addEventListener("load", async () => {
    // Al entrar mostramos una ruleta inicial de ejemplo.
    // Todavia no son recomendaciones reales: solo sirve para que la interfaz no este vacia.
    crearRuleta([
        { titulo: "Fantasia" },
        { titulo: "Terror" },
        { titulo: "Mood" },
        { titulo: "Sorpresa" }
    ]);

    try {
        // Si ya hay sesion abierta, cargamos favoritos automaticamente.
        session = await getSession();
        if (session.loggedIn) {
            await renderFavorites();
        }
    } catch (error) {
        setStatus(error.message);
    }
});
