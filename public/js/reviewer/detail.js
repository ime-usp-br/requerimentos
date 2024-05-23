window.onload = function() {
    const closeButton = document.querySelector(".close-button");
    
    closeButton.onclick = (event) => {
        const overlayContainer =
            document.querySelector(".overlay-container");
        overlayContainer.style.display = "none";
    };
}
