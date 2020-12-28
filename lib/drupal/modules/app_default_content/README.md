# App Default Content

The following entities should be exported:

```bash
drush sqlq "select uuid from node where type='resource';"
drush sqlq "select uuid from file_managed;"
drush sqlq "select uuid from taxonomy_term_data where vid='occupations';"
```
