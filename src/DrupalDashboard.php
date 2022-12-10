<?php

namespace markfullmer\drupaldashboard;

/**
 * Application View logic.
 */
class DrupalDashboard {

  public array $issues;

  /**
   * Class constructor.
   *
   * @param array $issues
   *   An array of issue ids from drupal.org
   */
  public function __construct($issue_numbers) {
    $yesterday = time() - 60 * 60 * 24;
    foreach ($issue_numbers as $i) {
      $i = (int) $i;
      $file = $i . '.txt';
      if (file_exists($file) && filemtime($file) > $yesterday) {
        $issue = unserialize(file_get_contents($file));
      }
      else {
        $raw = file_get_contents('https://www.drupal.org/api-d7/node.json?type=project_issue&nid=' . $i);
        $data = json_decode($raw, TRUE);
        if (!isset($data['list']) || !isset($data['list'][0])) {
          continue;
        }
        $raw_data = $data['list'][0];
        $issue = [
          'url' => $raw_data['url'],
          'title' => $raw_data['title'],
          'field_issue_status' => $raw_data['field_issue_status'],
          'changed' => $raw_data['changed'],
        ];
        file_put_contents($file, serialize($issue));
      }
      $issues[$issue['changed']] = [
        'module' => $this->project_name($issue['url']),
        'title' => $issue['title'],
        'status' => $this->status_map($issue['field_issue_status']),
        'changed' => date("F j, Y", $issue['changed']),
        'url' => $issue['url'],
      ];
    }
    krsort($issues);
    $this->issues = $issues;
  }

  protected function cleanup() {
    foreach(glob(getcwd() . '/*.txt') as $file) {
      if (is_file($file)) {
        unlink($file);
      }
    }
  }

  protected function project_name($url) {
    preg_match('/https:\/\/www.drupal.org\/project\/([\w\_]*)\/(.*)/', $url, $output);
    if (isset($output[1])) {
      return $output[1];
    }
    return $url;
  }

  protected function status_map($id) {
    $map = [
      '1' => 'Active',
      '7' => 'Closed (fixed)',
      '8' => 'Needs Review',
      '13' => 'Needs Work',
      '14' => 'Reviewed & Tested by the Community',
      '15' => 'Patch (to be ported)',
    ];
    if (isset($map[$id])) {
      return $map[$id];
    }
    return $id;
  }

  public function buildTable() {
    $output = [];
    $output[] = '<div id="table">';

    $output[] = '<table><thead><th>Module</th><th>Issue</th><th>Status</th><th>Last Activity</th></thead>';
    foreach ($this->issues as $issue) {
      $output[] = '<tr><td>' . $issue['module'] . '</td><td><a href="' . $issue['url'] . '">' . $issue['title'] . '</a></td><td>' . $issue['status'] . '</td><td>' . $issue['changed'] . '</td></tr>';
    }
    return implode("", $output);
  }
}
