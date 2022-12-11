<?php
require '../vendor/autoload.php';
require 'head.php';

use markfullmer\drupaldashboard\DrupalDashboard;

$markdown = file_get_contents('../README.md');
$parser = new Parsedown();
echo '<div class="container">';

if (isset($_REQUEST['issues'])) {
  $issues = [];
  $issues = explode(' ', $_REQUEST['issues']);
  $app = new DrupalDashboard($issues);
  echo '<h1>Drupal Issue Status Dashboard</h1>';
  echo '<p><a href="/">Home</a> | <a href="https://github.com/markfullmer/drupaldashboard">Source code</a></p>';
  echo $app->buildTable();
}
else {
  echo $parser->text($markdown);
}
echo '</div>';
