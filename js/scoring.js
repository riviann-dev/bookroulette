// Tabla de perfiles emocionales.
// Cada estado de animo se traduce a tres valores ideales entre 1 y 10.
// Despues comparamos cada libro con estos valores para ver si encaja.
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
    // Calcula cercania entre el valor del libro y el valor ideal del mood.
    // Ejemplo: si el mood pide energia 9 y el libro tiene energia 8,
    // la diferencia es pequena y el libro recibe buena puntuacion.
    return Math.max(0, 10 - Math.abs(Number(value || 0) - target));
}

export function calcularPuntuacion(books, genero, mood) {
    // Cada mood tiene un "perfil ideal" de tono, profundidad y energia.
    const profile = moodProfiles[mood];

    return books.map((book) => {
        // Empezamos cada libro desde cero y vamos sumando o restando puntos.
        let score = 0;

        // El genero es el criterio principal. Si coincide suma bastante;
        // si no coincide, se penaliza para que salga por debajo.
        if (book.genero === genero) {
            score += 24;
        } else {
            score -= 8;
        }

        if (profile) {
            // Sumamos puntos segun lo cerca que este cada atributo del libro
            // al perfil emocional elegido por el usuario.
            // El tono pesa un poco mas porque marca mucho la sensacion de lectura.
            score += proximityScore(book.tono, profile.tono) * 1.5;
            score += proximityScore(book.profundidad, profile.profundidad) * 1.25;
            score += proximityScore(book.energia, profile.energia) * 1.25;
        }

        return {
            ...book,
            // Redondeamos a un decimal para mostrar un score limpio en pantalla.
            score: Math.round(score * 10) / 10
        };
    });
}

export function obtenerTop(scored, limite = 4) {
    // Ordenamos los libros de mejor a peor puntuacion.
    // Por defecto devolvemos 4 porque son las opciones que entran en la ruleta.
    return [...scored]
        .sort((a, b) => b.score - a.score)
        .slice(0, limite);
}
