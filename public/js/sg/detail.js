window.onload = function() {
    // o form está sendo submetido por javascript para diferenciar entre
    // os dois botões que podem ser clickados para submetê-lo
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
    ////////////////////////////////////////////////////////////////////////////

    // faz o overlay desaparecer quando o usuário aperta o botão de fechar
    const closeButton = document.querySelector(".close-button");
    closeButton.onclick = (event) => {
        const overlayContainer = document.querySelector(".overlay-container");
        overlayContainer.style.display = "none";
    };
    ////////////////////////////////////////////////////////////////////////////

    // adiciona os event handler que vai mudar o link dos botões
    // para o arquivo correspondente
    const docSelects = document.querySelectorAll(".docs-select");

    docSelects.forEach((docSelect) => {
        docSelect.addEventListener("change", function () {
            const filesWrapper = docSelect.parentNode.parentNode;
            const linkButton = filesWrapper.querySelector("a");

            linkButton.href = `/document/${docSelect.value}`;
        });
    });
    ////////////////////////////////////////////////////////////////////////////


    // fazendo os elementos do form não serem editaveis quando a página carrega
    const inputs = document.querySelectorAll("input");
    const selects = document.querySelectorAll("select");
    const textareas = document.querySelectorAll("textarea");

    inputs.forEach((input) => {
        input.readOnly = true;
    });

    textareas.forEach((textarea) => {

        if (!textarea.parentNode.classList.contains("result-text")) {
            textarea.readOnly = true;
        }
    });

    selects.forEach((select) => {
        if (
            !select.classList.contains("docs-select") &&
            !select.classList.contains("mode-select")
        ) {
            select.disabled = true;
        }
    });
    ////////////////////////////////////////////////////////////////////////////

    
    // adiciona o event handler que vai fazer os elementos do form
    // alternarem entre editáveis ou não editáveis
    const modeSelect = document.querySelector(".mode-select");

    modeSelect.addEventListener("change", function () {
        const inputs = document.querySelectorAll("input");
        const selects = document.querySelectorAll("select");
        const textareas = document.querySelectorAll("textarea");

        if (modeSelect.value === "edit") {
            inputs.forEach((input) => {
                input.readOnly = false;
            });

            textareas.forEach((textarea) => {
                if (
                    !textarea.parentNode.classList.contains(
                        "result-text"
                    )
                ) {
                    textarea.readOnly = false;
                }
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

            textareas.forEach((textarea) => {
                if (
                    !textarea.parentNode.classList.contains(
                        "result-text"
                    )
                ) {
                    textarea.readOnly = true;
                }
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
    });
}
