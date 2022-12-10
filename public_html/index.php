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
  echo $app->buildTable();
}
else {
  echo $parser->text($markdown);
}

  // '3287213',
  // '3297177',
  // '3289033',
  // '3316376',
  // '3287784',
  // '3289537',
  // '3304993',
  // '3288229',
  // '3324916',
  // '3289683',
  // '3232190',

echo '</div>';
