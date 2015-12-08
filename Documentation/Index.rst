.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: Includes.txt


.. _start:

=======================================
Apache Solr for TYPO3 - Result Grouping
=======================================

.. only:: html

	:Classification:
		solr

	:Version:
		|release|

	:Language:
		en

	:Description:
		Apache Solr for TYPO3 - Result Grouping allows grouping of documents sharing a common field. For each group the most relevant documents are returned.

	:Keywords:
		search, full text, index, solr, lucene, fast, query, results, grouping, field group, query group

	:Copyright:
		2009-2015

	:Author:
		Ingo Renner

	:Email:
		ingo@typo3.org

	:License:
		This document is published under the Open Content License
		available from http://www.opencontent.org/opl.shtml

	:Rendered:
		|today|

	The content of this document is related to TYPO3,
	a GNU/GPL CMS/Framework available from `typo3.org <http://www.typo3.org/>`_.


	**Table of Contents**

.. toctree::
	:maxdepth: 3
	:titlesonly:
	:glob:


What does it do?
================

The result grouping addon for the TYPO3 solr extension can be used to split the result set from Solr into groups.

An example could be, that you index pages, products, and news and want show the results grouped by the type.


Before you start
================

Make sure your solr extension is configured to index everything you need

* EXT:solr is installed
* TypoScript template is included and solr endpoint is configured
* TYPO3 domain record exists
* Solr sites are initialized through "Initialize Solr connections"
* Solr checks in the reports module are green

If you run into any issues with setting up the base EXT:solr extension, please consult the `documentation <https://forge.typo3.org/projects/extension-solr/wiki>`_.
Also please don't hesitate to ask for help on the `EXT:solr Slack channel <https://typo3.slack.com/messages/ext-solr/>`_
