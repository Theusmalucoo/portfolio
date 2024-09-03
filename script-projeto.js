// Função para alternar o menu responsivo
function toggleMenu() {
    const navbar = document.querySelector('.navbar');
    navbar.classList.toggle('active');
}

// Função para verificar e alterar a ordem dos elementos com base no tamanho da tela
function reorderElements() {
    const menuIcon = document.querySelector('.menu-icon');

    // Verifica se a largura da tela é menor ou igual a 768px (dispositivos móveis)
    if (window.innerWidth <= 768) {
        // Remove o atributo hidden para exibir o ícone do menu
        menuIcon.removeAttribute('hidden');
    } else {
        // Adiciona o atributo hidden para ocultar o ícone do menu
        menuIcon.setAttribute('hidden', true);
    }
}

// Chama a função ao carregar a página e redimensionar a tela
window.addEventListener('DOMContentLoaded', reorderElements);
window.addEventListener('resize', reorderElements);

// Função para inicializar o carrossel
function initializeCarousel() {
    const carousels = document.querySelectorAll('.carousel');

    carousels.forEach(carousel => {
        let imgs = carousel.querySelectorAll('img');
        let index = 0;

        // Inicializa a exibição das imagens
        function startCarousel() {
            imgs.forEach(img => img.style.display = 'none'); // Oculta todas as imagens
            imgs[index].style.display = 'block'; // Exibe a imagem atual
        }

        function showNextImage() {
            imgs[index].style.display = 'none'; // Oculta a imagem atual
            index = (index + 1) % imgs.length; // Avança para a próxima imagem
            imgs[index].style.display = 'block'; // Exibe a nova imagem
        }

        // Começa o carrossel imediatamente
        startCarousel();
        carousel.interval = setInterval(showNextImage, 3000); // Altera a imagem a cada 3 segundos
    });
}

// Inicializa o carrossel após o carregamento da página
window.addEventListener('DOMContentLoaded', initializeCarousel);

function setActiveLink() {
    const linkId = 'projeto-link'; 
    const linkElement = document.getElementById(linkId);

    if (linkElement) {
        linkElement.classList.add('active');
    }
}


document.addEventListener('DOMContentLoaded', setActiveLink);
