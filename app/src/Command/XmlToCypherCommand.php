<?php

// Usage:
// % docker exec -i --workdir /opt/planetdam/bin planetdamorg_web_1 /opt/planetdam/bin/console app:xml-to-cypher /var/opt/topicbank/topics-graph.xml > /var/opt/topicbank/topics.cypher
// % docker compose exec neo4j cypher-shell -u neo4j -p secret -f /var/tmp/topics.cypher

namespace App\Command;

use StrehleDe\TopicCards\Configuration\Configuration;
use StrehleDe\TopicCards\Import\SimpleImportScript;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class XmlToCypherCommand extends Command
{
    protected static $defaultName = 'app:xml-to-cypher';

    protected Configuration $topicCardsConfiguration;


    public function __construct(Configuration $topicCardsConfiguration, string $name = null)
    {
        parent::__construct($name);

        $this->topicCardsConfiguration = $topicCardsConfiguration;
    }


    protected function configure()
    {
        $this
            ->setHelp('Convert Graph XML file to Neo4j Cypher text')
            ->addArgument('xmlFileName', InputArgument::REQUIRED, 'Path to the Graph XML input file');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $script = new SimpleImportScript($this->topicCardsConfiguration->getNeo4jConfig()->getClient());
        $script->convertFileToCypher($input->getArgument('xmlFileName'));

        return Command::SUCCESS;
    }
}