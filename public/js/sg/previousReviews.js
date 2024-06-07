window.onload = function () {
    // configuração da biblioteca datatables
    $("#table").DataTable({
        // posicionamento dos componentes em torno da tabela
        dom: '<"table_nav"f>tp',

        // ordenando as linhas pela primeira coluna, em ordem decrescente
        order: [[0, "asc"], [1, "asc"], [2, "asc"]],

        // permitindo um scroll horizontal quando a tabela não cabe na tela
        scrollX: true,

        // número de linhas em cada página da tabela
        pageLength: 10
    });

    const text = (document.querySelector(
        "#table_filter"
    ).firstChild.firstChild.textContent = "Pesquisar");
    
};
