(function () {
    const authLink = document.querySelector("#auth-link");
    if (!authLink) {
        return;
    }
    const loginUrl = authLink.href;

    function getUser() {
        try {
            const user = JSON.parse(localStorage.getItem("nemuUser") || "null");
            return user && user.isLoggedIn && user.nim ? user : null;
        } catch (error) {
            console.error("Data login NEMU tidak valid:", error);
            return null;
        }
    }

    function updateAuthLink() {
        const user = getUser();

        if (user) {
            authLink.href = "#logout";
            authLink.textContent = "Keluar";
            authLink.setAttribute("aria-label", `Keluar dari akun ${user.nim}`);
            return;
        }

        const next = `${window.location.pathname}${window.location.search}${window.location.hash}`;
        authLink.href = `${loginUrl}?${new URLSearchParams({ next }).toString()}`;
        authLink.textContent = "Masuk";
        authLink.removeAttribute("aria-label");
    }

    authLink.addEventListener("click", function (event) {
        if (!getUser()) {
            return;
        }

        event.preventDefault();
        localStorage.removeItem("nemuUser");
        window.location.assign("/");
    });

    updateAuthLink();
})();
