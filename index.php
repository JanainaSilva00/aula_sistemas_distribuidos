<?php
define("PROJECT_ROOT_PATH", __DIR__);

// include the base controller file
require_once PROJECT_ROOT_PATH . "/Controller/AbstractController.php";

// include the use model file
require_once PROJECT_ROOT_PATH . "/Model/Student.php";
?>

<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

if ((isset($uri[2]) && $uri[2] != 'students') || !isset($uri[3])) {
    echo '<link rel="stylesheet" href="style.css">';
    echo "API - AULA PARA SISTEMAS DISTIBUÍDOS";
    echo file_get_contents("index.html");
    echo "HTTP 1.1";
    exit();
}

require  "Controller/StudentController.php";
$studentController = new StudentController();
$methodName = $uri[3];
$studentController->{$methodName}();
