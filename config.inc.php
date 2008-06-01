<?PHP
$dbhost='HOST';
$dbname='BD';
$dbuser='USER';
$dbpassword='PASS';
$path_smarty="/usr/local/www/data/Smarty-2.6.11/libs/";
ini_set("include_path",".:$path_smarty");

require('Smarty.class.php');
$smarty = new Smarty();
$smarty->template_dir = '/usr/local/www/data/travian/templates/';
$smarty->compile_dir = '/usr/local/www/data/travian/templates_c/';


?>