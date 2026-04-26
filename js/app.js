import { getBooks, getFavorites, getSession, removeFavorite, saveFavorite } from "./api.js";
import { calcularPuntuacion, obtenerTop } from "./scoring.js";
import { crearRuleta, girarRuleta } from "./roulette.js";

// Guardamos las opciones actuales de la ruleta y el ultimo libro ganador.
let opcionesActuales = [];
let ultimaGanadora = null;
let session = { loggedIn: false, user: null };

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
    if (!optionsListNode) {
        return;
    }

    if (!libros.length) {
        optionsListNode.innerHTML = "<p class='empty-state'>No hay opciones para esos criterios todavia.</p>";
        return;
    }

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
    // Pintamos la ficha final del libro que ha ganado.
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
    if (!favoritesListNode || !session.loggedIn) {
        return;
    }

    try {
        const favorites = await getFavorites();

        if (!favorites.length) {
            favoritesListNode.innerHTML = "<p class='empty-state'>Aun no has guardado libros favoritos.</p>";
            return;
        }

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
        favoritesListNode.innerHTML = `<p class='empty-state'>${error.message}</p>`;
    }
}

window.generarOpciones = async function () {
    const genero = document.getElementById("genero").value;
    const mood = document.getElementById("mood").value;

    if (!genero || !mood) {
        setStatus("Selecciona genero y mood para generar la recomendacion.");
        return;
    }

    try {
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

        ultimaGanadora = null;
        resultNode.innerHTML = `
            <h2>Todo listo para girar</h2>
            <p>La ruleta usara las mejores opciones segun tu seleccion.</p>
        `;
        favoriteButton?.classList.add("hidden");
    } catch (error) {
        setStatus(error.message);
    }
};

window.spin = function () {
    if (opcionesActuales.length === 0) {
        setStatus("Primero genera opciones antes de girar la ruleta.");
        return;
    }

    const index = Math.floor(Math.random() * opcionesActuales.length);

    // El mismo indice se usa para girar la ruleta y mostrar el resultado.
    girarRuleta(index, opcionesActuales.length);
    setStatus("Girando la ruleta...");

    setTimeout(() => {
        const ganador = opcionesActuales[index];
        ultimaGanadora = ganador;
        renderResult(ganador);
        setStatus("Ya tienes recomendacion.");
        favoriteButton?.classList.remove("hidden");
    }, 4000);
};

document.getElementById("generateButton")?.addEventListener("click", window.generarOpciones);
document.getElementById("spinButton")?.addEventListener("click", window.spin);

favoriteButton?.addEventListener("click", async () => {
    if (!ultimaGanadora) {
        setStatus("Gira la ruleta antes de guardar un favorito.");
        return;
    }

    try {
        const response = await saveFavorite(ultimaGanadora.id);
        setStatus(response.message || "Libro guardado.");
        await renderFavorites();
    } catch (error) {
        setStatus(error.message);
    }
});

favoritesListNode?.addEventListener("click", async (event) => {
    // Delegacion de eventos: escuchamos el click en toda la lista.
    const button = event.target.closest(".remove-favorite");
    if (!button) {
        return;
    }

    try {
        await removeFavorite(Number(button.dataset.id));
        setStatus("Favorito eliminado.");
        await renderFavorites();
    } catch (error) {
        setStatus(error.message);
    }
});

window.addEventListener("load", async () => {
    // Al entrar mostramos una ruleta inicial de ejemplo.
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
