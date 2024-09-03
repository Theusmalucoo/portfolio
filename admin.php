<?php
session_start();
$message = '';
$message_type = '';

// Verificar se o usuário está logado
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Exibir mensagens de sucesso ou erro
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Lidar com a exclusão de projetos
if (isset($_POST['delete_project'])) {
    $project_dir = $_POST['project_dir'];
    $project_path = __DIR__ . '/projetos/' . $project_dir;

    if (is_dir($project_path)) {
        // Excluir o diretório e seus conteúdos
        $files = array_diff(scandir($project_path), array('.', '..'));
        foreach ($files as $file) {
            $file_path = $project_path . '/' . $file;
            is_dir($file_path) ? rmdir($file_path) : unlink($file_path);
        }
        rmdir($project_path);
        $_SESSION['message'] = 'Projeto excluído com sucesso.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Diretório não encontrado.';
        $_SESSION['message_type'] = 'error';
    }
    header('Location: admin.php');
    exit();
}

// Lidar com a criação de novos projetos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete_project'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $directory = 'projetos/' . strtolower(str_replace(' ', '-', $title));

    // Criar diretório do projeto
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);

        // Mover mídia para o diretório do projeto
        foreach ($_FILES['media']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['media']['name'][$key];
            move_uploaded_file($tmp_name, "$directory/$file_name");
        }

        // Criar o arquivo HTML base para o projeto
        $html_content = "
<?php
\$title = '$title';
\$description = '$description';
\$currentDir = __DIR__;
\$mediaFiles = glob(\$currentDir . '/*.{jpg,png,gif,mp4,webm}', GLOB_BRACE);

if (!empty(\$mediaFiles)) {
    \$mainMedia = basename(\$mediaFiles[0]);
    \$mainMediaExt = pathinfo(\$mainMedia, PATHINFO_EXTENSION);
    \$mediaHtml = '';

    foreach (\$mediaFiles as \$index => \$file) {
        \$fileName = basename(\$file);
        \$fileExt = pathinfo(\$fileName, PATHINFO_EXTENSION);
        \$mediaHtml .= '<div class=\"mySlides zoom-container\">';
        \$mediaHtml .= '<div class=\"numbertext\">' . (\$index + 1) . ' / ' . count(\$mediaFiles) . '</div>';
        if (in_array(\$fileExt, ['jpg', 'png', 'gif'])) {
            \$mediaHtml .= '<img src=\"' . \$fileName . '\" class=\"zoomable\">';
        } elseif (in_array(\$fileExt, ['mp4', 'webm'])) {
            \$mediaHtml .= '<video src=\"' . \$fileName . '\" class=\"zoomable\" controls></video>';
        }
        \$mediaHtml .= '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang=\"pt-br\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title><?php echo \$title; ?></title>
    <link rel=\"stylesheet\" href=\"style.css\">
    <script src='script.js' defer></script>
</head>
<?php include '../../header.php'; ?>
<body>
    <div class='main'>
        <section class='projects-section'>
            <div class='project-card'>
                <h2 class='project-title'><?php echo \$title; ?></h2>
                <div class='media-container'>
                    <?php if (!empty(\$mediaFiles)): ?>
                        <?php if (in_array(\$mainMediaExt, ['jpg', 'png', 'gif'])): ?>
                            <img src=\"<?php echo \$mainMedia; ?>\" onclick=\"openModal();currentSlide(1)\" class=\"main-media hover-shadow\">
                        <?php elseif (in_array(\$mainMediaExt, ['mp4', 'webm'])): ?>
                            <video src=\"<?php echo \$mainMedia; ?>\" onclick=\"openModal();currentSlide(1)\" class=\"main-media hover-shadow\" controls></video>
                        <?php endif; ?>
                    <?php endif; ?>
                    <div id=\"myModal\" class=\"modal\">
                        <!-- Botão de Fechar -->
                        <span class=\"close cursor\" onclick=\"closeModal()\">&times;</span>
                        <div class=\"modal-content\">
                            <?php echo \$mediaHtml; ?>
                        </div>
                        <a class=\"prev\" onclick=\"plusSlides(-1)\">&#10094;</a>
                        <a class=\"next\" onclick=\"plusSlides(1)\">&#10095;</a>
                    </div>
                </div>
                <p><?php echo \$description; ?></p>
            </div>
        </section>
    </div>
    <script src='script.js' defer></script>
</body>
</html>
";

file_put_contents("$directory/index.php", $html_content);

        // Criar arquivos CSS e JS específicos para o projeto
        $css_content = "

        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    text-decoration: none;
    border: none;
    outline: none;
    scroll-behavior: smooth;
    font-family: 'Poppins', sans-serif;
}

:root {
    --bg-color: #080808;
    --second-bg-color: #1b0000;
    --text-color: white;
    --main-color: #f60b0b;
}

html, body {
    max-width: 100%;
    overflow-x: hidden;
}

body {
    background: var(--bg-color);
    color: var(--text-color);
    display: flex;
    justify-content: center;
    align-items: flex-start;
    margin: 0;
    padding-top: 80px; /* Espaço para o cabeçalho */
    min-height: 100vh; /* Ocupa toda a altura da tela */
}

header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    padding: 1rem 5%;
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
    height: 80px; /* Defina a altura do cabeçalho */
}

.logo {
    font-size: 2rem;
    color: var(--text-color);
    font-weight: 800;
    cursor: pointer;
    transition: 0.3s ease;
}

.logo:hover {
    transform: scale(1.1);
}

.logo span {
    text-shadow: 0 0 25px var(--main-color);
    color: var(--main-color);
}

.navbar {
    display: flex;
    align-items: center;
}

.navbar a {
    font-size: 1.4rem;
    color: var(--text-color);
    margin-left: 2rem;
    font-weight: 500;
    transition: 0.3s ease;
    border-bottom: 3px solid transparent;
}

.navbar a:hover,
.navbar a.active {
    color: var(--main-color);
    border-bottom: 3px solid var(--main-color);
}

.menu-icon {
    background-image: url('../../menu-icon.png');
    background-size: contain;
    background-repeat: no-repeat;
    width: 30px;
    height: 30px;
    cursor: pointer;
    display: none;
}

.main {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    padding: 0 2rem;
    min-height: 100vh;
    gap: 2rem;
}

.projects-section {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    width: 100%;
    padding: 20px;
}

.project-card {
    background: var(--second-bg-color);
    color: var(--text-color);
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    padding: 20px;
    width: 100%;
    max-width: 1000px;
    margin: 20px auto;
    position: relative;
    height: auto;
    min-height: auto;
    overflow: visible;
    display: flex; /* Torna o project-card um contêiner flexível */
    flex-direction: column; /* Organiza os itens em coluna */
    align-items: center; /* Centraliza itens horizontalmente */
}

.project-card > * {
    margin: 0 auto; /* Garante que os filhos sejam centralizados horizontalmente */
}

.project-title {
    font-size: 2rem;
    color: var(--main-color);
    margin-bottom: 20px;
}

.main-media {
    width: 100%;
    max-width: 600px; /* Define uma largura máxima */
    margin: 20px 0; /* Centraliza verticalmente com margem */
    cursor: pointer;
    display: block; /* Garante que a mídia é um bloco */
    text-align: center; /* Centraliza o conteúdo textual dentro da mídia */
}

.hover-shadow:hover {
    box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.5);
}

.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.8);
    padding-top: 80px; /* Adiciona espaço para o cabeçalho em dispositivos móveis */
}

.modal-content {
    position: relative;
    margin: 0 auto;
    padding: 0;
    width: 80%;
    max-width: 1200px;
    text-align: center;
}

.modal-header {
    position: fixed;
    top: 80px; /* Começa logo abaixo do cabeçalho */
    left: 0;
    width: 100%;
    text-align: right;
    padding: 10px;
    z-index: 2;
}

.close {
    color: white;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: #999;
    text-decoration: none;
    cursor: pointer;
}

.mySlides {
    display: none;
}

.modal img, .modal video {
    max-width: 100%;
    max-height: 80vh;
    height: auto;
    width: auto;
    cursor: zoom-in;
    transition: transform 0.2s ease-in-out;
    position: relative;
    z-index: 1; /* Coloca a mídia abaixo dos botões */
}

.zoomed {
    transform: scale(2);
    cursor: zoom-out;
}

.zoom-container {
    overflow: hidden;
    position: relative;
}

.prev, .next {
    cursor: pointer;
    position: absolute;
    top: 50%;
    width: auto;
    padding: 16px;
    color: white;
    font-weight: bold;
    font-size: 18px;
    border-radius: 3px;
    user-select: none;
    background-color: rgba(0,0,0,0.8);
    transform: translateY(-50%);
}

.prev {
    left: 10px; /* Distância do lado esquerdo */
}

.next {
    right: 10px; /* Distância do lado direito */
}

@media screen and (max-width: 768px) {
    .menu-icon {
        display: block;
    }

    .navbar {
        display: none;
        flex-direction: column;
        position: fixed;
        top: 80px; /* A altura do cabeçalho */
        left: 0;
        width: 100%;
        background: rgba(0, 0, 0, 0.8);
        padding: 2rem 0;
        z-index: 999;
        text-align: center;
    }

    .navbar.active {
        display: flex;
    }

    .navbar a {
        margin: 1rem 0;
    }

    header {
        padding: 1rem 5%;
    }

    .main {
        padding-top: 0;
        justify-content: flex-start;
        padding-bottom: 10vh;
    }

    .project-card {
        margin-top: 20px;
        height: auto;
        overflow: visible;
    }

    .main-media {
        width: 80%;
        height: 80%;
    }

    p {
        margin-top: 5vh;
    }

    h2 {
        margin-bottom: -50vh;
    }

    .player-container {
        height: 20vh;
    }
}

@media screen and (max-width: 480px) {
    .main {
        padding-top: 0;
        padding-bottom: 15vh;
    }

    .project-card {
        max-height: none;
    }

    .main-media {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
}
      
";
        file_put_contents("$directory/style.css", $css_content);

        $js_content = "function toggleMenu() {
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                navbar.classList.toggle('active');
            }
        }
        
        function setActiveLink() {
            const linkId = 'projeto-link'; 
            const linkElement = document.getElementById(linkId);
        
            if (linkElement) {
                linkElement.classList.add('active');
            }
        }
        
        var slideIndex = 1; // Declara a variável slideIndex no escopo global
        
        function openModal() {
            document.getElementById('myModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('myModal').style.display = 'none';
        }
        
        function plusSlides(n) {
            showSlides(slideIndex += n);
        }
        
        function currentSlide(n) {
            showSlides(slideIndex = n);
        }
        
        function showSlides(n) {
            var i;
            var slides = document.getElementsByClassName('mySlides');
            if (n > slides.length) { slideIndex = 1 }
            if (n < 1) { slideIndex = slides.length }
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = 'none';
            }
            slides[slideIndex - 1].style.display = 'block';
        }
        
        function setupZoom() {
            document.querySelectorAll('.modal img, .modal video').forEach(function(media) {
                var zoomed = false;
                var startX, startY, initialX, initialY;
        
                media.addEventListener('click', function() {
                    if (zoomed) {
                        media.classList.remove('zoomed');
                        media.style.transformOrigin = ''; // Resetar origem do zoom
                        media.style.transform = ''; // Resetar o zoom
                        zoomed = false;
                    } else {
                        media.classList.add('zoomed');
                        zoomed = true;
                        // Calcular o centro do elemento
                        media.style.transformOrigin = 'center center';
                    }
                });
        
                media.addEventListener('touchstart', function(e) {
                    if (zoomed) {
                        startX = e.touches[0].clientX;
                        startY = e.touches[0].clientY;
                        var transform = media.style.transform.replace(/[^0-9.,-]/g, '').split(',');
                        initialX = parseFloat(transform[0]) || 0;
                        initialY = parseFloat(transform[1]) || 0;
                    }
                });
        
                media.addEventListener('touchmove', function(e) {
                    if (!zoomed) return;
        
                    let dx = e.touches[0].clientX - startX;
                    let dy = e.touches[0].clientY - startY;
                    media.style.transform = 'scale(2) translate(' + (initialX + dx) + 'px, ' + (initialY + dy) + 'px)';
                });
        
                media.addEventListener('mousemove', function(e) {
                    if (!zoomed) return;
        
                    var rect = media.getBoundingClientRect();
                    var x = (e.clientX - rect.left) / rect.width * 100;
                    var y = (e.clientY - rect.top) / rect.height * 100;
        
                    media.style.transformOrigin = x + '% ' + y + '%';
                });
        
                media.addEventListener('mouseleave', function() {
                    if (zoomed) {
                        media.classList.remove('zoomed');
                        media.style.transform = '';
                        zoomed = false;
                    }
                });
            });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('JavaScript do projeto carregado');
        
            toggleMenu(); 
            setActiveLink(); 
            setupZoom(); 
        
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' || event.keyCode === 27) {
                    closeModal();
                }
            });
        });
        ";
        
        file_put_contents("$directory/script.js", $js_content);

        // Atualizar o arquivo principal de projetos
        $projects_file = 'projeto.php';
        $project_list = "
        <a href='$directory/index.php' class='project'>
            <h2 class='project-title'>$title</h2>
        </a>";

        $current_content = file_get_contents($projects_file);
        $current_content = str_replace('</section>', "$project_list\n</section>", $current_content);
        file_put_contents($projects_file, $current_content);

        $_SESSION['message'] = "Projeto '$title' criado com sucesso!";
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = "O diretório já existe.";
        $_SESSION['message_type'] = 'error';
    }
    header('Location: admin.php');
    exit();
}

// Obter a lista de projetos
$projects = array_diff(scandir(__DIR__ . '/projetos'), array('.', '..'));
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo</title>
    <link rel="stylesheet" href="style-admin.css"> <!-- Link para o CSS separado -->
    <link href='https://unpkg.com/boxicons@2.1.3/css/boxicons.min.css' rel='stylesheet'>
    <style>
        /* Estilos CSS para a mensagem */
        .message {
            opacity: 1;
            transition: opacity 1s ease;
        }
    </style>
</head>
<body>

    <?php if ($message): ?>
        <div id="message" class="message <?= htmlspecialchars($message_type) ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div id='section'>
        <a href="index.php">
            <i class='bx bxs-home' id="home"></i>
        </a>
        <h1>Painel Administrativo</h1>
        <form action="admin.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Título do Projeto:</label>
                <input type="text" name="title" id="title" required>
            </div>

            <div class="form-group">
                <label for="description">Descrição:</label>
                <textarea name="description" id="description" required></textarea>
            </div>

            <div class="form-group">
                <label for="media">Mídias:</label>
                <input type="file" name="media[]" id="media" multiple required>
            </div>

            <input type="submit" value="Criar Projeto">
        </form>
    </div>

    <h2>Projetos Existentes</h2>
    <form action="" method="post">
        <div class="form-group">
            <select name="project_dir" required>
                <option value="">Selecione um projeto</option>
                <?php
                $projects = glob('projetos/*', GLOB_ONLYDIR);
                foreach ($projects as $project) {
                    $dir = basename($project);
                    echo "<option value='$dir'>$dir</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" name="delete_project">Excluir Projeto</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const message = document.getElementById('message');
            
            if (message) {
                setTimeout(() => {
                    message.style.opacity = '0'; // Transição suave
                    setTimeout(() => {
                        message.remove(); // Remove o elemento completamente
                    }, 1000); // Tempo de espera para remover após a transição
                }, 2500); // 5 segundos de exibição
            }
        });
    </script>
</body>
</html>
