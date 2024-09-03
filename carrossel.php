<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function exibirCarrossel($diretorio) {
    // Definir o caminho completo para o diretório de imagens
    $caminhoDiretorio = __DIR__ . "/projetos/$diretorio";

    // Verificar se o diretório existe
    if (!is_dir($caminhoDiretorio)) {
        echo "<p>Diretório não encontrado: $caminhoDiretorio</p>";
        return;
    }

    // Buscar apenas imagens no diretório
    $arquivos = glob("$caminhoDiretorio/*.{jpg,jpeg,png,gif}", GLOB_BRACE);

    // Verificar se há arquivos de imagem no diretório
    if (empty($arquivos)) {
        echo "<p>Não há imagens para exibir no diretório: $caminhoDiretorio</p>";
        return;
    }

    echo '<div class="carousel">';
    foreach ($arquivos as $arquivo) {
        $caminhoArquivo = basename($arquivo);
        echo "<img src=\"projetos/$diretorio/$caminhoArquivo\" alt=\"Imagem\">";
    }
    echo '</div>';
}
?>
