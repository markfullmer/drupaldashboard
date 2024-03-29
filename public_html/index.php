<?php
require '../vendor/autoload.php';
require 'head.php';

use markfullmer\drupaldashboard\DrupalDashboard;

$markdown = file_get_contents('../README.md');
$parser = new Parsedown();
echo '<div class="container">';

$title = 'Drupal Issue Status Dashboard';
if (isset($_REQUEST['title'])) {
  $title = htmlspecialchars(strip_tags($_REQUEST['title']));
}

if (isset($_REQUEST['issues'])) {
  $issues = [];
  $issues = explode(' ', $_REQUEST['issues']);
  $app = new DrupalDashboard($issues, 'issues');
  echo "<h1>$title</h1>";
  echo '<p><a href="/">Home</a> | <a href="https://github.com/markfullmer/drupaldashboard">Source code</a></p>';
  echo $app->buildTable();
}
elseif (isset($_REQUEST['projects'])) {
  $issues = [];
  $issues = explode(' ', $_REQUEST['projects']);
  $app = new DrupalDashboard($issues, 'projects');
  echo "<h1>$title</h1>";
  echo '<p><a href="/">Home</a> | <a href="https://github.com/markfullmer/drupaldashboard">Source code</a></p>';
  echo $app->buildTable();
}
else {
  echo $parser->text($markdown);
}
echo '</div>';
