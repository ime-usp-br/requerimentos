window.onload = function () {
    const addTakenButton = document.querySelector(".taken .add-disc");
    let takenDiscCount = 1;

    addTakenButton.onclick = (event) => {
        if (takenDiscCount === 0) {
            message = document.querySelector(".taken .empty-list-message");
            message.remove();
        }

        const takenDiscs = document.querySelector(".taken .disc-list");
        takenDiscCount++;
        takenDiscs.insertAdjacentHTML(
            "beforeend",
            `<div class="disc"><label>Nome: <input type="text" name="disc${takenDiscCount}-name" id="disc-name"></label><label class="disc-institution">Instituição em que foi cursada:<input type="text" name="disc${takenDiscCount}-institution" id="disc-institution" required></label><div class="disc-middle-row"><label class="disc-code">Sigla<input type="text" name="disc${takenDiscCount}-code" id="disc-code"></label><label>Ano: <input type="text" name="disc${takenDiscCount}-year" id="disc-year"></label><label>Nota: <input type="text" name="disc${takenDiscCount}-grade" id="disc-grade"></label></div><div class="disc-last-row"><label>Semestre:<select name="disc${takenDiscCount}-semester" id="disc-semester"><option value="">Selecione o semestre</option><option value="Primeiro">Primeiro</option><option value="Segundo">Segundo</option></select></label></div></div>`
        );
    };

    removeTakenButton = document.querySelector(".taken .remove-disc");

    removeTakenButton.onclick = (event) => {
        if (takenDiscCount === 1) {
            return;
        }

        const takenDiscs = document.querySelector(".taken .disc-list");
        const discToRemove = takenDiscs.lastChild;
        discToRemove.remove();

        takenDiscCount--;
        // if (takenDiscCount === 0) {
        //     const takenDiscs = document.querySelector(".taken .disc-list");
        //     takenDiscs.insertAdjacentHTML(
        //         "beforeend",
        //         `<p class="empty-list-message">Adicione aqui as disciplinas cursadas a serem aproveitadas.</p>`
        //     );
        // }
    };

    // const requestedAddButton = document.querySelector(".requested .add-disc");
    // let requestedDiscsCount = 0;

    // requestedAddButton.onclick = (event) => {
    //     if (requestedDiscsCount === 0) {
    //         message = document.querySelector(".requested .empty-list-message");
    //         message.remove();
    //         emptyRequestedDiscList = false;
    //     }

    //     const requestedDiscs = document.querySelector(".requested .disc-list");
    //     requestedDiscsCount++;

    //     requestedDiscs.insertAdjacentHTML(
    //         "beforeend",
    //         `<div class="disc"><label>Nome: <input type="text" id="disc-name" name="requested-disc-name"></label><label>Tipo:<select name="requested-disc-type" id="disc-type"><option value="">Selecione o tipo</option><option value="Extracurricular">Extracurricular</option><option value="Obrigatória">Obrigatória</option><option value="Optativa Eletiva">Optativa Eletiva</option><option value="Optativa Livre">Optativa Livre</option></select></label><div class="disc-last-row"><label class="disc-code">Sigla<input type="text" name="requested-disc-code" id="disc-code"></label><!-- <a href="#" class="button record-button">Histórico</a> --></div></div>`
    //     );
    // };

    // const requestedRemoveButton = document.querySelector(
    //     ".requested .remove-disc"
    // );

    // requestedRemoveButton.onclick = (event) => {
    //     if (requestedDiscsCount === 0) {
    //         return;
    //     }

    //     const requestedDiscs = document.querySelector(".requested .disc-list");
    //     const discToRemove = requestedDiscs.lastChild;
    //     discToRemove.remove();

    //     requestedDiscsCount--;
    //     if (requestedDiscsCount === 0) {
    //         const takenDiscs = document.querySelector(".requested .disc-list");
    //         takenDiscs.insertAdjacentHTML(
    //             "beforeend",
    //             `<p class="empty-list-message">Adicione aqui as disciplinas que serão requeridas.</p>`
    //         );
    //     }
    // };

    filePickerButtons = document.querySelectorAll(".document input");

    filePickerButtons.forEach((button) => {

        button.onchange = (event) => {
            const selectedFile = document.querySelector(`.${button.id}`);
            let filename = button.value.slice(12);

            if (filename.length > 28) {
                filename = `${filename.slice(0, 20)}...${filename.slice(
                    filename.length - 10,
                    filename.length
                )}`;
            }

            selectedFile.textContent = filename;
            selectedFile.insertAdjacentHTML(
                "afterbegin",
                "<span class='attached-file'>Arquivo anexado:</span><br/>"
            );
        };
    });


    const form = document.querySelector("#form");
    form.addEventListener("submit", (event) => {
        event.preventDefault();
        const takenDiscInput = document.querySelector("#taken-disc-count");
        takenDiscInput.value = takenDiscCount;
        form.submit();
    });

    const closeButton = document.querySelector(".close-button");
    closeButton.onclick = (event) => {
        const overlayContainer = document.querySelector(".overlay-container");
        overlayContainer.style.display = "none";
    };
};
