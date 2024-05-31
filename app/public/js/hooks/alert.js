document.addEventListener("DOMContentLoaded", () => {
    const alerts = document.querySelectorAll(".alert");
    
    if (!alerts) {
        return;
    }

    // Hider after 5 seconds
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.add("fade");
        }, 3000);
        setTimeout(() => {
            alert.style.display = "none";
        }, 3500);
    });
});