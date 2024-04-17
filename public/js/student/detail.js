window.onload = function() {

    // const options = document.querySelectorAll(".option");
    const selects = document.querySelectorAll('.docs-select');

    selects.forEach((select) => {
        select.addEventListener('change', function() {
            const filesWrapper = select.parentNode.parentNode;
            const linkButton = filesWrapper.querySelector('a');
            
            linkButton.href = select.value;
        });
        // console.log(selects);
    });
    // console.log(options);
}