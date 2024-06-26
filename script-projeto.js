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

// Função para inicializar o carrossel
function initializeCarousel() {
    const carousels = document.querySelectorAll('.carousel');

    carousels.forEach(carousel => {
        let imgs = carousel.querySelectorAll('img');
        let index = 0;

        function showNextImage() {
            imgs[index].style.display = 'none';
            index = (index + 1) % imgs.length;
            imgs[index].style.display = 'block';
        }

        carousel.addEventListener('mouseenter', () => {
            imgs.forEach(img => img.style.display = 'none');
            imgs[index].style.display = 'block';
            carousel.interval = setInterval(showNextImage, 1000); // Muda a imagem a cada 1 segundo
        });

        carousel.addEventListener('mouseleave', () => {
            clearInterval(carousel.interval);
            imgs.forEach(img => img.style.display = 'block');
        });
    });
}

