window.onload = function() {

    // adiciona os event handlers que vão mudar o link dos botões
    // para o arquivo correspondente a opção selecionada no select
    const docSelects = document.querySelectorAll(".docs-select");

    docSelects.forEach((docSelect) => {
        docSelect.addEventListener("change", function () {
            const filesWrapper = docSelect.parentNode.parentNode;
            const linkButton = filesWrapper.querySelector("a");

            linkButton.href = `/documento/${docSelect.value}`;
        });
    });
    ////////////////////////////////////////////////////////////////////////////
}