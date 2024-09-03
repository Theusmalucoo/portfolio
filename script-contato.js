// Função para alternar o menu responsivo
function toggleMenu() {
    const navbar = document.querySelector('.navbar');
    navbar.classList.toggle('active');
}

function setActiveLink() {
    const linkId = 'contato-link'; 
    const linkElement = document.getElementById(linkId);

    if (linkElement) {
        linkElement.classList.add('active');
    }
}


document.addEventListener('DOMContentLoaded', setActiveLink);

