<?php

namespace TopicBank\Bin;

use \Ulrichsg\Getopt\Getopt;
use \Ulrichsg\Getopt\Option;

require_once dirname(__DIR__) . '/include/init.php';

// Typical invocation:
// TOPICBANK_CONFIG=/path/to/config.x.php php bin/reindex.php --recreate --full


class Reindex
{
    protected $topicmap;
    protected $getopt;


    public function __construct(\TopicBank\Interfaces\iTopicMap $topicmap)
    {
        $this->topicmap = $topicmap;
    }


    public function execute()
    {
        $this->getopt = new Getopt(
        [
            new Option('i', 'index', Getopt::REQUIRED_ARGUMENT),
            new Option('l', 'limit', Getopt::REQUIRED_ARGUMENT),
            new Option(null, 'topics', Getopt::REQUIRED_ARGUMENT),
            new Option(null, 'associations', Getopt::REQUIRED_ARGUMENT),
            new Option(null, 'recreate'),
            new Option(null, 'full'),
            new Option(null, 'config', Getopt::REQUIRED_ARGUMENT),
            new Option('h', 'help')
        ]);

        $this->getopt->parse();

        if ($this->getopt[ 'help' ])
        {
            $this->getopt->setBanner("\nTopicBank Elasticsearch reindex\n\n");

            echo $this->getopt->getHelpText();
            exit;
        }

        $start_time = microtime(true);

        $index = $this->topicmap->getSearchIndex();

        if ($this->getopt[ 'index' ])
            $index = $this->getopt[ 'index' ];

        if ($this->getopt[ 'recreate' ])
        {
            $this->recreateIndex
            (
                $index,
                $this->getIndexParams($index)
            );
        }

        $this->indexTopics($topic_summary);
        $this->indexAssociations($association_summary);

        echo "Done.\n";

        echo $topic_summary . $association_summary;

        $seconds = (microtime(true) - $start_time);
        $minutes = ($seconds / 60);

        printf("Total time: %.1f s = %d minutes\n", $seconds, $minutes);
    }


    protected function getIndexParams($index)
    {
        $params =
        [
            'index' => $index,
            'body' =>
            [
            /*
                'settings' =>
                [
                    'analysis' =>
                    [
                        'analyzer' =>
                        [
                            'ducet_sort' =>
                            [
                                'tokenizer' => 'keyword',
                                'filter' => [ 'icu_collation' ]
                            ]
                        ]
                    ]
                ],
                */
                'mappings' =>
                [
                    '_doc' =>
                    [
                        '_source' => [ 'enabled' => true ],
                        'properties' =>
                        [
                            'association_type_id' =>
                            [
                                'type' => 'keyword'
                            ],
                            'fulltext' =>
                            [
                                'type' => 'text'
                            ],
                            'has_name_type_id' =>
                            [
                                'type' => 'keyword'
                            ],
                            'has_occurrence_type_id' =>
                            [
                                'type' => 'keyword'
                            ],
                            'has_player_id' =>
                            [
                                'type' => 'keyword'
                            ],
                            'has_role_type_id' =>
                            [
                                'type' => 'keyword'
                            ],
                            'label' =>
                            [
                                'type' => 'text',
                                'copy_to' => 'fulltext',
                                'fields' =>
                                [
                                    'raw' =>
                                    [
                                        'type' => 'keyword'
                                    ]
                                ]
                            ],
                            'name' =>
                            [
                                'type' => 'text',
                                'copy_to' => 'fulltext'
                            ],
                            'occurrence' =>
                            [
                                'type' => 'text',
                                'copy_to' => 'fulltext'
                            ],
                            'topic_type_id' =>
                            [
                                'type' => 'keyword'
                            ],
                            'type' =>
                            [
                                'type' => 'keyword'
                            ],
                            'subject' =>
                            [
                                'type' => 'keyword'
                            ],
                        ]
                    ]
                ]
            ]
        ];

        $callback_result = [ ];

        $this->topicmap->trigger
        (
            \TopicBank\Backends\Db\Search::EVENT_INDEX_PARAMS,
            [ 'index_params' => $params ],
            $callback_result
        );

        if (isset($callback_result[ 'index_params' ]) && is_array($callback_result[ 'index_params' ]))
            $params = $callback_result[ 'index_params' ];

        return $params;
    }


    protected function recreateIndex($index, $params)
    {
        $services = $this->topicmap->getServices();

        $services->search->init();

        $elasticsearch = $services->search->getElasticSearchClient();

        if ($elasticsearch->indices()->exists([ 'index' => $index ]))
        {
            printf("Deleting index %s...\n", $index);

            $response = $elasticsearch->indices()->delete([ 'index' => $index ]);

            print_r($response);
        }

        printf("Creating index %s...\n", $index);

        $response = $elasticsearch->indices()->create($params);

        print_r($response);
    }


    protected function indexTopics(&$summary)
    {
        echo "Indexing topics...\n";

        $start_time = microtime(true);

        if ($this->getopt[ 'full' ])
        {
            $limit = 0;

            if ($this->getopt[ 'limit' ])
                $limit = $this->getopt[ 'limit' ];

            $topic_ids = $this->topicmap->getTopicIds([ 'limit' => $limit ]);
        }

        $topic = $this->topicmap->newTopic();
        $cnt = 0;

        foreach ($topic_ids as $topic_id)
        {
            $ok = $topic->load($topic_id);

            if ($ok >= 0)
                $ok = $topic->index();

            printf("#%d %s (%s)\n", ++$cnt, $topic->getId(), $ok);

            if (($limit > 0) && ($cnt >= $limit))
                break;
        }

        $summary = $this->formatSummary('topic', $cnt, $start_time);
    }


    protected function indexAssociations(&$summary)
    {
        $limit = 0;

        if ($this->getopt[ 'full' ])
        {
            if ($this->getopt[ 'limit' ])
                $limit = $this->getopt[ 'limit' ];

            $association_ids = $this->topicmap->getAssociationIds([ 'limit' => $limit ]);
        }

        echo "Indexing associations...\n";

        $association = $this->topicmap->newAssociation();
        $cnt = 0;
        $start_time = microtime(true);

        foreach ($association_ids as $association_id)
        {
            $ok = $association->load($association_id);

            if ($ok >= 0)
                $ok = $association->index();

            printf("#%d %s (%s)\n", ++$cnt, $association->getId(), $ok);

            if (($limit > 0) && ($cnt >= $limit))
                break;
        }

        $summary = $this->formatSummary('association', $cnt, $start_time);
    }


    protected function formatSummary($type, $cnt, $start_time)
    {
        $total_time = (microtime(true) - $start_time);

        if ($cnt === 0)
            return sprintf("No %ss indexed.\n", $type);

        return sprintf
        (
            "%d %ss indexed in %.1f s (%.3f s per %s).\n",
            $cnt,
            $type,
            $total_time,
            ($total_time / $cnt),
            $type
        );
    }
}


$main = new Reindex($topicmap);
$main->execute();
