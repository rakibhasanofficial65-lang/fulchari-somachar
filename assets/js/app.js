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

    initVercelBlobUpload();

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
   VERCEL BLOB IMAGE UPLOAD
========================================================= */

function initVercelBlobUpload() {

    const imageInputs =
        document.querySelectorAll(
            'input[type="file"][data-blob-upload]'
        );


    if (!imageInputs.length) {
        return;
    }


    imageInputs.forEach(function (input) {

        const form =
            input.closest("form");


        if (!form) {
            return;
        }


        let blobUrlInput =
            form.querySelector(
                'input[name="image_url"]'
            );


        if (!blobUrlInput) {

            blobUrlInput =
                document.createElement("input");

            blobUrlInput.type = "hidden";
            blobUrlInput.name = "image_url";

            form.appendChild(
                blobUrlInput
            );

        }


        let statusElement =
            form.querySelector(
                "[data-upload-status]"
            );


        if (!statusElement) {

            statusElement =
                document.createElement("div");

            statusElement.setAttribute(
                "data-upload-status",
                "true"
            );

            statusElement.className =
                "blob-upload-status";

            input.parentNode.appendChild(
                statusElement
            );

        }


        let progressElement =
            form.querySelector(
                "[data-upload-progress]"
            );


        if (!progressElement) {

            progressElement =
                document.createElement("progress");

            progressElement.setAttribute(
                "data-upload-progress",
                "true"
            );

            progressElement.max = 100;
            progressElement.value = 0;

            progressElement.className =
                "blob-upload-progress";

            progressElement.style.display =
                "none";

            input.parentNode.appendChild(
                progressElement
            );

        }


        let submitButton =
            form.querySelector(
                'button[type="submit"], input[type="submit"]'
            );


        let uploadPromise = null;


        input.addEventListener(
            "change",
            function () {

                const file =
                    input.files &&
                    input.files[0];


                blobUrlInput.value = "";


                if (!file) {

                    statusElement.textContent = "";

                    progressElement.style.display =
                        "none";

                    progressElement.value = 0;

                    return;

                }


                if (
                    file.type !== "image/jpeg" &&
                    file.type !== "image/png" &&
                    file.type !== "image/webp"
                ) {

                    statusElement.textContent =
                        "শুধু JPG, PNG অথবা WebP ছবি ব্যবহার করুন।";

                    statusElement.className =
                        "blob-upload-status upload-error";

                    input.value = "";

                    return;

                }


                const maxSize =
                    40 * 1024 * 1024;


                if (file.size > maxSize) {

                    statusElement.textContent =
                        "ছবির সর্বোচ্চ আকার 40MB হতে পারে।";

                    statusElement.className =
                        "blob-upload-status upload-error";

                    input.value = "";

                    return;

                }


                statusElement.textContent =
                    "ছবি প্রস্তুত হচ্ছে...";

                statusElement.className =
                    "blob-upload-status upload-processing";


                progressElement.style.display =
                    "block";

                progressElement.value = 0;


                uploadPromise =
                    uploadToVercelBlob(
                        file,
                        statusElement,
                        progressElement,
                        function (url) {

                            blobUrlInput.value =
                                url;

                        }
                    );

            }
        );


        form.addEventListener(
            "submit",
            async function (event) {

                const file =
                    input.files &&
                    input.files[0];


                if (!file) {
                    return;
                }


                if (
                    blobUrlInput.value
                ) {

                    return;

                }


                event.preventDefault();


                if (!uploadPromise) {

                    statusElement.textContent =
                        "ছবি upload শুরু করা যাচ্ছে না।";

                    statusElement.className =
                        "blob-upload-status upload-error";

                    return;

                }


                if (submitButton) {

                    submitButton.disabled =
                        true;

                    submitButton.dataset.originalText =
                        submitButton.tagName === "INPUT"
                            ? submitButton.value
                            : submitButton.textContent;


                    if (
                        submitButton.tagName === "INPUT"
                    ) {

                        submitButton.value =
                            "ছবি Upload হচ্ছে...";

                    } else {

                        submitButton.textContent =
                            "ছবি Upload হচ্ছে...";

                    }

                }


                try {

                    await uploadPromise;

                    if (!blobUrlInput.value) {

                        throw new Error(
                            "Blob URL পাওয়া যায়নি।"
                        );

                    }


                    statusElement.textContent =
                        "ছবি সফলভাবে Upload হয়েছে।";

                    statusElement.className =
                        "blob-upload-status upload-success";


                    form.submit();

                } catch (error) {

                    console.error(
                        "Vercel Blob upload error:",
                        error
                    );


                    statusElement.textContent =
                        "ছবি Upload করা যায়নি। আবার চেষ্টা করুন।";

                    statusElement.className =
                        "blob-upload-status upload-error";


                    if (submitButton) {

                        submitButton.disabled =
                            false;

                        restoreSubmitButton(
                            submitButton
                        );

                    }

                }

            }
        );

    });

}


/* =========================================================
   UPLOAD TO VERCEL BLOB
========================================================= */

async function uploadToVercelBlob(
    file,
    statusElement,
    progressElement,
    onSuccess
) {

    try {

        /*
         * Load the Vercel Blob client SDK
         * directly in the browser.
         */

        const blobModule =
            await import(
                "https://esm.sh/@vercel/blob/client@2.6.1"
            );


        const upload =
            blobModule.upload;


        if (
            typeof upload !== "function"
        ) {

            throw new Error(
                "Vercel Blob client library পাওয়া যায়নি।"
            );

        }


        statusElement.textContent =
            "ছবি Upload হচ্ছে...";


        const blob =
            await upload(
                file.name,
                file,
                {
                    access: "public",

                    handleUploadUrl:
                        "/api/upload",

                    multipart: true,

                    onUploadProgress:
                        function (event) {

                            const percentage =
                                Number(
                                    event.percentage || 0
                                );


                            progressElement.value =
                                percentage;


                            statusElement.textContent =
                                "ছবি Upload হচ্ছে... " +
                                Math.round(
                                    percentage
                                ) +
                                "%";

                        }
                }
            );


        if (
            !blob ||
            !blob.url
        ) {

            throw new Error(
                "Vercel Blob থেকে image URL পাওয়া যায়নি।"
            );

        }


        onSuccess(
            blob.url
        );


        progressElement.value =
            100;


        statusElement.textContent =
            "ছবি সফলভাবে Upload হয়েছে।";


        statusElement.className =
            "blob-upload-status upload-success";


        return blob.url;

    } catch (error) {

        console.error(
            "Vercel Blob upload failed:",
            error
        );


        statusElement.textContent =
            "ছবি Upload ব্যর্থ হয়েছে।";


        statusElement.className =
            "blob-upload-status upload-error";


        progressElement.style.display =
            "none";


        throw error;

    }

}


/* =========================================================
   RESTORE SUBMIT BUTTON
========================================================= */

function restoreSubmitButton(
    button
) {

    if (
        !button ||
        !button.dataset.originalText
    ) {

        return;

    }


    if (
        button.tagName === "INPUT"
    ) {

        button.value =
            button.dataset.originalText;

    } else {

        button.textContent =
            button.dataset.originalText;

    }


    delete button.dataset.originalText;

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


                input.value =
                    value;

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
