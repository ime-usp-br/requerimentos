const closeButton = document.querySelector(".close-button");
closeButton.onclick = (event) => {
    const overlayContainer = document.querySelector(".overlay-container");
    overlayContainer.style.display = "none";
};

const openButton = document.querySelector('.open-button');
openButton.onclick = (event) => {
    const overlayContainer = document.querySelector('.overlay-container');
    overlayContainer.style.display = "block";
}
