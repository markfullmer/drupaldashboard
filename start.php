<?php

$issue_numbers = [
  '3287213',
  '3297177',
  '3289033',
  '3316376',
  '3287784',
  '3289537',
  '3304993',
  '3288229',
  '3324916',
  '3289683',
  '3232190',
];

$issues = [];

function project_name($url) {
  preg_match('/https:\/\/www.drupal.org\/project\/([\w\_]*)\/(.*)/', $url, $output);
  if (isset($output[1])) {
    return $output[1];
  }
  return $url;
}

function status_map($id) {
  $map = [
    '1' => 'Active',
    '7' => 'Closed (fixed)',
    '8' => 'Needs Review',
    '13' => 'Needs Work',
    '14' => 'RTBC',
    '15' => 'Patch (to be ported)',
  ];
  if (isset($map[$id])) {
    return $map[$id];
  }
  return $id;
}

foreach ($issue_numbers as $i) {
  $raw = file_get_contents('https://www.drupal.org/api-d7/node.json?type=project_issue&nid=' . $i);
  $data = json_decode($raw, TRUE);
  $issue = $data['list'][0];
  $issues[$issue['changed']] = [
    'module' => project_name($issue['url']),
    'title' => $issue['title'],
    'status' => status_map($issue['field_issue_status']),
    'updated' => date("F j, Y", $issue['changed']),
    'url' => $issue['url'],
  ];
}
krsort($issues);
echo '<table>';
echo '<tr><th>Module</th><th>Issue</th><th>Status</th><th>Last Activity</th>';
foreach ($issues as $issue) {
  echo '<tr><td>' . $issue['module'] . '</td><td><a href="' . $issue['url'] . '">' . $issue['title'] . '</a></td><td>' . $issue['status'] . '</td><td>' . $issue['updated'] . '</td></tr>';
}
echo '</table>' . PHP_EOL;
