<?php
include_once 'carrossel.php';

$projects_directory = 'projetos/';
$projects = glob("$projects_directory*/");

foreach ($projects as $project) {
    $project_name = basename($project);
    $project_dir = $projects_directory . $project_name;
    $index_path = $project_dir . '/index.php'; // Caminho para o arquivo index.php do projeto
    ?>
    <a href="<?php echo htmlspecialchars($index_path); ?>" class='project'>
        <?php exibirCarrossel($project_name); ?>
        <h2 class='project-title'><?php echo htmlspecialchars($project_name); ?></h2>
    </a>
    <?php
}
?>
