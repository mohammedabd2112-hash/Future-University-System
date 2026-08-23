document.documentElement.classList.add("js-enabled");

const pageRoot = document.body;

/* =========================================================
   PAGE TRANSITION
========================================================= */

if (pageRoot) {
    pageRoot.classList.add("page-transition");

    requestAnimationFrame(() => {
        pageRoot.classList.add("is-ready");
    });
}


/* =========================================================
   TOAST
========================================================= */

function showToast(message, type = "info") {
    let toast = document.querySelector(".toast");

    if (!toast) {
        toast = document.createElement("div");
        toast.className = "toast";
        toast.setAttribute("role", "status");
        toast.setAttribute("aria-live", "polite");

        document.body.appendChild(toast);
    }

    toast.className = `toast toast-${type} is-visible`;
    toast.textContent = message;

    window.setTimeout(() => {
        toast.classList.remove("is-visible");
    }, 2800);
}


/* =========================================================
   ADMIN DRAWER
========================================================= */

function closeDrawer() {
    const sidebar = document.querySelector(".admin-sidebar");
    const backdrop = document.querySelector(".drawer-backdrop");
    const toggle = document.querySelector(".sidebar-toggle");

    if (!sidebar || !backdrop) {
        return;
    }

    sidebar.classList.remove("is-open");
    backdrop.classList.remove("is-open");

    toggle?.setAttribute("aria-expanded", "false");
}


function setupAdminDrawer() {
    const sidebar = document.querySelector(".admin-sidebar");
    const topbar = document.querySelector(".admin-topbar");

    if (!sidebar || !topbar) {
        return;
    }

    document.body.classList.add("has-drawer");

    const toggle = document.createElement("button");

    toggle.type = "button";
    toggle.className = "sidebar-toggle button-secondary";
    toggle.setAttribute("aria-label", "فتح قائمة الإدارة");
    toggle.setAttribute("aria-expanded", "false");
    toggle.textContent = "القائمة";

    topbar.prepend(toggle);

    const backdrop = document.createElement("div");

    backdrop.className = "drawer-backdrop";
    backdrop.setAttribute("aria-hidden", "true");

    document.body.appendChild(backdrop);

    toggle.addEventListener("click", () => {
        const isOpen = sidebar.classList.toggle("is-open");

        backdrop.classList.toggle("is-open", isOpen);

        toggle.setAttribute(
            "aria-expanded",
            String(isOpen)
        );
    });

    backdrop.addEventListener("click", closeDrawer);

    document.addEventListener("keydown", event => {
        if (event.key === "Escape") {
            closeDrawer();
        }
    });
}


/* =========================================================
   PUBLIC NAVIGATION
========================================================= */

function setupPublicNavigation() {
    const nav = document.querySelector(".site-nav");
    const headerInner = document.querySelector(".site-header__inner");

    if (!nav || !headerInner) {
        return;
    }

    const toggle = document.createElement("button");

    toggle.type = "button";
    toggle.className = "site-nav-toggle button-secondary";
    toggle.setAttribute("aria-label", "فتح قائمة التنقل");
    toggle.setAttribute("aria-expanded", "false");
    toggle.textContent = "القائمة";

    headerInner.insertBefore(toggle, nav);

    const backdrop = document.createElement("div");
    backdrop.className = "public-nav-backdrop";
    backdrop.setAttribute("aria-hidden", "true");
    document.body.appendChild(backdrop);

    const closePublicNav = () => {
        nav.classList.remove("is-open");
        document.body.classList.remove("top-nav-open");
        backdrop.classList.remove("is-open");
        toggle.setAttribute("aria-expanded", "false");
    };

    backdrop.addEventListener("click", closePublicNav);

    toggle.addEventListener("click", () => {
        const isOpen = nav.classList.toggle("is-open");
        document.body.classList.toggle("top-nav-open", isOpen);
        backdrop.classList.toggle("is-open", isOpen);

        toggle.setAttribute(
            "aria-expanded",
            String(isOpen)
        );
    });

    nav.querySelectorAll("a").forEach(link => {
        link.addEventListener("click", () => {
            closePublicNav();

            toggle.setAttribute(
                "aria-expanded",
                "false"
            );
        });
    });

    document.addEventListener("click", event => {
        if (!headerInner.contains(event.target)) {
            closePublicNav();

            toggle.setAttribute(
                "aria-expanded",
                "false"
            );
        }
    });

    document.addEventListener("keydown", event => {
        if (event.key === "Escape") {
            closePublicNav();

            toggle.setAttribute(
                "aria-expanded",
                "false"
            );
        }
    });
}


/* =========================================================
   HEADER SCROLL STATE
========================================================= */

function setupScrollState() {
    const header = document.querySelector(".site-header");

    if (!header) {
        return;
    }

    const updateHeader = () => {
        header.classList.toggle(
            "is-scrolled",
            window.scrollY > 12
        );
    };

    updateHeader();

    window.addEventListener(
        "scroll",
        updateHeader,
        { passive: true }
    );
}


/* =========================================================
   SCROLL REVEAL
========================================================= */

function setupScrollReveal() {

    const revealTargets = document.querySelectorAll(
        ".home-hero, .page-section, .overview-band, .page-header, .student-profile, .faculty-grid, .admin-welcome, .admin-section, .admin-form-panel, .admin-table-panel, .login-card"
    );

    if (!revealTargets.length) {
        return;
    }

    revealTargets.forEach((element, index) => {

        element.classList.add("reveal-on-scroll");

        element.style.setProperty(
            "--reveal-delay",
            `${Math.min(index * 45, 180)}ms`
        );

        if (element.classList.contains("news-section")) {
            element.classList.add("reveal-slide");
        }

        if (element.classList.contains("overview-band")) {
            element.classList.add("reveal-scale");
        }
    });

    if (!("IntersectionObserver" in window)) {

        revealTargets.forEach(element => {
            element.classList.add("is-revealed");
        });

        return;
    }

    const observer = new IntersectionObserver(
        (entries, currentObserver) => {

            entries.forEach(entry => {

                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add("is-revealed");

                currentObserver.unobserve(
                    entry.target
                );
            });

        },
        {
            threshold: 0.12
        }
    );

    revealTargets.forEach(element => {
        observer.observe(element);
    });
}


/* =========================================================
   COUNTERS
========================================================= */

function setupCounters() {

    const counters =
        document.querySelectorAll("[data-count]");

    counters.forEach(counter => {

        const target =
            Number(counter.dataset.count);

        if (!Number.isFinite(target) || target < 1) {

            counter.textContent =
                String(target);

            return;
        }

        let started = false;

        const startCounter = () => {

            if (started) {
                return;
            }

            started = true;

            const startTime =
                performance.now();

            const duration = 1100;

            const update = now => {

                const progress =
                    Math.min(
                        (now - startTime) /
                        duration,
                        1
                    );

                const eased =
                    1 -
                    Math.pow(
                        1 - progress,
                        3
                    );

                counter.textContent =
                    String(
                        Math.round(
                            target * eased
                        )
                    );

                if (progress < 1) {
                    requestAnimationFrame(update);
                }
            };

            requestAnimationFrame(update);
        };

        if (!("IntersectionObserver" in window)) {

            startCounter();

            return;
        }

        const observer =
            new IntersectionObserver(
                entries => {

                    if (
                        entries.some(
                            entry =>
                                entry.isIntersecting
                        )
                    ) {

                        startCounter();

                        observer.disconnect();
                    }

                },
                {
                    threshold: 0.4
                }
            );

        observer.observe(counter);
    });
}


/* =========================================================
   HOMEPAGE PREMIUM MOTION
========================================================= */

function setupHomepageMotion() {

    if (
        !document.body.classList.contains(
            "home-page"
        )
    ) {
        return;
    }

    const reduceMotion =
        window.matchMedia(
            "(prefers-reduced-motion: reduce)"
        ).matches;

    const canHover =
        window.matchMedia(
            "(hover: hover)"
        ).matches;

    createAmbientBackground();

    setupHomepageReveal();

    if (!reduceMotion && canHover) {

        setupCursorGlow();

        setupHeroParallax();

        setupMagneticButtons();
    }
}


/* =========================================================
   AMBIENT BACKGROUND
========================================================= */

function createAmbientBackground() {

    if (
        document.querySelector(
            ".ambient-background"
        )
    ) {
        return;
    }

    const wrapper =
        document.createElement("div");

    wrapper.className =
        "ambient-background";

    wrapper.setAttribute(
        "aria-hidden",
        "true"
    );

    wrapper.innerHTML = `
        <div class="ambient-grid"></div>

        <div class="ambient-orb ambient-orb--one"></div>
        <div class="ambient-orb ambient-orb--two"></div>
        <div class="ambient-orb ambient-orb--three"></div>
    `;

    document.body.prepend(wrapper);
}


/* =========================================================
   CURSOR GLOW
========================================================= */

function setupCursorGlow() {

    const glow =
        document.createElement("div");

    glow.className =
        "cursor-glow";

    glow.setAttribute(
        "aria-hidden",
        "true"
    );

    document.body.appendChild(glow);

    let mouseX = window.innerWidth / 2;
    let mouseY = window.innerHeight / 2;

    let currentX = mouseX;
    let currentY = mouseY;

    document.addEventListener(
        "pointermove",
        event => {

            mouseX = event.clientX;
            mouseY = event.clientY;

            document.body.classList.add(
                "cursor-active"
            );
        },
        {
            passive: true
        }
    );

    document.addEventListener(
        "pointerleave",
        () => {

            document.body.classList.remove(
                "cursor-active"
            );
        }
    );

    function animateGlow() {

        currentX +=
            (mouseX - currentX) * 0.12;

        currentY +=
            (mouseY - currentY) * 0.12;

        glow.style.transform =
            `translate3d(
                ${currentX}px,
                ${currentY}px,
                0
            ) translate3d(-50%, -50%, 0)`;

        requestAnimationFrame(
            animateGlow
        );
    }

    animateGlow();
}


/* =========================================================
   HERO PARALLAX
========================================================= */

function setupHeroParallax() {

    const hero =
        document.querySelector(
            ".home-hero"
        );

    const visual =
        document.querySelector(
            ".hero-visual"
        );

    if (!hero || !visual) {
        return;
    }

    let targetX = 0;
    let targetY = 0;

    let currentX = 0;
    let currentY = 0;

    hero.addEventListener(
        "pointermove",
        event => {

            const bounds =
                hero.getBoundingClientRect();

            const x =
                (
                    event.clientX -
                    bounds.left
                ) /
                bounds.width -
                0.5;

            const y =
                (
                    event.clientY -
                    bounds.top
                ) /
                bounds.height -
                0.5;

            targetX = x;
            targetY = y;
        },
        {
            passive: true
        }
    );

    hero.addEventListener(
        "pointerleave",
        () => {

            targetX = 0;
            targetY = 0;
        }
    );

    function animateHero() {

        currentX +=
            (targetX - currentX) *
            0.055;

        currentY +=
            (targetY - currentY) *
            0.055;

        const heroX =
            currentX * -7;

        const heroY =
            currentY * -4;

        const visualX =
            currentX * 14;

        const visualY =
            currentY * 10;

        const rotateY =
            currentX * 5;

        const rotateX =
            currentY * -4;

        hero.style.setProperty(
            "--hero-x",
            `${heroX.toFixed(2)}px`
        );

        hero.style.setProperty(
            "--hero-y",
            `${heroY.toFixed(2)}px`
        );

        visual.style.setProperty(
            "--pointer-x",
            `${visualX.toFixed(2)}px`
        );

        visual.style.setProperty(
            "--pointer-y",
            `${visualY.toFixed(2)}px`
        );

        visual.style.setProperty(
            "--visual-rotate-x",
            `${rotateX.toFixed(2)}deg`
        );

        visual.style.setProperty(
            "--visual-rotate-y",
            `${rotateY.toFixed(2)}deg`
        );

        requestAnimationFrame(
            animateHero
        );
    }

    animateHero();
}


/* =========================================================
   MAGNETIC BUTTONS
========================================================= */

function setupMagneticButtons() {

    const buttons =
        document.querySelectorAll(
            ".home-page .hero-actions .button"
        );

    buttons.forEach(button => {

        button.addEventListener(
            "pointermove",
            event => {

                const rect =
                    button.getBoundingClientRect();

                const x =
                    event.clientX -
                    rect.left -
                    rect.width / 2;

                const y =
                    event.clientY -
                    rect.top -
                    rect.height / 2;

                button.style.transform =
                    `translate(
                        ${x * 0.08}px,
                        ${y * 0.08 - 4}px
                    )`;
            }
        );

        button.addEventListener(
            "pointerleave",
            () => {

                button.style.transform =
                    "";
            }
        );
    });
}


/* =========================================================
   HOMEPAGE REPEATED ITEMS
========================================================= */

function setupHomepageReveal() {

    const repeatedItems =
        document.querySelectorAll(
            ".quick-link, .news-item"
        );

    repeatedItems.forEach(
        (item, index) => {

            item.classList.add(
                "reveal-item"
            );

            item.style.setProperty(
                "--item-delay",
                `${Math.min(
                    index * 90,
                    450
                )}ms`
            );
        }
    );

    if (
        !("IntersectionObserver" in window)
    ) {

        repeatedItems.forEach(item => {
            item.classList.add(
                "is-revealed"
            );
        });

        return;
    }

    const observer =
        new IntersectionObserver(
            (entries, currentObserver) => {

                entries.forEach(entry => {

                    if (!entry.isIntersecting) {
                        return;
                    }

                    entry.target.classList.add(
                        "is-revealed"
                    );

                    currentObserver.unobserve(
                        entry.target
                    );
                });

            },
            {
                threshold: 0.12
            }
        );

    repeatedItems.forEach(item => {
        observer.observe(item);
    });
}


/* =========================================================
   DELETE CONFIRMATION
========================================================= */

function setupDeleteDialog() {

    if (
        !document.querySelector(
            "button[type='submit'].button-danger"
        )
    ) {
        return;
    }

    const backdrop =
        document.createElement("div");

    backdrop.className =
        "confirm-dialog-backdrop";

    backdrop.innerHTML = `
        <section
            class="confirm-dialog"
            role="dialog"
            aria-modal="true"
            aria-labelledby="confirm-title">

            <h2 id="confirm-title">
                تأكيد الحذف
            </h2>

            <p>
                هذا الإجراء لا يمكن التراجع عنه.
            </p>

            <div class="confirm-dialog__actions">

                <button
                    type="button"
                    class="button-secondary"
                    data-confirm-cancel>
                    إلغاء
                </button>

                <button
                    type="button"
                    class="button-danger"
                    data-confirm-submit>
                    حذف
                </button>

            </div>

        </section>
    `;

    document.body.appendChild(
        backdrop
    );

    let pendingForm = null;

    document.addEventListener(
        "click",
        event => {

            const deleteButton =
                event.target.closest(
                    "button[type='submit'].button-danger"
                );

            if (
                !deleteButton ||
                !deleteButton.form
            ) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            pendingForm =
                deleteButton.form;

            backdrop.classList.add(
                "is-open"
            );

            backdrop
                .querySelector(
                    "[data-confirm-submit]"
                )
                .focus();

        },
        true
    );

    backdrop
        .querySelector(
            "[data-confirm-cancel]"
        )
        .addEventListener(
            "click",
            () => {

                pendingForm = null;

                backdrop.classList.remove(
                    "is-open"
                );
            }
        );

    backdrop
        .querySelector(
            "[data-confirm-submit]"
        )
        .addEventListener(
            "click",
            () => {

                if (pendingForm) {
                    pendingForm.submit();
                }

                pendingForm = null;

                backdrop.classList.remove(
                    "is-open"
                );
            }
        );

    backdrop.addEventListener(
        "click",
        event => {

            if (
                event.target === backdrop
            ) {

                pendingForm = null;

                backdrop.classList.remove(
                    "is-open"
                );
            }
        }
    );

    document.addEventListener(
        "keydown",
        event => {

            if (event.key === "Escape") {

                pendingForm = null;

                backdrop.classList.remove(
                    "is-open"
                );
            }
        }
    );
}


/* =========================================================
   PAGE NAVIGATION TRANSITION
========================================================= */

function setupPageLinks() {

    document.addEventListener(
        "click",
        event => {

            const link =
                event.target.closest("a");

            if (!link) {
                return;
            }

            if (
                link.target === "_blank" ||
                link.hasAttribute("download") ||
                link
                    .getAttribute("href")
                    ?.startsWith("#")
            ) {
                return;
            }

            const destination =
                new URL(
                    link.href,
                    window.location.href
                );

            if (
                destination.origin !==
                    window.location.origin ||
                destination.href ===
                    window.location.href
            ) {
                return;
            }

            const publicNav =
                document.querySelector(
                    ".site-nav"
                );

            const publicNavToggle =
                document.querySelector(
                    ".site-nav-toggle"
                );

            publicNav?.classList.remove(
                "is-open"
            );

            publicNavToggle?.setAttribute(
                "aria-expanded",
                "false"
            );

            event.preventDefault();

            pageRoot?.classList.remove(
                "is-ready"
            );

            window.setTimeout(
                () => {
                    window.location.href =
                        destination.href;
                },
                260
            );
        }
    );
}


/* =========================================================
   LOGIN
========================================================= */

const loginForm =
    document.getElementById(
        "loginForm"
    );

if (loginForm) {

    loginForm.addEventListener(
        "submit",
        async event => {

            event.preventDefault();

            const email =
                document
                    .getElementById("email")
                    .value
                    .trim();

            const password =
                document
                    .getElementById("password")
                    .value;

            const message =
                document.getElementById(
                    "message"
                );

            const loginButton =
                document.getElementById(
                    "loginButton"
                );

            if (
                email === "" ||
                password === ""
            ) {

                message.textContent =
                    "يرجى إدخال البريد الإلكتروني وكلمة المرور";

                return;
            }

            loginButton.disabled = true;

            loginButton.setAttribute(
                "aria-busy",
                "true"
            );

            loginButton.textContent =
                "جاري تسجيل الدخول...";

            try {

                const response =
                    await fetch(
                        "../api/login.php",
                        {
                            method: "POST",

                            headers: {
                                "Content-Type":
                                    "application/x-www-form-urlencoded"
                            },

                            body:
                                new URLSearchParams({
                                    email,
                                    password
                                })
                        }
                    );

                const data =
                    await response.json();

                if (data.success) {

                    message.textContent =
                        "تم تسجيل الدخول بنجاح";

                    if (
                        data.user.role ===
                        "admin"
                    ) {

                        window.location.href =
                            "../admin/index.php";

                    } else if (
                        data.user.role ===
                        "student"
                    ) {

                        window.location.href =
                            "students.php";

                    } else {

                        message.textContent =
                            "نوع المستخدم غير معروف";

                        loginButton.disabled =
                            false;

                        loginButton.removeAttribute(
                            "aria-busy"
                        );

                        loginButton.textContent =
                            "تسجيل الدخول";
                    }

                } else {

                    message.textContent =
                        data.message;

                    loginButton.disabled =
                        false;

                    loginButton.removeAttribute(
                        "aria-busy"
                    );

                    loginButton.textContent =
                        "تسجيل الدخول";
                }

            } catch (error) {

                console.error(error);

                message.textContent =
                    "حدث خطأ أثناء الاتصال بالخادم";

                loginButton.disabled =
                    false;

                loginButton.removeAttribute(
                    "aria-busy"
                );

                loginButton.textContent =
                    "تسجيل الدخول";
            }
        }
    );
}


/* =========================================================
   INITIALIZE
========================================================= */

setupAdminDrawer();
setupPublicNavigation();
setupScrollState();
setupScrollReveal();
setupCounters();
setupHomepageMotion();
setupDeleteDialog();
setupPageLinks();


/* =========================================================
   CINEMATIC ENHANCEMENTS
========================================================= */

function setupScrollProgress() {
    if (document.querySelector(".page-progress")) {
        return;
    }

    const progress = document.createElement("div");
    progress.className = "page-progress";
    progress.setAttribute("aria-hidden", "true");
    document.body.appendChild(progress);

    let ticking = false;

    const update = () => {
        const scrollable = document.documentElement.scrollHeight - window.innerHeight;
        const ratio = scrollable > 0 ? window.scrollY / scrollable : 0;
        progress.style.width = `${Math.min(Math.max(ratio * 100, 0), 100)}%`;
        ticking = false;
    };

    window.addEventListener("scroll", () => {
        if (!ticking) {
            window.requestAnimationFrame(update);
            ticking = true;
        }
    }, { passive: true });

    window.addEventListener("resize", update, { passive: true });
    update();
}

function setupHomepageTiltCards() {
    if (!document.body.classList.contains("home-page")) {
        return;
    }

    const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const canHover = window.matchMedia("(hover: hover)").matches;

    if (reduceMotion || !canHover) {
        return;
    }

    document.querySelectorAll(".home-page .quick-link, .home-page .news-item").forEach(card => {
        card.addEventListener("pointermove", event => {
            const rect = card.getBoundingClientRect();
            const x = (event.clientX - rect.left) / rect.width - 0.5;
            const y = (event.clientY - rect.top) / rect.height - 0.5;
            card.style.transform = `perspective(900px) rotateX(${(-y * 1.8).toFixed(2)}deg) rotateY(${(x * 2.4).toFixed(2)}deg) translateY(-4px)`;
        }, { passive: true });

        card.addEventListener("pointerleave", () => {
            card.style.transform = "";
        }, { passive: true });
    });
}

function setupSectionSpotlight() {
    if (!document.body.classList.contains("home-page")) {
        return;
    }

    const sections = document.querySelectorAll(".home-page .page-section, .home-page .overview-band");
    if (!sections.length || !("IntersectionObserver" in window)) {
        return;
    }

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            entry.target.classList.toggle("is-in-view", entry.isIntersecting);
        });
    }, { threshold: 0.18 });

    sections.forEach(section => observer.observe(section));
}

setupScrollProgress();
setupHomepageTiltCards();
setupSectionSpotlight();


/* =========================================================
   GLOBAL ATMOSPHERIC EFFECTS
========================================================= */
(function setupGlobalAtmosphere() {
    const body = document.body;
    if (!body || body.classList.contains("home-page")) return;

    body.classList.add("has-atmospheric-effects");

    const canHover = window.matchMedia("(hover: hover)").matches;
    const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    if (!canHover || reduceMotion) return;

    let targetX = 0;
    let targetY = 0;
    let currentX = 0;
    let currentY = 0;

    window.addEventListener("pointermove", event => {
        targetX = (event.clientX / window.innerWidth - 0.5) * 2;
        targetY = (event.clientY / window.innerHeight - 0.5) * 2;
    }, { passive: true });

    function animateAtmosphere() {
        currentX += (targetX - currentX) * 0.035;
        currentY += (targetY - currentY) * 0.035;
        body.style.setProperty("--atmosphere-x", `${(currentX * 18).toFixed(2)}px`);
        body.style.setProperty("--atmosphere-y", `${(currentY * 18).toFixed(2)}px`);
        window.requestAnimationFrame(animateAtmosphere);
    }

    animateAtmosphere();
}());


/* =========================================================
   FLOATING BOOKS BACKGROUND
========================================================= */
(function setupFloatingBooks() {
    const body = document.body;
    if (!body || body.querySelector(".floating-books")) return;

    const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    if (reduceMotion) return;

    const layer = document.createElement("div");
    layer.className = "floating-books";
    layer.setAttribute("aria-hidden", "true");

    const colors = ["#102a43", "#1f4e79", "#e87932", "#5c8fa8", "#b8cbd6"];
    const positions = [
        [7, 18], [17, 72], [29, 34], [42, 84], [56, 15],
        [68, 66], [79, 28], [91, 78], [12, 92], [88, 48]
    ];

    positions.forEach(([left, top], index) => {
        const book = document.createElement("span");
        book.className = "floating-book";
        book.style.left = `${left}%`;
        book.style.top = `${top}%`;
        book.style.setProperty("--book-color", colors[index % colors.length]);
        book.style.setProperty("--book-width", `${24 + (index % 4) * 9}px`);
        book.style.setProperty("--book-height", `${8 + (index % 3) * 3}px`);
        book.style.setProperty("--book-rotate", `${-28 + (index * 13) % 56}deg`);
        book.style.setProperty("--book-duration", `${12 + (index % 5) * 2}s`);
        book.style.setProperty("--book-delay", `${-(index * 1.4)}s`);
        book.style.setProperty("--book-drift", `${18 + (index % 4) * 16}px`);
        layer.appendChild(book);
    });

    body.prepend(layer);
}());


/* =========================================================
   FU MARK POINTER GLOW
========================================================= */
(function setupFuMarkMotion() {
    const marks = document.querySelectorAll(".brand__mark, .login-brand, .faculty-member__mark, .admin-user__mark");
    const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const canHover = window.matchMedia("(hover: hover)").matches;

    if (!marks.length || reduceMotion || !canHover) return;

    marks.forEach(mark => {
        mark.addEventListener("pointermove", event => {
            const rect = mark.getBoundingClientRect();
            const x = (event.clientX - rect.left) / rect.width - 0.5;
            const y = (event.clientY - rect.top) / rect.height - 0.5;
            mark.style.setProperty("--fu-x", `${(x * 5).toFixed(2)}px`);
            mark.style.setProperty("--fu-y", `${(y * 5).toFixed(2)}px`);
        }, { passive: true });

        mark.addEventListener("pointerleave", () => {
            mark.style.setProperty("--fu-x", "0px");
            mark.style.setProperty("--fu-y", "0px");
        }, { passive: true });
    });
}());
