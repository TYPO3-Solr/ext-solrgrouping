### Grouping pages and news with FieldGrouing on type

To setup the grouping for pages and news (ext:news). Make sure you did the following steps:

## Include the Grouping TypoScript Template

You can include the shipped Template "Apache Solr - Result Grouping" and make sure grouping is enabled:

	plugin.tx_solr.search.grouping = 1

or

Setup your own grouping:

	plugin.tx_solr {
		search {
			grouping = 1
			grouping {
				numberOfGroups = 5
				numberOfResultsPerGroup = 5
				allowGetParameterSwitch = 0
				groups {
					typeGroup {
						field = type
					}
				}
			}
		}
	}


## Adapt the rendering in your Template

	<!-- ###SOLR_SEARCH_RESULTS### begin -->
		<ol start="###RESULTS.OFFSET###" class="results-list">
		<!-- ###LOOP: RESULT_DOCUMENTS### begin -->
			<!-- ###LOOP_CONTENT### -->
			<li class="results-entry">

				###IF: ###RESULT_DOCUMENT.__solr_grouping_groupStart###|==|pages_start###
				<h4 style="">Pages</h4>
				###IF: ###RESULT_DOCUMENT.__solr_grouping_groupStart###|==|pages_start###

				###IF:###RESULT_DOCUMENT.__solr_grouping_groupStart###|==|tx_news_domain_model_news_start###
				<h4>News (EXT: news)</h4>
				###IF: ###RESULT_DOCUMENT.__solr_grouping_groupStart###|==|tx_news_domain_model_news_start###

				###IF: ###RESULT_DOCUMENT.__solr_grouping_groupStart###|!=|###
				<hr />
				###IF: ###RESULT_DOCUMENT.__solr_grouping_groupStart###|!=|###

				<h5 class="results-topic"><a href="###RESULT_DOCUMENT.URL###">###RESULT_DOCUMENT.TITLE###</a></h5>
				<div class="results-teaser">
					<div class="relevance">
						<div class="relevance-label">###LLL: relevance###: </div>
						<div class="relevance-bar">###RELEVANCE_BAR: ###RESULT_DOCUMENT######</div>
						<div class="relevance-percent">###RELEVANCE: ###RESULT_DOCUMENT######%</div>
					</div>
					<p class="result-content">###RESULT_DOCUMENT.CONTENT###</p>
				</div>
			</li>

			###IF: ###RESULT_DOCUMENT.__solr_grouping_groupNumberOfDocumentsFound###|>|###RESULT_DOCUMENT.__solr_grouping_groupNumberOfDocuments######
			<li>
				###IF: ###RESULT_DOCUMENT.__solr_grouping_groupEnd###|==|pages_end###
				###SOLR_LINK: More Pages||&###TX_SOLR.PREFIX###[grouping]=off&###TX_SOLR.PREFIX###[filter][]=type: ###RESULT_DOCUMENT.type######
				###IF:###RESULT_DOCUMENT.__solr_grouping_groupEnd###|==|pages_end###

				###IF: ###RESULT_DOCUMENT.__solr_grouping_groupEnd###|==|tx_news_domain_model_news_end###
				###SOLR_LINK: More News (EXT: news)||&###TX_SOLR.PREFIX###[grouping]=off&###TX_SOLR.PREFIX###[filter][]=type: ###RESULT_DOCUMENT.type######
				###IF:###RESULT_DOCUMENT.__solr_grouping_groupEnd###|==|tx_news_domain_model_news_end###
			</li>
			###IF: ###RESULT_DOCUMENT.__solr_grouping_groupNumberOfDocumentsFound###|>|###RESULT_DOCUMENT.__solr_grouping_groupNumberOfDocuments######

			<!-- ###LOOP_CONTENT### -->
		<!-- ###LOOP: RESULT_DOCUMENTS### end -->
		</ol>
	<!-- ###SOLR_SEARCH_RESULTS### end -->
