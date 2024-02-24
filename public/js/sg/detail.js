window.onload = function() {
    const form = document.querySelector("#form");
    form.addEventListener("submit", (event) => {
        event.preventDefault();
        if (event.submitter) {
            const clickedButton = event.submitter;
            const btnType = document.querySelector("#btnType");
            if (clickedButton.textContent === 'Encaminhar para o departamento') {
                btnType.value = "validate";
            } else if (clickedButton.textContent === 'Salvar mudanças') {
                btnType.value = "save";
            } 
        }
        form.submit();
    });

    const closeButton = document.querySelector(".close-button");
    
    closeButton.onclick = (event) => {
        const overlayContainer =
            document.querySelector(".overlay-container");
        overlayContainer.style.display = "none";
    };
}
