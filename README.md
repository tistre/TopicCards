TopicCards
==========

Build the PHP Docker image:

```
% docker compose build php
```

Install dependencies:

```
% docker compose run --rm --workdir /opt/app php composer install
```

Start application:

```
% docker compose up -d
```

Import data into Neo4j:

```
% docker exec -i --workdir /opt/app/bin topiccards-php-1 /opt/app/bin/console app:xml-to-cypher /opt/tmp/example.xml > var/tmp/example.cypher
% docker exec -i topiccards-neo4j-1 cypher-shell -u neo4j -p secret < var/tmp/example.cypher
```

Create Elasticsearch index:

```
% docker compose exec php bash
$ curl -X PUT 'http://search:9200/topiccards-0001?pretty' -H 'Content-Type: application/json' -d '@/opt/app/config/elasticsearch-index.json'
```

Fill Elasticsearch index:

```
% docker exec -i topiccards-neo4j-1 cypher-shell -u neo4j -p secret --format plain 'MATCH (n) RETURN n.uuid;' > var/tmp/neo4j-node-uuids.txt
% docker exec -i --workdir /opt/app/bin topiccards-php-1 /opt/app/bin/console app:reindex < var/tmp/neo4j-node-uuids.txt
```