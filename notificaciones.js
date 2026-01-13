export function showNotification(type, message) {
    // Crear contenedor
    const notif = document.createElement('div');
    notif.className = `notification ${type} show`;

    // Texto
    const text = document.createElement('span');
    text.textContent = message;
    notif.appendChild(text);

    // Botón de cerrar
    const closeBtn = document.createElement('button');
    closeBtn.className = 'close-btn';
    closeBtn.innerHTML = '&times;';
    closeBtn.onclick = () => notif.remove();
    notif.appendChild(closeBtn);

    // Añadir al body
    document.body.appendChild(notif);
}