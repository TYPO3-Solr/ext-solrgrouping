
# Solrgrouping for Apache Solr for TYPO3

## What does it do?

The result grouping add on for the TYPO3 solr extension can be used to split the result set from solr into groups.

An example could be, that you index pages and news and want show the results grouped by the type


## Before you start

Make sure your solr extension is configured to index everything you need

- ext:solr is installed
- TypoScript template is included and solr endpoint is configured
- TYPO3 domain record exists
- Solr sites are initialized with "Initialize Solr connections"
- Solr parts in the reports module are green

If you have problems there please read the documentations referred in the [github] (https://github.com/TYPO3-Solr/ext-solr)
page of the solr extension.

## Configure solr grouping and adapt the Templates

* Example Configuration: [Field group on the solr type field] (Examples/FieldGroups.md)

* Example Configuration: [Group by queries] (Examples/QueryGroups.md)

* Magic document fields that can be used in the template: [templating reference] (Reference/Templating.md)

* The documentation of the available TypoScript properties can be found here: https://forge.typo3.org/projects/extension-solr/wiki/Tx_solrsearch#grouping