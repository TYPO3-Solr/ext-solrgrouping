<?php
namespace ApacheSolrForTypo3\Solrgrouping\Response\Modifier;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2015 Ingo Renner <ingo@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use ApacheSolrForTypo3\Solr\Search;
use ApacheSolrForTypo3\Solr\Search\ResponseModifier;
use ApacheSolrForTypo3\Solr\Search\SearchAware;
use ApacheSolrForTypo3\Solr\Util;

/**
 * Writes statistics after searches have been conducted.
 *
 * @author Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage solr
 */
class Grouping implements ResponseModifier, SearchAware
{

    /**
     * Search instance that provided the response.
     *
     * @var Search
     */
    protected $search;


    /**
     * Sets the search instance that provided the response.
     *
     * @param Search $search Currently active search instance
     */
    public function setSearch(Search $search)
    {
        $this->search = $search;
    }

    /**
     * Modifies the given response and returns the modified response as result.
     *
     * @param \Apache_Solr_Response $response The response to modify
     * @return \Apache_Solr_Response The modified response
     */
    public function modifyResponse(\Apache_Solr_Response $response)
    {
        $documents = $this->getFlattenedDocumentsList($response);
        $response = $this->injectDocumentsIntoResponse($response, $documents);

        return $response;
    }

    /**
     * Injects the flattened documents list back into the response.
     *
     * @param \Apache_Solr_Response $response Response as given by Solr
     * @param array $documents List of documents to inject as results
     * @return \Apache_Solr_Response Response object with result documents
     */
    protected function injectDocumentsIntoResponse(\Apache_Solr_Response $response, array $documents)
    {
        $reflectionClass = new \ReflectionClass('Apache_Solr_Response');
        $parsedDataProperty = $reflectionClass->getProperty('_parsedData');
        $parsedDataProperty->setAccessible(true);

        $parsedData = $parsedDataProperty->getValue($response);

        $responseSection = new \stdClass();
        $responseSection->numFound = $this->getSumNumberOfDocumentsFound($documents);
        $responseSection->docs = $documents;
        $responseSection->maxScore = $this->getDocumentMaximumScore($documents);
        $responseSection->start = 0;

        $parsedData->response = $responseSection;

        $parsedDataProperty->setValue($response, $parsedData);
        $parsedDataProperty->setAccessible(false);

        return $response;
    }

    /**
     * Extracts the documents from the group structure and converts it to a
     * flat list.
     *
     * @param \Apache_Solr_Response $response Solr response
     * @return \Apache_Solr_Document[]
     */
    protected function getFlattenedDocumentsList(\Apache_Solr_Response $response)
    {
        $flatDocumentsList = array();

        foreach ($response->grouped as $groupCollectionKey => $groupCollection) {
            $groupCollectionDocuments = $this->getGroupCollectionDocuments($groupCollectionKey, $groupCollection);

            $groupCollectionDocuments = $this->addGroupConfigurationName($groupCollectionDocuments,
                $groupCollectionKey);

            $flatDocumentsList = array_merge(
                $flatDocumentsList,
                $groupCollectionDocuments
            );
        }

        return $flatDocumentsList;
    }

    /**
     * Gets a flat list of documents of a group collection.
     *
     * Also adds fields to the documents, with data from the group collection.
     *
     * Currently support field based group collections and query based single
     * groups. Query function based groups are coming with Apache Solr 4.0.
     *
     * TODO add function based group support with Apache Solr 4.0
     *
     * @param string $groupCollectionKey Name / value of the group in the Solr response
     * @param \stdClass $groupCollection Group collection, multiple groups for a field's values
     * @return \Apache_Solr_Document[] An array of Apache_Solr_Document objects
     */
    protected function getGroupCollectionDocuments($groupCollectionKey, $groupCollection)
    {
        $groupCollectionDocuments = array();
        $groupConfigurationName = $this->findGroupConfigurationNameByGroupCollectionKey($groupCollectionKey);
        $groupConfiguration = $this->getGroupConfigurationByName($groupConfigurationName);

        if (isset($groupCollection->groups)) {
            // field based group list

            foreach ($groupCollection->groups as $group) {
                $groupDocuments = $this->getFieldGroupDocuments($group);
                $groupDocuments = $this->limitNumberOfGroupResults($groupDocuments, $groupConfiguration);
                $groupDocuments = $this->markGroupBoundary($groupDocuments);

                $groupCollectionDocuments = array_merge(
                    $groupCollectionDocuments,
                    $groupDocuments
                );
            }
        } else {
            // query based group

            $groupCollection->groupValue = $groupCollectionKey;

            $groupDocuments = $this->getQueryGroupDocuments($groupCollection);
            $groupDocuments = $this->limitNumberOfGroupResults($groupDocuments, $groupConfiguration);
            $groupDocuments = $this->markGroupBoundary($groupDocuments);

            $groupCollectionDocuments = array_merge(
                $groupCollectionDocuments,
                $groupDocuments
            );
        }

        return $groupCollectionDocuments;
    }

    /**
     * Gets a flat list of documents from a single group.
     *
     * Also adds fields to the documents, with data from the group.
     *
     * @param \stdClass $group A single group
     * @return \Apache_Solr_Document[] An array of Apache_Solr_Document objects
     */
    protected function getFieldGroupDocuments($group)
    {
        $groupDocuments = array();

        foreach ($group->doclist->docs as $rawDocument) {
            $document = $this->createApacheSolrDocument($rawDocument);

            $document->__solr_grouping_groupingType = 'fieldGroup';
            $document->__solr_grouping_groupValue = $group->groupValue;
            $document->__solr_grouping_groupNumberOfDocumentsFound = $group->doclist->numFound;
            $document->__solr_grouping_groupMaximumScore = $group->doclist->maxScore;

            $groupDocuments[] = $document;
        }

        return $groupDocuments;
    }

    /**
     * Gets a flat list of documents from a query group.
     *
     * Also adds fields to the documents, with data from the group.
     *
     * @param \stdClass $group A query group
     * @return \Apache_Solr_Document[] An array of Apache_Solr_Document objects
     */
    protected function getQueryGroupDocuments($group)
    {
        $groupDocuments = array();

        foreach ($group->doclist->docs as $rawDocument) {
            $document = $this->createApacheSolrDocument($rawDocument);

            $document->__solr_grouping_groupingType = 'queryGroup';
            $document->__solr_grouping_groupValue = $group->groupValue;
            $document->__solr_grouping_groupNumberOfDocumentsFound = $group->doclist->numFound;
            $document->__solr_grouping_groupMaximumScore = $group->doclist->maxScore;

            $groupDocuments[] = $document;
        }

        return $groupDocuments;
    }

    /**
     * Gets the maximum score from a set of documents.
     *
     * @param array $documents An array of Apache_Solr_Document objects.
     * @return float Maximum score retrieved from the given documents.
     */
    protected function getDocumentMaximumScore(array $documents)
    {
        $maximumScore = 0;

        foreach ($documents as $document) {
            if ($document->score > $maximumScore) {
                $maximumScore = $document->score;
            }
        }

        return $maximumScore;
    }

    /**
     * Calculates the overall number of documents found summing up the groups'
     * real number of documents numFound attributes.
     *
     * @param array $documents Array of Apache_Solr_Document objects from flattened group structure with magic __solr_grouping_groupNumberOfDocumentsFound fields
     * @return integer Sum of all the groups numFound values
     */
    protected function getSumNumberOfDocumentsFound(array $documents)
    {
        $numberOfDocumentsFound = 0;

        foreach ($documents as $document) {
            if (isset($document->__solr_grouping_groupStart)) {
                $numberOfDocumentsFound += $document->__solr_grouping_groupNumberOfDocumentsFound;
            }
        }

        return $numberOfDocumentsFound;
    }

    /**
     * Creates an Apache_Solr_Document from a raw stdClass object as parsed by
     * SolrPhpClient.
     *
     * For compatibility reasons taken from Apache_Solr_Response->_parseData()
     *
     * @param \stdClass $rawDocument The raw document as initially returned by SolrPhpClient
     * @return \Apache_Solr_Document Apache Solr Document
     */
    private function createApacheSolrDocument(\stdClass $rawDocument)
    {
        $collapseSingleValueArrays = $this->search->getSolrConnection()->getCollapseSingleValueArrays();

        $document = new \Apache_Solr_Document();
        foreach ($rawDocument as $key => $value) {
            // If a result is an array with only a single value
            // then its nice to be able to access it
            // as if it were always a single value
            if ($collapseSingleValueArrays && is_array($value) && count($value) <= 1) {
                $value = array_shift($value);
            }

            $document->$key = $value;
        }

        return $document;
    }

    /**
     * Adds the name of the group configuration to each Apache_Solr_Document
     * of a collection of document. If a configuration name cannot be found,
     * the name is not added to the documents.
     *
     * @param array $documents Array of Apache_Solr_Document objects
     * @param string $groupCollectionKey Name / value of the group in the Solr response
     * @return array Array of Apache_Solr_Document object with group configuration name added
     */
    protected function addGroupConfigurationName(array $documents, $groupCollectionKey)
    {
        $groupConfigurationName = $this->findGroupConfigurationNameByGroupCollectionKey($groupCollectionKey);

        if ($groupConfigurationName) {
            foreach ($documents as $document) {
                $document->__solr_grouping_groupConfigurationName = $groupConfigurationName;
            }
        }

        return $documents;
    }

    /**
     * Finds the group configuration name for a given group value /
     * group collection name / group field / group query.
     *
     * @param string $groupCollectionKey Group field or query key in Solr's response
     * @return boolean|string Name of the group configuration or FALSE if none could be found.
     */
    protected function findGroupConfigurationNameByGroupCollectionKey($groupCollectionKey)
    {
        $groupConfigurationName = false;

        $solrConfiguration = Util::getSolrConfiguration();
        $groupingConfiguration = $solrConfiguration['search.']['grouping.'];
        $configuredGroups = $groupingConfiguration['groups.'];

        foreach ($configuredGroups as $groupName => $groupConfiguration) {
            if (isset($groupConfiguration['field'])
                && $groupConfiguration['field'] == $groupCollectionKey
            ) {
                $groupConfigurationName = $groupName;
                break;
            }

            if (isset($groupConfiguration['query'])
                && $groupConfiguration['query'] == $groupCollectionKey
            ) {
                $groupConfigurationName = $groupName;
                break;
            }
        }

        if ($groupConfigurationName) {
            $groupConfigurationName = substr($groupConfigurationName, 0, -1);
        }

        return $groupConfigurationName;
    }

    /**
     * Gets a group's TypoScript configuration.
     *
     * @param string $groupConfigurationName Group name
     * @return array The group's configuration
     */
    protected function getGroupConfigurationByName($groupConfigurationName)
    {
        $groupConfiguration = null;

        $solrConfiguration = Util::getSolrConfiguration();
        $groupingConfiguration = $solrConfiguration['search.']['grouping.']['groups.'];

        if (isset($groupingConfiguration[$groupConfigurationName . '.'])) {
            $groupConfiguration = $groupingConfiguration[$groupConfigurationName . '.'];
        }

        return $groupConfiguration;
    }

    /**
     * Reduces a group's number of documents to the ammount configured for
     * the group. If no limit is given for a group explicitly, the default
     * limit will be applied.
     *
     * Consider this a helper tool, since Solr (currently) doesn't provide a
     * way to set the number of results per group.
     *
     * @param array $documents A group's documents.
     * @param array $groupConfiguration Group configuration
     * @return array Array of documents reduced to the configured size.
     */
    protected function limitNumberOfGroupResults(array $documents, array $groupConfiguration)
    {
        $solrConfiguration = Util::getSolrConfiguration();
        $defaultLimit = $solrConfiguration['search.']['grouping.']['numberOfResultsPerGroup'];

        $limit = $defaultLimit;
        if (isset($groupConfiguration['numberOfResultsPerGroup'])) {
            $limit = $groupConfiguration['numberOfResultsPerGroup'];
        }

        $documents = array_slice($documents, 0, $limit);

        $numberOfDocuments = count($documents);
        foreach ($documents as $document) {
            $document->__solr_grouping_groupNumberOfDocuments = $numberOfDocuments;
        }

        return $documents;
    }

    /**
     * Marks a group's boundary by adding magic fields for thefirst (start)
     * and the last (end) documents.
     *
     * @param array $documents Group documents - array of Apache_Solr_Document objects
     * @return array Group documents with marked boundary
     */
    protected function markGroupBoundary(array $documents)
    {
        $groupingType = $documents[0]->__solr_grouping_groupingType;
        $groupStart = 0;
        $groupEnd = count($documents) - 1;

        switch ($groupingType) {

            case 'fieldGroup':
                $groupValue = $documents[0]->__solr_grouping_groupValue;

                $documents[$groupStart]->__solr_grouping_groupStart = $groupValue . '_start';
                $documents[$groupEnd]->__solr_grouping_groupEnd = $groupValue . '_end';
                break;

            case 'queryGroup':
                $documents[$groupStart]->__solr_grouping_groupStart = 'queryGroup_start';
                $documents[$groupEnd]->__solr_grouping_groupEnd = 'queryGroup_end';
                break;

        }

        return $documents;
    }
}
