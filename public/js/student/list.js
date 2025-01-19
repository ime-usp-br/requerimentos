window.onload = function () {
    // função usada para ordenar as linhas da tabela pela data
    $.fn.dataTable.moment("D/M/YYYY");

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
    });

    const text = (document.querySelector(
        "#table_filter"
    ).firstChild.firstChild.textContent = "Pesquisar");

    // fazendo cada linha da tabela ser um link para a página de análise do requerimento
    const table = $("#table").DataTable();
    $("#table tbody").on("click", "tr", function () {
        window.location.href = "detalhe/" + table.row(this).data()[0];
    });
};
