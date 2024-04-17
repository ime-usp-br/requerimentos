window.onload = function () {
    
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

    // const form = document.querySelector("#form");
    form.addEventListener("submit", (event) => {
        event.preventDefault();

        form.submit();
    });
};
