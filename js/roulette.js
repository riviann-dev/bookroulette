export function crearRuleta(libros) {
    // Buscamos el contenedor visual de la ruleta en el HTML.
    const ruleta = document.getElementById("ruleta");

    if (!ruleta || !libros || libros.length === 0) {
        // Si no hay contenedor o no hay libros, no se puede dibujar nada.
        return;
    }

    // Paleta de colores que se va repitiendo si hay mas segmentos que colores.
    const colores = ["#ff7a59", "#f6bd60", "#84dcc6", "#7da6ff", "#f28482", "#90be6d"];
    // Cada libro ocupa la misma porcion del circulo.
    const tamano = 360 / libros.length;
    let gradiente = "conic-gradient(";

    // Limpiamos la ruleta y la devolvemos a la posicion inicial.
    ruleta.innerHTML = "";
    ruleta.style.transform = "rotate(0deg)";

    libros.forEach((libro, index) => {
        // Calculamos el inicio y final del segmento en grados.
        const start = index * tamano;
        const end = start + tamano;
        gradiente += `${colores[index % colores.length]} ${start}deg ${end}deg, `;

        // Cada titulo se coloca en el centro de su segmento.
        const label = document.createElement("div");
        label.className = "label";

        const angulo = start + tamano / 2;
        // La etiqueta se rota hasta su segmento, se aleja del centro
        // y luego se contrarrota para que el texto se lea horizontal.
        label.style.transform = `translate(-50%, -50%) rotate(${angulo}deg) translate(0, -110px) rotate(-${angulo}deg)`;
        // Recortamos titulos largos para que no se salgan del segmento.
        label.textContent = (libro.titulo || "Opcion").slice(0, 16);

        ruleta.appendChild(label);
    });

    // Cerramos el conic-gradient quitando la ultima coma.
    ruleta.style.background = gradiente.slice(0, -2) + ")";
}

export function girarRuleta(index, total) {
    // Esta funcion solo anima la ruleta; el libro ganador ya se eligio en app.js.
    const ruleta = document.getElementById("ruleta");
    if (!ruleta || total <= 0) {
        return;
    }

    const tamano = 360 / total;
    const angulo = index * tamano + tamano / 2;

    // Damos varias vueltas completas para que la animacion se vea natural
    // y al final dejamos el segmento ganador justo bajo el puntero.
    const giro = 360 * 5 + (360 - angulo);

    ruleta.style.transform = `rotate(${giro}deg)`;
}
