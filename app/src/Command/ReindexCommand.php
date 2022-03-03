<?php

// Usage:
// If needed, create new index (make sure to update index name in topiccards.yaml as well):
// curl -X PUT 'http://search:9200/planetdam-0002?pretty' -H 'Content-Type: application/json' -d '@/opt/planetdam/config/elasticsearch-index.json'
// Export node IDs:
// % docker compose exec neo4j cypher-shell -u neo4j -p secret --format plain 'MATCH (n) RETURN n.uuid;' > var/tmp/neo4j-node-uuids.txt
// Reindex:
// % docker exec -i --workdir /opt/planetdam/bin planetdamorg_web_1 /opt/planetdam/bin/console app:reindex < var/tmp/neo4j-node-uuids.txt

namespace App\Command;

use StrehleDe\TopicCards\Configuration\Configuration;
use StrehleDe\TopicCards\Search\IndexUpdate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ReindexCommand extends Command
{
    protected static $defaultName = 'app:reindex';

    protected Configuration $topicCardsConfiguration;


    public function __construct(Configuration $topicCardsConfiguration, string $name = null)
    {
        parent::__construct($name);

        $this->topicCardsConfiguration = $topicCardsConfiguration;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Assuming file format returned by
        //   cypher-shell -u user -p pass --format plain 'MATCH (n) RETURN n.uuid LIMIT 10;'
        // which looks like this:
        // n.uuid
        //"d81b2c7a-179c-4dde-9211-cdfcbeb2731c"
        //"3caa9e27-4e2d-4aaf-bc4d-6c5f5bbe5a1a"

        $cnt = 0;
        $first = true;

        while (!feof(STDIN)) {
            $line = trim(fgets(STDIN));

            // Skip first line, contains header
            if ($first) {
                $first = false;
                continue;
            }

            // Trim quotes
            $uuid = trim($line, '"');

            if (empty($uuid)) {
                continue;
            }

            $cnt++;
            $output->writeln(sprintf('%d: Indexing node <%s>', $cnt, $uuid));

            IndexUpdate::updateNode($uuid, $this->topicCardsConfiguration);
        }

        return Command::SUCCESS;
    }
}