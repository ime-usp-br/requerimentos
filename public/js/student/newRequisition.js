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

    filePickerButtons = document.querySelectorAll(".document input");

    filePickerButtons.forEach((button) => {
        // console.log(button)

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

        const maxFileSize = 5 * 1024 * 1024;
        const fileInputs = document.querySelectorAll('.file-input');

        const studiedHist = fileInputs[0].files[0];
        const currentHist = fileInputs[1].files[0];
        const studiedSyl = fileInputs[2].files[0];
        const reqSyl = fileInputs[3].files[0];

        if (!studiedHist || !currentHist || !studiedSyl || !reqSyl) {
            alert('Pelo menos um dos arquivos necessários não foi adicionado');
            return;
        }

        if (studiedHist.size > maxFileSize*2) {
            alert('O histórico das disciplinas concluídas excede o limite de 10MB de tamanho');
            return;
        }
        if (currentHist.size > maxFileSize) {
            alert('O histórico das disciplinas atuais excede o limite de 5MB de tamanho');
            return;
        }
        if (studiedSyl.size > maxFileSize) {
            alert('A ementa das disciplinas concluídas excede o limite de 5MB de tamanho');
            return;
        }
        if (reqSyl.size > maxFileSize) {
            alert('A ementa das disciplinas requeridas excede o limite de 5MB de tamanho');
            return;
        }

        form.submit();
    });

    const closeButton = document.querySelector(".close-button");
    closeButton.onclick = (event) => {
        const overlayContainer = document.querySelector(".overlay-container");
        overlayContainer.style.display = "none";
    };
};
