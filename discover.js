import { showNotification } from './notificaciones.js';

let projectQueue = [...(window.PROJECTS || [])];

/* =========================
   CREATE CARD
   ========================= */
function createProjectCard(project) {
    const card = document.createElement("div");
    card.className = "project-card";

    card.innerHTML = `
        <video src="${project.Video}" autoplay muted loop></video>

        <div class="project-info hidden">
            <p>${project.Description}</p>
        </div>

        <div class="actions">
            <button class="nope">Nope</button>
            <button class="like">Like</button>
        </div>
    `;

    addCardEvents(card);
    return card;
}

/* =========================
   EVENTS
   ========================= */
function addCardEvents(card) {
    card.querySelector(".like").onclick = () => handleAction(card, "like");
    card.querySelector(".nope").onclick = () => handleAction(card, "nope");
}

function handleAction(card, action) {
    card.classList.add(action === "like" ? "swipe-right" : "swipe-left");

    setTimeout(() => {
        card.remove();
        loadNextProject();
    }, 400);

    /* =========================
        LIKE NOTIFICATION
    ========================= */

    if (action === "like") {
        showNotification("info","💖 Match! Anar al xat");
    }
}

/* =========================
   LOAD NEXT PROJECT
   ========================= */
function loadNextProject() {
    if (projectQueue.length === 0) return;

    const project = projectQueue.shift();
    const card = createProjectCard(project);

    document.getElementById("discover-container").appendChild(card);
}

/* =========================
   DETAILS TOGGLE
   ========================= */
document.getElementById("nav-details")?.addEventListener("click", () => {
    const info = document.querySelector(".project-card .project-info");
    if (info) info.classList.toggle("hidden");
});

/* =========================
   INIT
   ========================= */
loadNextProject();
