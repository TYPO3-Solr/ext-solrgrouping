.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _conf-tx-solr-search-grouping:

Configuration Reference
=======================

The following options are subsection of ``plugin.tx_solr.search``.

grouping
--------

:Type: Boolean
:Default: 0
:Since: 1.0

Set ``plugin.tx_solr.search.grouping = 1`` to enable grouping.

grouping.numberOfGroups
-----------------------

:Type: Integer
:Default: 5
:Since: 1.0

Number of groups to return.

grouping.numberOfResultsPerGroup
--------------------------------

:Type: Integer
:Default: 5
:Since: 1.0

Number of results to return per group. Can be overridden per group configuration.

grouping.allowGetParameterSwitch
--------------------------------

:Type: Boolean
:Default: 0
:Since: 1.0

For technical reasons in the implementation of grouping on the Solr server side you
won't be able to use pagination when grouping is enabled.

Usually you will be grouping results as a first presentation of results for a
visitor's search request. From that group view you can then link to separate views
for each group that shows all results for a given group. You can put each of these
views on a separate page or stay on the same page as the original group view.

For the later case where you want to stay on the same page you need to tell the solr
extension to turn off grouping. To do this there is a GET parameter - ``tx_solr[grouping]=off``.
To make the extension recognize the GET parameter you need to enable this option.

grouping.groups
---------------

:Type: Array
:Since: 1.0

Defines which fields you want to use for grouping. It's a list of grouping configurations.

grouping.groups[groupingName] - Groups configuration
You can add new groups simply by adding grouping configurations in TypoScript.
[groupingName] is a grouping configuration name, kind of a "container" for a single
grouping configuration. All configuration options for creating a groups is defined
in such a container.

There are currently two types of grouping configurations, field groups, and query
groups. A field grouping configuration will usually result in multiple groups,
depending on the values of that index field. A group grouping configuration results
in a single group, giving you a bit more control on what groups to display.

Groups are rendered in the order you define your grouping configurations in TypoScript.

Each grouping configuration must at least have a field or a query option. Solr can only
create groups from string-like fields.

grouping.groups.[groupingName].field
------------------------------------

:Type: String
:Required: yes, or query below
:Since: 1.0

Defines which field's values to use to create groups from. Results in multiple groups,
depending on the field's values.

.. code-block:: typoscript

    plugin.tx_solr.search.grouping.groups {

      typeFieldGroup {
        field = type
      }

    }

grouping.groups.[groupingName].query
------------------------------------

:Type: String
:Required: yes, or field above
:Since: 1.0

Defines a query to creates a single group of results that match this query.

.. code-block:: typoscript

    plugin.tx_solr.search.grouping.groups {

      siteSectionGroup {
        query = pid_stringS:4
      }

    }

grouping.groups.[groupingName].numberOfResultsPerGroup
------------------------------------------------------
:Type: Integer
:Since: 1.0

Overwrites the global setting in ``plugin.tx_solr.search.grouping.numberOfResultsPerGroup``.
