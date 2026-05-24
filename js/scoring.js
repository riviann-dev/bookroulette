const moodProfiles = {
    feliz: { tono: 9, profundidad: 5, energia: 6 },
    relajado: { tono: 8, profundidad: 4, energia: 3 },
    reflexivo: { tono: 5, profundidad: 10, energia: 3 },
    curioso: { tono: 6, profundidad: 8, energia: 5 },
    aventurero: { tono: 7, profundidad: 5, energia: 9 },
    romantico: { tono: 8, profundidad: 7, energia: 4 },
    intenso: { tono: 4, profundidad: 6, energia: 10 },
    oscuro: { tono: 2, profundidad: 8, energia: 8 }
};

function proximityScore(value, target) {
    // Cuanto mas cerca esta el valor del libro al valor ideal del mood,
    // mas puntos recibe.
    return Math.max(0, 10 - Math.abs(Number(value || 0) - target));
}

export function calcularPuntuacion(books, genero, mood) {
    // Cada mood tiene un "perfil ideal" de tono, profundidad y energia.
    const profile = moodProfiles[mood];

    return books.map((book) => {
        let score = 0;

        // El genero es el filtro principal, por eso pesa mas.
        if (book.genero === genero) {
            score += 24;
        } else {
            score -= 8;
        }

        if (profile) {
            // Sumamos puntos segun lo cerca que este cada atributo del libro
            // al perfil emocional elegido por el usuario.
            score += proximityScore(book.tono, profile.tono) * 1.5;
            score += proximityScore(book.profundidad, profile.profundidad) * 1.25;
            score += proximityScore(book.energia, profile.energia) * 1.25;
        }

        return {
            ...book,
            score: Math.round(score * 10) / 10
        };
    });
}

export function obtenerTop(scored, limite = 4) {
    // Ordenamos de mayor a menor y nos quedamos con las mejores opciones.
    return [...scored]
        .sort((a, b) => b.score - a.score)
        .slice(0, limite);
}
