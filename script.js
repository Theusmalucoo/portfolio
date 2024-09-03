// Função para alternar o menu responsivo
function toggleMenu() {
    const navbar = document.querySelector('.navbar');
    navbar.classList.toggle('active');
}

// Função para verificar e alterar a ordem dos elementos com base no tamanho da tela
function reorderElements() {
    const menuIcon = document.querySelector('.menu-icon');
    const homeSection = document.querySelector('.home');

    // Verifica se a largura da tela é menor ou igual a 768px (dispositivos móveis)
    if (window.innerWidth <= 768) {
        // Adiciona a classe para visualização móvel
        homeSection.classList.add('mobile-view');
        // Remove o atributo hidden para exibir o ícone do menu
        menuIcon.removeAttribute('hidden');
    } else {
        // Remove a classe para visualização padrão
        homeSection.classList.remove('mobile-view');
        // Adiciona o atributo hidden para ocultar o ícone do menu
        menuIcon.setAttribute('hidden', true);
    }


}

// Chama a função ao carregar a página e redimensionar a tela
window.addEventListener('DOMContentLoaded', reorderElements);
window.addEventListener('resize', reorderElements);

function setActiveLink() {
    const linkId = 'home-link'; 
    const linkElement = document.getElementById(linkId);

    if (linkElement) {
        linkElement.classList.add('active');
    }
}


document.addEventListener('DOMContentLoaded', setActiveLink);