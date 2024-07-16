window.onload = function() {
    // faz o overlay desaparecer quando o usuário aperta o botão de fechar
    const closeButton = document.querySelector(".close-button");
    
    closeButton.onclick = (event) => {
        const overlayContainer =
            document.querySelector(".overlay-container");
        overlayContainer.style.display = "none";
    };
}
