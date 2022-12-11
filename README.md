# Drupal Issue Status Dashboard

## Purpose
Generate a table of issues from drupal.org that you've got your eye on.

This uses the slow but steady [drupal.org API](https://www.drupal.org/drupalorg/docs/apis/rest-and-other-apis).

## Usage
Input your request as query parameters to the URL of this site.

| Query Parameter | Role | Example |
| --------------- | ---- | ------- |
| `issues`        | Retrieve core or contrib issues by number | [?issues=3287213+3297177](?issues=3287213+3297177) |


## Sample Output

<table><thead><tr><th>Module</th><th>Issue</th><th>Status</th><th>Last Activity</th></tr></thead><tbody><tr><td>pathologic</td><td><a href="https://www.drupal.org/project/pathologic/issues/3289033">Automated Drupal 10 compatibility fixes</a></td><td>Needs Review</td><td>December 2, 2022</td></tr><tr><td>entity_clone</td><td><a href="https://www.drupal.org/project/entity_clone/issues/3287213">Automated Drupal 10 compatibility fixes</a></td><td>Active</td><td>October 17, 2022</td></tr><tr><td>features</td><td><a href="https://www.drupal.org/project/features/issues/3297177">Automated Drupal 10 compatibility fixes</a></td><td>Active</td><td>August 26, 2022</td></tr></tbody></table>

## Examples

### List of Drupal 10 compatibility issues for select contrib modules
[?issues=3287213+3297177+3289033+3316376+3287784+3289537+3304993+3288229+3324916+3324916+3289683+3232190](?issues=3287213+3297177+3289033+3316376+3287784+3289537+3304993+3288229+3324916+3324916+3289683+3232190)

