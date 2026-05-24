// Ruta base de la aplicacion. Viene desde index.php para que los fetch funcionen
// aunque el proyecto este dentro de /bookroulette y no en la raiz del servidor.
const baseUrl = window.bookRouletteConfig?.baseUrl || "";

function extractJsonPayload(text) {
    // En desarrollo PHP puede imprimir algun warning antes del JSON.
    // Esta funcion localiza donde empieza realmente el objeto o array JSON.
    const objectStart = text.indexOf("{");
    const arrayStart = text.indexOf("[");

    // Si aparecen objeto y array, nos quedamos con el que salga primero.
    // Si solo aparece uno, usamos ese como inicio de la respuesta util.
    let start = -1;
    if (objectStart >= 0 && arrayStart >= 0) {
        start = Math.min(objectStart, arrayStart);
    } else {
        start = Math.max(objectStart, arrayStart);
    }

    if (start < 0) {
        // Si no encontramos ni { ni [, la respuesta no puede convertirse a JSON.
        throw new Error("La respuesta del servidor no es JSON valido.");
    }

    return JSON.parse(text.slice(start));
}

async function parseJson(response) {
    // Centraliza la lectura de respuestas de la API.
    // Asi todos los endpoints se tratan igual y los errores se muestran en pantalla.
    const rawText = await response.text();
    const data = extractJsonPayload(rawText);

    if (!response.ok) {
        // Si PHP envio un codigo de error, usamos su mensaje para informar al usuario.
        throw new Error(data.error || "Ha ocurrido un error inesperado.");
    }

    return data;
}

export async function getBooks() {
    // Pide a PHP todos los libros disponibles para poder puntuarlos en el navegador.
    const response = await fetch(`${baseUrl}/api/getBooks.php`);
    return parseJson(response);
}

export async function getSession() {
    // Consulta si existe una sesion activa y devuelve los datos basicos del usuario.
    const response = await fetch(`${baseUrl}/api/getSession.php`);
    return parseJson(response);
}

export async function getFavorites() {
    // Recupera los favoritos del usuario logueado para pintarlos en la pagina.
    const response = await fetch(`${baseUrl}/api/getFavorites.php`);
    return parseJson(response);
}

export async function saveFavorite(idLibro) {
    // Envia el id del libro ganador para guardarlo en la tabla favoritos.
    const response = await fetch(`${baseUrl}/api/saveFavorite.php`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ id_libro: idLibro })
    });

    return parseJson(response);
}

export async function removeFavorite(idFavorito) {
    // Envia el id del favorito, no el del libro, porque se elimina la relacion guardada.
    const response = await fetch(`${baseUrl}/api/removeFavorite.php`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ id_favorito: idFavorito })
    });

    return parseJson(response);
}
