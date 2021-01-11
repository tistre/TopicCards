--TEST--
Xddb\Backends\Db\Association::save(): Basic INSERT functionality
--FILE--
<?php

require_once dirname(__DIR__) . '/include/init.php';
require_once dirname(__DIR__) . '/tests/config.php';

$prefix = 'xddb';

$services = new \Xddb\Backends\Db\Services();
$services->setDbParams($db_params);

$system = new \Xddb\Backends\Db\TopicMapSystem($services);

$services->topicmap = $system->newTopicMap();
$services->topicmap->setUrl($prefix);

// Clean slate

$sql_statements = 
[
    "delete from " . $prefix . "_association",
    "delete from " . $prefix . "_topic where topic_id in ('john', 'jane')",
    "delete from " . $prefix . "_topic"
];

$services->db_utils->connect();

foreach ($sql_statements as $sql_statement)
{
    $sql = $services->db->prepare($sql_statement);    
    $sql->execute();
}

// Requirements

foreach ([ 'person', 'basename', 'maiden_name', 'email', 'xsd_string', 'marriage', 'husband', 'wife' ] as $id)
{
    $topic = $services->topicmap->newTopic();
    $topic->setId($id);
    $topic->save();
}

// Jane

$topic = $services->topicmap->newTopic();
$topic->setId('jane');
$topic->setTypes([ 'person' ]);
$topic->setSubjectIdentifiers(array( 'http://www.example.com/jane' ));

$name = $topic->newName();
$name->setType('basename');
$name->setValue('Jane Doe');

$name = $topic->newName();
$name->setType('basename');
$name->setValue('Jane Smith');
$name->setScope([ 'maiden_name' ]);

$occurrence = $topic->newOccurrence();
$occurrence->setType('email');
$occurrence->setValue('jane.doe@example.com');
$occurrence->setDatatype('xsd_string');

$occurrence = $topic->newOccurrence();
$occurrence->setType('email');
$occurrence->setValue('jane.smith@example.com');
$occurrence->setDatatype('xsd_string');
$occurrence->setScope([ 'maiden_name' ]);

var_dump($topic->save());

// John

$topic = $services->topicmap->newTopic();
$topic->setId('john');
$topic->setTypes([ 'person' ]);
$topic->setSubjectIdentifiers(array( 'http://www.example.com/john' ));

$name = $topic->newName();
$name->setType('basename');
$name->setValue('John Doe');

var_dump($topic->save());

// Marriage

$association = $services->topicmap->newAssociation();
$association->setId('jane_and_john');
$association->setType('marriage');

$role = $association->newRole();
$role->setPlayer('jane');
$role->setType('wife');

$role = $association->newRole();
$role->setPlayer('john');
$role->setType('husband');

var_dump($association->save());

foreach ($services->topicmap->getTopics([ 'type' => 'person' ]) as $topic_id)
{
    $topic = $services->topicmap->newTopic();
    
    var_dump($topic_id);
    
    $topic->load($topic_id);
    
    $all = $topic->getAll();
    
    unset($all[ 'created' ]);
    unset($all[ 'updated' ]);
    
    print_r($all);

    foreach ($topic->getNames([ ]) as $name)
    {
        var_dump($name->getType());
        var_dump($name->getValue());
        var_dump($name->getReifier());
        var_dump($name->getScope());
    }
    
    var_dump($topic->getTypes());    
    var_dump($topic->getSubjectIdentifiers());    
    var_dump($topic->getSubjectLocators());

    foreach ($topic->getOccurrences([ ]) as $occurrence)
    {
        var_dump($occurrence->getType());
        var_dump($occurrence->getValue());
        var_dump($occurrence->getReifier());
        var_dump($occurrence->getScope());
    }
}

foreach ($services->topicmap->getAssociations([ ]) as $association_id)
{
    $association = $services->topicmap->newAssociation();
    
    var_dump($association_id);
    
    $association->load($association_id);
    
    $all = $association->getAll();

    unset($all[ 'created' ]);
    unset($all[ 'updated' ]);
    
    print_r($all);

    var_dump($association->getType());
    var_dump($association->getScope());

    foreach ($association->getRoles([ ]) as $role)
    {
        var_dump($role->getType());
        var_dump($role->getPlayer());
        var_dump($role->getReifier());
    }
}

?>
--EXPECT--
int(1)
int(1)
int(1)
string(4) "jane"
Array
(
    [types] => Array
        (
            [0] => person
        )

    [subject_identifiers] => Array
        (
            [0] => http://www.example.com/jane
        )

    [subject_locators] => Array
        (
        )

    [names] => Array
        (
            [0] => Array
                (
                    [value] => Jane Doe
                    [type] => basename
                    [reifier] => 
                    [scope] => Array
                        (
                        )

                )

            [1] => Array
                (
                    [value] => Jane Smith
                    [type] => basename
                    [reifier] => 
                    [scope] => Array
                        (
                            [0] => maiden_name
                        )

                )

        )

    [occurrences] => Array
        (
            [0] => Array
                (
                    [value] => jane.doe@example.com
                    [datatype] => xsd_string
                    [type] => email
                    [reifier] => 
                    [scope] => Array
                        (
                        )

                )

            [1] => Array
                (
                    [value] => jane.smith@example.com
                    [datatype] => xsd_string
                    [type] => email
                    [reifier] => 
                    [scope] => Array
                        (
                            [0] => maiden_name
                        )

                )

        )

    [id] => jane
    [version] => 1
)
string(8) "basename"
string(8) "Jane Doe"
string(0) ""
array(0) {
}
string(8) "basename"
string(10) "Jane Smith"
string(0) ""
array(1) {
  [0]=>
  string(11) "maiden_name"
}
array(1) {
  [0]=>
  string(6) "person"
}
array(1) {
  [0]=>
  string(27) "http://www.example.com/jane"
}
array(0) {
}
string(5) "email"
string(20) "jane.doe@example.com"
string(0) ""
array(0) {
}
string(5) "email"
string(22) "jane.smith@example.com"
string(0) ""
array(1) {
  [0]=>
  string(11) "maiden_name"
}
string(4) "john"
Array
(
    [types] => Array
        (
            [0] => person
        )

    [subject_identifiers] => Array
        (
            [0] => http://www.example.com/john
        )

    [subject_locators] => Array
        (
        )

    [names] => Array
        (
            [0] => Array
                (
                    [value] => John Doe
                    [type] => basename
                    [reifier] => 
                    [scope] => Array
                        (
                        )

                )

        )

    [occurrences] => Array
        (
        )

    [id] => john
    [version] => 1
)
string(8) "basename"
string(8) "John Doe"
string(0) ""
array(0) {
}
array(1) {
  [0]=>
  string(6) "person"
}
array(1) {
  [0]=>
  string(27) "http://www.example.com/john"
}
array(0) {
}
string(13) "jane_and_john"
Array
(
    [roles] => Array
        (
            [0] => Array
                (
                    [player] => jane
                    [type] => wife
                    [reifier] => 
                )

            [1] => Array
                (
                    [player] => john
                    [type] => husband
                    [reifier] => 
                )

        )

    [id] => jane_and_john
    [version] => 1
    [type] => marriage
    [reifier] => 
    [scope] => Array
        (
        )

)
string(8) "marriage"
array(0) {
}
string(4) "wife"
string(4) "jane"
string(0) ""
string(7) "husband"
string(4) "john"
string(0) ""
