<?php

namespace markfullmer\drupaldashboard;

/**
 * Application View logic.
 */
class DrupalDashboard {

  public array $issues;

  public string $type;

  /**
   * Class constructor.
   *
   * @param array $issues
   *   An array of issue ids from drupal.org
   *
   * @param string $type
   *  The request type: 'projects' or 'issues'
   */
  public function __construct($numbers, $type = 'issues') {
    $this->type = $type;
    $yesterday = time() - 60 * 60 * 24;
    if ($type === 'projects') {
      $issue_numbers = $this->getProjectIssues($numbers);
    }
    else {
      $issue_numbers = $numbers;
    }
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
          'id' => $i,
          'url' => $raw_data['url'],
          'title' => $raw_data['title'],
          'field_issue_status' => $raw_data['field_issue_status'],
          'changed' => $raw_data['changed'],
        ];
        file_put_contents($file, serialize($issue));
      }
      $issues[$issue['changed']] = [
        'id' => $i,
        'module' => $this->project_name($issue['url']),
        'title' => $issue['title'],
        'status' => $this->status_map($issue['field_issue_status']),
        'changed' => date("F j, Y", $issue['changed']),
        'url' => $issue['url'],
        'remove' => $this->getRemoveLink($issue_numbers, $i),
      ];
    }
    krsort($issues);
    $this->issues = $issues;
  }

  protected function getProjectIssues($projects) {
    $yesterday = time() - 60 * 60 * 24;
    $issues = [];
    foreach ($projects as $project) {
      $project_id = NULL;
      if (is_numeric($project)) {
        $project_id = $project;
      }
      else {
        $project_file = file_get_contents('projects.txt');
        $existing_projects = [];
        if (file_exists($project_file)) {
          $existing_projects = json_decode($project_file, TRUE);
        }
        if (array_key_exists($project, $existing_projects)) {
          $project_id = $existing_projects[$project];
        }
        else {
          $project_json = file_get_contents("https://www.drupal.org/api-d7/node.json?field_project_machine_name=$project");
          $project_data = json_decode($project_json, TRUE);
          $project_id = $project_data['list'][0]['nid'] ?? NULL;
          if (isset($project_id)) {
            $existing_projects[$project] = $project_id;
            file_put_contents('projects.txt', json_encode($existing_projects));
          }
        }
      }
      $file = 'project-' . $project_id . '.txt';
      if (file_exists($file) && filemtime($file) > $yesterday) {
        $project_issues = unserialize(file_get_contents($file));
        $issues = array_merge($issues, $project_issues);
      }
      else {
        $project_issues = [];
        $raw = file_get_contents("https://www.drupal.org/api-d7/node.json?type=project_issue&sort=changed&direction=DESC&field_project=$project_id");
        $data = json_decode($raw, TRUE);
        foreach ($data['list'] as $issue) {
          $status = (string) $issue['field_issue_status'];
          if (in_array($status, ['1', '8', '13', '14'])) {
            $project_issues[] = $issue['nid'];
            $issues[] = $issue['nid'];
          }
        }
        file_put_contents($file, serialize($project_issues));
      }
    }
    return $issues;
  }

  protected function getRemoveLink($issue_numbers, $i) {
    $remaining = [];
    foreach ($issue_numbers as $n) {
      if ($n != $i) {
        $remaining[] = $n;
      }
    }
    $query = '?issues=' . implode('+', $remaining);
    return $query;
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
      '2' => 'Fixed',
      '3' => 'Closed (duplicate)',
      '4' => 'Postponed',
      '5' => 'Closed (won\'t fix)',
      '6' => 'Closed (works as designed)',
      '7' => 'Closed (fixed)',
      '8' => 'Needs review',
      '13' => 'Needs work',
      '14' => 'Reviewed & tested by the community',
      '15' => 'Patch (to be ported)',
      '16' => 'Postponed (maintainer needs more info)',
      '17' => 'Closed (outdated)',
      '18' => 'Closed (cannot reproduce)',
    ];

    if (isset($map[$id])) {
      return $map[$id];
    }
    return $id;
  }

  public function buildTable() {
    $output = [];
    $output[] = '<div id="table">';
    if ($this->type === 'projects') {
      $output[] = '<table><thead><th>Module</th><th>Issue</th><th>Status</th><th>Last Activity</th></thead>';
      foreach ($this->issues as $issue) {
        $output[] = '<tr><td>' . $issue['module'] . '</td><td><a href="' . $issue['url'] . '">' . htmlentities($issue['title']) . '</a></td><td>' . $issue['status'] . '</td><td>' . $issue['changed'] . '</td></tr>';
      }
    }
    else {
      $output[] = '<table><thead><th>Module</th><th>Issue</th><th>Status</th><th>Last Activity</th><th>Remove</th></thead>';
      foreach ($this->issues as $issue) {
        $output[] = '<tr><td>' . $issue['module'] . '</td><td><a href="' . $issue['url'] . '">' . htmlentities($issue['title']) . '</a></td><td>' . $issue['status'] . '</td><td>' . $issue['changed'] . '</td><td><a href=" ' . $issue['remove'] . '">&#8855;</a></td></tr>';
      }
    }
    return implode("", $output);
  }
}
