const baseUrl = window.bookRouletteConfig?.baseUrl || "";

function extractJsonPayload(text) {
    // En este proyecto PHP a veces devuelve warnings antes del JSON.
    // Con esto buscamos el inicio real de la respuesta util.
    const objectStart = text.indexOf("{");
    const arrayStart = text.indexOf("[");

    let start = -1;
    if (objectStart >= 0 && arrayStart >= 0) {
        start = Math.min(objectStart, arrayStart);
    } else {
        start = Math.max(objectStart, arrayStart);
    }

    if (start < 0) {
        throw new Error("La respuesta del servidor no es JSON valido.");
    }

    return JSON.parse(text.slice(start));
}

async function parseJson(response) {
    // Leemos el texto completo y despues extraemos el JSON.
    const rawText = await response.text();
    const data = extractJsonPayload(rawText);

    if (!response.ok) {
        throw new Error(data.error || "Ha ocurrido un error inesperado.");
    }

    return data;
}

export async function getBooks() {
    // Obtiene todos los libros del catalogo.
    const response = await fetch(`${baseUrl}/api/getBooks.php`);
    return parseJson(response);
}

export async function getSession() {
    // Devuelve si hay usuario logueado.
    const response = await fetch(`${baseUrl}/api/getSession.php`);
    return parseJson(response);
}

export async function getFavorites() {
    // Carga los favoritos del usuario actual.
    const response = await fetch(`${baseUrl}/api/getFavorites.php`);
    return parseJson(response);
}

export async function saveFavorite(idLibro) {
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
    const response = await fetch(`${baseUrl}/api/removeFavorite.php`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ id_favorito: idFavorito })
    });

    return parseJson(response);
}
