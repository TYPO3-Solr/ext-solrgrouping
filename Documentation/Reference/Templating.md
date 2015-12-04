# Templating

When using grouping solr will add a few magic fields to the result documents that will help you to easily style your results page depending on which group is being rendered.

Every field is prefixed with __solr_grouping_.

Example:

	##RESULT_DOCUMENT.__solr_grouping_groupConfigurationName###

Here's a list of the magic fields added to the result documents (don't forget the prefix mentioned above):
groupingType

There are two types of group configurations, field groups and query groups. Depending on the configuration this field will hold a value of either fieldGroup or queryGroup.

## groupValue

When configuring groups using field groups there will be groups depending on the configured field. So there will be multiple groups coming out of one group configuration. When configuring groups using query groups each group configuration will result in just one group of results matching the given query.

For field groups this magic field will hold the value of the field for the current group. For query groups this field's value is the query set in the TypoScript configuration.

## groupNumberOfDocuments

The number of documents returned in that group.

## groupNumberOfDocumentsFound

The number of documents actually found for the group, this can be more than the documents returned.

## groupMaximumScore

Maximum score of the documents in the group. Each group has its own relevance ranking.

## groupStart

This field is set for the first document of a group only. This way you can use an IF construct in your template to insert a title at the beginning of a group by targeting this magic field.

For field groups the field's value will be the group value (field __solr_grouping_groupValue) appended with _start, f.e. pages_start or tt_news_start.

For query groups the field's value is always queryGroup_start.

## groupEnd

Similar to the groupStart field this field is available for the last document of a group only.

For field groups the field's value will be the group value (field __solr_grouping_groupValue) appended with _end, f.e. pages_end or tt_news_end.

For query groups the field's value is always queryGroup_end.

## groupConfigurationName

Holds the name of the TypoScript group configuration the document belongs to.