# Drupal Issue Status Dashboard

## Purpose
Generate a table of issues from drupal.org that you've got your eye on.

## Usage
Input your request as query parameters to the URL of this site.

| Query Parameter | Role | Example |
| --------------- | ---- | ------- |
| `issues`        | Retrieve core or contrib issues by number | [?issues=3287213+3297177](?issues=3287213+3297177) |
| `projects`        | Retrieve all open issues for project(s) (find project IDs by viewing page source `shortlink` value) | [?projects=2549777+2975380](?projects=2549777+2975380) |
| `sort`          | Sort by `changed`, `module`, or `status` | [?issues=3287213+3297177&sort=status](?issues=3287213+3297177&sort=status) |
| `include_closed`          | (Boolean) Whether or not to include closed issues | [?issues=3287213+3297177&include_closed=1](?issues=3287213+3297177&include_closed=1) |

## Sample Output

<table><thead><tr><th>Module</th><th>Issue</th><th>Status</th><th>Last Activity</th></tr></thead><tbody><tr><td>pathologic</td><td><a href="https://www.drupal.org/project/pathologic/issues/3289033">Automated Drupal 10 compatibility fixes</a></td><td>Needs Review</td><td>December 2, 2022</td></tr><tr><td>entity_clone</td><td><a href="https://www.drupal.org/project/entity_clone/issues/3287213">Automated Drupal 10 compatibility fixes</a></td><td>Active</td><td>October 17, 2022</td></tr><tr><td>features</td><td><a href="https://www.drupal.org/project/features/issues/3297177">Automated Drupal 10 compatibility fixes</a></td><td>Active</td><td>August 26, 2022</td></tr></tbody></table>

## Examples

### List of Drupal 10 compatibility issues for select contrib modules
[?issues=3287213+3297177+3289033+3316376+3287784+3289537+3304993+3288229+3324916+3324916+3289683+3232190](?issues=3287213+3297177+3289033+3316376+3287784+3289537+3304993+3288229+3324916+3324916+3289683+3232190)

