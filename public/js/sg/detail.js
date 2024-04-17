window.onload = function() {
    const form = document.querySelector("#form");
    form.addEventListener("submit", (event) => {
        event.preventDefault();
        if (event.submitter) {
            const clickedButton = event.submitter;
            const btnType = document.querySelector("#btnType");
            if (clickedButton.id === "send-btn") {
                btnType.value = "send";
            } else if (clickedButton.id === "save-btn") {
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
