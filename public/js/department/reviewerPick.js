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
            { visible: false, targets: 3 },
            { orderable: false, targets: 2 },
        ],
    });

    const text = (document.querySelector(
        "#table_filter"
    ).firstChild.firstChild.textContent = "Pesquisar");

    // funcionalidade dos botões de remoção de papel
    const table = $("#table").DataTable();
    $("#table tbody").on("click", ".button", function () {
        const parentForm = $(this).parent();
        parentForm.submit();

        table.row($(this).parents("tr")).remove().draw();
    });

    // const forms = document.querySelectorAll(".button-form");

    // forms.forEach(form => {
    //     form.addEventListener('submit', function(event) {
    //         event.preventDefault();
    //     });
    // });

    // form.addEventListener("submit", (event) => {

    // if (event.submitter) {
    //     const clickedButton = event.submitter;
    //     const btnType = document.querySelector("#btnType");
    //     if (
    //         clickedButton.textContent ===
    //         "Encaminhar para o departamento"
    //     ) {
    //         btnType.value = "validate";
    //     } else if (clickedButton.textContent === "Salvar mudanças") {
    //         btnType.value = "save";
    //     }
    // }
    // form.submit();
    // });

    // faz o overlay aparecer quando o botão de adicionar é clickado
    const popupButton = document.querySelector("nav button");
    popupButton.onclick = (event) => {
        const overlayContainer = document.querySelector(".overlay-container");
        overlayContainer.classList.add("overlay-show");
    };

    // fecha o overlay
    const closeButton = document.querySelector(".close-button");
    closeButton.onclick = (event) => {
        const overlayContainer = document.querySelector(".overlay-container");
        overlayContainer.classList.remove("overlay-show");
    };

    // faz a opção por departamento aparecer
    const radioButtons = document.getElementsByName("role");
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

    // const table = $("#table").DataTable();
    // $("#table tbody").on("click", "tr", function () {
    //     window.location.href = "detalhe/" + table.row(this).data()[5];
    // });
};
