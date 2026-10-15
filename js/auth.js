(function () {
    const AUTH_KEY = "nemuUser";

    function getUser() {
        const storedUser = localStorage.getItem(AUTH_KEY);
        if (!storedUser) {
            return null;
        }

        try {
            const user = JSON.parse(storedUser);
            return user && user.isLoggedIn && user.nim ? user : null;
        } catch (error) {
            console.error("Data login NEMU tidak valid:", error);
            return null;
        }
    }

    function isLoggedIn() {
        return Boolean(getUser());
    }

    // PIC sengaja tidak disimpan. Hanya NIM yang dicatat untuk mock login.
    function loginUser(nim) {
        const user = {
            isLoggedIn: true,
            nim: String(nim).trim()
        };

        localStorage.setItem(AUTH_KEY, JSON.stringify(user));
        return user;
    }

    function logoutUser() {
        localStorage.removeItem(AUTH_KEY);
    }

    function currentPageTarget() {
        return `${window.location.pathname.split("/").pop()}${window.location.search}${window.location.hash}`;
    }

    function requireLogin(reason, returnTo) {
        if (isLoggedIn()) {
            return true;
        }

        const target = returnTo || currentPageTarget();
        const query = new URLSearchParams({
            next: target,
            reason: reason || "login"
        });
        window.location.href = `login.html?${query.toString()}`;
        return false;
    }

    function updateNavbar() {
        const navList = document.querySelector("nav ul");
        if (!navList) {
            return;
        }

        navList.querySelectorAll("[data-auth-generated]").forEach(function (item) {
            item.remove();
        });

        const item = document.createElement("li");
        item.dataset.authGenerated = "true";
        const link = document.createElement("a");
        link.className =
            "rounded-full border-2 border-ink px-4 py-1.5 text-sm font-bold transition hover:bg-ink hover:text-paper";

        if (isLoggedIn()) {
            link.href = "#logout";
            link.textContent = "Keluar";
            link.addEventListener("click", function (event) {
                event.preventDefault();
                logoutUser();
                window.location.href = "nemu.html";
            });
        } else {
            link.href = `login.html?${new URLSearchParams({ next: currentPageTarget() }).toString()}`;
            link.textContent = "Masuk";
        }

        item.appendChild(link);
        navList.appendChild(item);
    }

    window.NemuAuth = {
        isLoggedIn,
        loginUser,
        logoutUser,
        requireLogin,
        updateNavbar
    };

    updateNavbar();

    if (document.body.dataset.requireLogin === "true") {
        requireLogin("report");
    }
})();