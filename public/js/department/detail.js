window.onload = function() {
    // o form está sendo submetido por javascript para diferenciar entre
    // os dois botões que podem ser clickados para submetê-lo, e para
    // remover o atributo "disabled" dos selects 
    const form = document.querySelector("#form");
    form.addEventListener("submit", (event) => {
        event.preventDefault();

        document.querySelectorAll("select").forEach((select) => {
            select.disabled = false;
        });

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

            linkButton.href = `/documento/${docSelect.value}`;
        });
    });
}
