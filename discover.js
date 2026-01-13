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
            <div class="tags">
                ${(project.tags || []).map(tag => `<span>#${tag}</span>`).join("")}
            </div>
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

    if (action === "like") {
        showLikeNotification();
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
   LIKE NOTIFICATION
   ========================= */
function showLikeNotification() {
    const notif = document.createElement("div");
    notif.className = "like-notification";
    notif.textContent = "💖 Match! Anar al xat";

    notif.onclick = () => window.location.href = "chat.php";

    document.body.appendChild(notif);
    setTimeout(() => notif.remove(), 2500);
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
