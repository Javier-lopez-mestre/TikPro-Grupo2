let projectQueue = [];
let isLoading = false;

/* =========================
   PRELOAD PROJECTS (AJAX)
   ========================= */
async function preloadProjects(n = 2) {
    if (isLoading) return;
    isLoading = true;

    const response = await fetch(`api/get_project.php?n=${n}`);
    const data = await response.json();

    projectQueue.push(...data);
    isLoading = false;
}

/* =========================
   CREATE CARD (DOM)
   ========================= */
function createProjectCard(project) {
    const card = document.createElement("div");
    card.className = "project-card";

    card.innerHTML = `
        <video src="${project.video}" autoplay muted loop></video>

        <div class="project-info hidden">
            <p>${project.description}</p>
            <div class="tags">
                ${project.tags.map(tag => `<span>#${tag}</span>`).join("")}
            </div>
        </div>

        <div class="actions">
            <button class="nope">Nope</button>
            <button class="like">Like</button>
        </div>
    `;

    addCardEvents(card, project.id);
    return card;
}

/* =========================
   EVENTS
   ========================= */
function addCardEvents(card, projectId) {
    card.querySelector(".like").onclick = () => handleAction(card, projectId, "like");
    card.querySelector(".nope").onclick = () => handleAction(card, projectId, "nope");
}

function handleAction(card, projectId, action) {
    card.classList.add(action === "like" ? "swipe-right" : "swipe-left");

    setTimeout(() => {
        card.remove();
        loadNextProject();
    }, 400);

    if (action === "like") {
        showLikeNotification();
        sendLike(projectId);
    }
}

/* =========================
   LOAD NEXT PROJECT
   ========================= */
function loadNextProject() {
    if (projectQueue.length === 0) {
        preloadProjects();
        return;
    }

    const project = projectQueue.shift();
    const card = createProjectCard(project);

    document.getElementById("discover-container").appendChild(card);

    if (projectQueue.length < 2) {
        preloadProjects();
    }
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
   SEND LIKE (AJAX)
   ========================= */
function sendLike(projectId) {
    fetch("api/like_project.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ projectId })
    });
}

/* =========================
   DETAILS TOGGLE
   ========================= */
document.getElementById("nav-details").onclick = () => {
    const info = document.querySelector(".project-card .project-info");
    if (info) info.classList.toggle("hidden");
};

/* =========================
   INIT
   ========================= */
preloadProjects();
loadNextProject();
