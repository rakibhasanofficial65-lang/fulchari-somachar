/* =========================================================
   FULCHARI SOMACHAR
   GLOBAL JAVASCRIPT
========================================================= */

"use strict";


/* =========================================================
   DOM READY
========================================================= */

document.addEventListener("DOMContentLoaded", function () {

    initConfirmActions();

    initImagePreview();

    initAutoHideAlerts();

    initSearchForm();

    initSmoothScroll();

    initMobileMenu();

});


/* =========================================================
   CONFIRM ACTIONS
   Delete / Logout / Important Actions
========================================================= */

function initConfirmActions() {

    const confirmElements =
        document.querySelectorAll(
            "[data-confirm]"
        );


    confirmElements.forEach(function (element) {

        element.addEventListener(
            "click",
            function (event) {

                const message =
                    element.getAttribute(
                        "data-confirm"
                    );


                if (
                    message &&
                    !window.confirm(message)
                ) {

                    event.preventDefault();

                }

            }
        );

    });

}


/* =========================================================
   IMAGE PREVIEW
   Admin Add/Edit News
========================================================= */

function initImagePreview() {

    const imageInputs =
        document.querySelectorAll(
            'input[type="file"][data-preview]'
        );


    imageInputs.forEach(function (input) {

        input.addEventListener(
            "change",
            function () {

                const previewId =
                    input.getAttribute(
                        "data-preview"
                    );


                if (!previewId) {
                    return;
                }


                const preview =
                    document.getElementById(
                        previewId
                    );


                if (!preview) {
                    return;
                }


                const file =
                    input.files &&
                    input.files[0];


                if (!file) {

                    preview.src = "";

                    preview.classList.add(
                        "hidden"
                    );

                    return;
                }


                if (!file.type.startsWith("image/")) {

                    preview.src = "";

                    preview.classList.add(
                        "hidden"
                    );

                    return;
                }


                const reader =
                    new FileReader();


                reader.onload =
                    function (event) {

                        preview.src =
                            event.target.result;

                        preview.classList.remove(
                            "hidden"
                        );

                    };


                reader.readAsDataURL(file);

            }
        );

    });

}


/* =========================================================
   AUTO HIDE ALERT
========================================================= */

function initAutoHideAlerts() {

    const alerts =
        document.querySelectorAll(
            ".alert[data-auto-hide]"
        );


    alerts.forEach(function (alert) {

        window.setTimeout(
            function () {

                alert.style.transition =
                    "opacity .4s ease";

                alert.style.opacity = "0";


                window.setTimeout(
                    function () {

                        alert.remove();

                    },
                    450
                );

            },
            4000
        );

    });

}


/* =========================================================
   SEARCH FORM
========================================================= */

function initSearchForm() {

    const forms =
        document.querySelectorAll(
            ".search-form"
        );


    forms.forEach(function (form) {

        form.addEventListener(
            "submit",
            function (event) {

                const input =
                    form.querySelector(
                        'input[name="q"]'
                    );


                if (!input) {
                    return;
                }


                const value =
                    input.value.trim();


                if (value === "") {

                    event.preventDefault();

                    input.focus();

                    return;

                }


                input.value = value;

            }
        );

    });

}


/* =========================================================
   SMOOTH SCROLL
========================================================= */

function initSmoothScroll() {

    const links =
        document.querySelectorAll(
            'a[href^="#"]'
        );


    links.forEach(function (link) {

        link.addEventListener(
            "click",
            function (event) {

                const targetId =
                    link.getAttribute(
                        "href"
                    );


                if (
                    !targetId ||
                    targetId === "#"
                ) {

                    return;

                }


                const target =
                    document.querySelector(
                        targetId
                    );


                if (!target) {
                    return;
                }


                event.preventDefault();


                target.scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });

            }
        );

    });

}


/* =========================================================
   MOBILE MENU
   Compatible with future toggle button
========================================================= */

function initMobileMenu() {

    const toggle =
        document.querySelector(
            "[data-menu-toggle]"
        );


    const menu =
        document.querySelector(
            "[data-mobile-menu]"
        );


    if (!toggle || !menu) {
        return;
    }


    toggle.addEventListener(
        "click",
        function () {

            const isOpen =
                menu.classList.toggle(
                    "is-open"
                );


            toggle.setAttribute(
                "aria-expanded",
                isOpen
                    ? "true"
                    : "false"
            );

        }
    );

}


/* =========================================================
   GLOBAL DELETE CONFIRM
========================================================= */

window.confirmDelete =
    function (message) {

        return window.confirm(
            message ||
            "আপনি কি নিশ্চিতভাবে এটি মুছে ফেলতে চান?"
        );

    };


/* =========================================================
   EPAPER PRINT
========================================================= */

window.printEpaper =
    function () {

        window.print();

    };
}
