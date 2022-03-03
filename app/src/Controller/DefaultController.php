<?php

namespace App\Controller;

use Laudis\Neo4j\Types\CypherMap;
use StrehleDe\TopicCards\Configuration\Configuration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{
    /**
     * @Route("/search")
     * @return Response
     */
    public function search(Configuration $topicCardsConfig): Response
    {
        $neo4jClient = $topicCardsConfig->getNeo4jConfig()->getClient();

        $statement = <<<'EOT'
            MATCH (o:Organization) RETURN o.name, o.url LIMIT 20
            EOT;

        $rows = $neo4jClient->run($statement);

        if (count($rows) < 1) {
            return new Response('No Organization found in database');
        }

        $html = '<body><h1>Organizations</h1><ul>';

        /** @var CypherMap $row */
        foreach ($rows as $row) {
            $html .= sprintf("<li>%s</li>\n", $row->get('o.name'));
        }

        $html .= '</ul>';

        return new Response($html);
    }
}