window.onload = function () {
    // configuração da biblioteca datatables
    $("#table").DataTable({
        // posicionamento dos componentes em torno da tabela
        dom: '<"table_nav"f>tp',

        // ordenando as linhas pela primeira coluna, em ordem decrescente
        order: [[0, "desc"]],

        // permitindo um scroll horizontal quando a tabela não cabe na tela
        scrollX: true,

        // número de linhas em cada página da tabela
        pageLength: 10,

        // a tabela tem uma coluna de IDs dos requerimentos que não é visível na página
        columnDefs: [
            { visible: false, targets: 4 },
            { orderable: false, targets: 3 },
        ],
    });

    const text = (document.querySelector(
        "#table_filter"
    ).firstChild.firstChild.textContent = "Pesquisar");

    const table = $("#table").DataTable();
    $("#table tbody").on("click", ".button", function () {
        table.row($(this).parents("tr")).remove().draw();
    });

    const popupButton = document.querySelector("nav button");
    popupButton.onclick = (event) => {
        const overlayContainer = document.querySelector(".overlay-container");
        overlayContainer.classList.add("overlay-show");
    };

    const closeButton = document.querySelector(".close-button");
    closeButton.onclick = (event) => {
        const overlayContainer = document.querySelector(".overlay-container");
        overlayContainer.classList.remove("overlay-show");
    };

    const radioButtons = document.getElementsByName("type");
    radioButtons.forEach((button) => {
        button.onchange = (event) => {
            const departmentRadio = document.querySelector(".department-radio");
            const departmentTitle = document.querySelector(".department-title");
            if (
                event.currentTarget.id === "coordination" ||
                event.currentTarget.id === "dept-secretary"
            ) {
                departmentRadio.classList.add("radio-show");
                departmentTitle.classList.add("radio-show");
            } else {
                departmentRadio.classList.remove("radio-show");
                departmentTitle.classList.remove("radio-show");
            }
        };
    });
};
