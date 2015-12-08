### Grouping pages and news with QueryGroups

This example is a bit more complex, but in return query qroups give you a bit more control about what to show. the query configuration supports the full Lucene query language, so you're free to define whatever query you need to build your group.

## Include the Grouping TypoScript Template

Setup the grouping with two section:

	plugin.tx_solr.search.grouping = 1
	plugin.tx_solr.search.grouping {
		groups {
			siteSection4 {
				query = pid_stringS:4
			}

			siteSection62 {
				query = pid_stringS:62
			}
		}
	}

	plugin.tx_solr.search.faceting = 1
	plugin.tx_solr.search.faceting.facets {
		section {
			field = pid
    		label = Site Section
			includeInAvailableFacets = 0
    		includeInUsedFacets = 0
  		}
	}


In the TypoScript configuration above we configure two query groups, each limiting the results of to a certain part of the site. This works the same way as filters applied when using facets. To allow drilling down into a single group and retrieving more results of a group we need to build query links as in the HTML template below (using the SOLR_LINK view helper).

To make the detail links work we use a little trick:

The links will work by actually using a facet - the "section" facet defined below the groups. Since the facet wouldn't make a lot of sense for our visitors and as we need it only for our filtering purposes, we simply disable rendering of the facet in both available and used facet lists (lines 23 and 24).

## Adapt the rendering in your Template

	<!-- ###SOLR_SEARCH_RESULTS### begin -->
		<ol start="###RESULTS.OFFSET###" class="results-list">
			<!-- ###LOOP:RESULT_DOCUMENTS### begin -->
			<!-- ###LOOP_CONTENT### -->
			<li class="results-entry">

				###IF:###RESULT_DOCUMENT.__solr_grouping_groupStart###|!=|###

				###IF:###RESULT_DOCUMENT.__solr_grouping_groupConfigurationName###|==|siteSection4###
				<h4>Examples</h4>
				###IF:###RESULT_DOCUMENT.__solr_grouping_groupConfigurationName###|==|siteSection4###

				###IF:###RESULT_DOCUMENT.__solr_grouping_groupConfigurationName###|==|siteSection62###
				<h4>Resources</h4>
				###IF:###RESULT_DOCUMENT.__solr_grouping_groupConfigurationName###|==|siteSection62###

				<hr/>
				###IF:###RESULT_DOCUMENT.__solr_grouping_groupStart###|!=|###

				<h5 class="results-topic"><a href="###RESULT_DOCUMENT.URL###">###RESULT_DOCUMENT.TITLE###</a></h5>
				<div class="results-teaser">

					<div class="relevance">
						<div class="relevance-label">###LLL:relevance###:</div>
						<div class="relevance-bar">###RELEVANCE_BAR:###RESULT_DOCUMENT######</div>
						<div class="relevance-percent">###RELEVANCE:###RESULT_DOCUMENT######%</div>
					</div>

					<p class="result-content">###RESULT_DOCUMENT.CONTENT###</p>

				</div>

			</li>

			###IF:###RESULT_DOCUMENT.__solr_grouping_groupNumberOfDocumentsFound###|>|###RESULT_DOCUMENT.__solr_grouping_groupNumberOfDocuments######
			<li>

				###IF:###RESULT_DOCUMENT.__solr_grouping_groupEnd###|!=|###

				###IF:###RESULT_DOCUMENT.__solr_grouping_groupConfigurationName###|==|siteSection4|end###
				###SOLR_LINK:More
				Examples||&###TX_SOLR.PREFIX###[grouping]=off&###TX_SOLR.PREFIX###[filter][]=section:###RESULT_DOCUMENT.pid######
				###IF:###RESULT_DOCUMENT.__solr_grouping_groupConfigurationName###|==|siteSection4|end###

				###IF:###RESULT_DOCUMENT.__solr_grouping_groupConfigurationName###|==|siteSection62|end###
				###SOLR_LINK:More
				Resources||&###TX_SOLR.PREFIX###[grouping]=off&###TX_SOLR.PREFIX###[filter][]=section:###RESULT_DOCUMENT.pid######
				###IF:###RESULT_DOCUMENT.__solr_grouping_groupConfigurationName###|==|siteSection62|end###

				###IF:###RESULT_DOCUMENT.__solr_grouping_groupEnd###|!=|###

			</li>
			###IF:###RESULT_DOCUMENT.__solr_grouping_groupNumberOfDocumentsFound###|>|###RESULT_DOCUMENT.__solr_grouping_groupNumberOfDocuments######

			<!-- ###LOOP_CONTENT### -->
			<!-- ###LOOP:RESULT_DOCUMENTS### end -->
		</ol>

	<!-- ###SOLR_SEARCH_RESULTS### end -->