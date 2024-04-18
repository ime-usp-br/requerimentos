window.onload = function() {
    const form = document.querySelector("#form");
    form.addEventListener("submit", (event) => {
        event.preventDefault();
        if (event.submitter) {
            const clickedButton = event.submitter;
            const btnType = document.querySelector("#btnType");
            if (clickedButton.id === "send-btn") {
                btnType.value = "send";
            } else if (clickedButton.id === "save-btn") {
                btnType.value = "save";
            }
        }
        form.submit();
    });

    const closeButton = document.querySelector(".close-button");

    closeButton.onclick = (event) => {
        const overlayContainer = document.querySelector(".overlay-container");
        overlayContainer.style.display = "none";
    };

    // const options = document.querySelectorAll(".option");
    const docSelects = document.querySelectorAll(".docs-select");

    docSelects.forEach((docSelect) => {
        docSelect.addEventListener("change", function () {
            const filesWrapper = docSelect.parentNode.parentNode;
            const linkButton = filesWrapper.querySelector("a");

            linkButton.href = docSelect.value;
        });
        // console.log(selects);
    });
    // console.log(options);

    const modeSelect = document.querySelector('.mode-select');

    modeSelect.addEventListener('change', function() {
        const inputs = document.querySelectorAll("input");
        const selects = document.querySelectorAll("select");

        if (modeSelect.value === 'edit') {
            inputs.forEach((input) => {
                input.readOnly = false;
            });

            selects.forEach((select) => {
                if (
                    !select.classList.contains("docs-select") &&
                    !select.classList.contains("mode-select")
                ) {
                    select.disabled = false;
                }
            });
        } else {
            inputs.forEach((input) => {
                input.readOnly = true;
            });

            selects.forEach((select) => {
                if (
                    !select.classList.contains("docs-select") &&
                    !select.classList.contains("mode-select")
                ) {
                    select.disabled = true;
                }
            });
        }
    })


}
