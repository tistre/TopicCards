<?php

namespace Xddb\Backends\Db;


trait TopicDbAdapter
{
    public function selectAll(array $filters)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $this->services->topicmap->getUrl();
        
        $sql = $this->services->db->prepare(sprintf
        (
            'select * from %s_topic'
            . ' where topic_id = :topic_id', 
            $prefix
        ));

        $sql->bindValue(':topic_id', $filters[ 'id' ], \PDO::PARAM_STR);
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $result = [ ];
        
        $name = new Name($this->services);
        $occurrence = new Occurrence($this->services);
        
        foreach ($sql->fetchAll() as $row)
        {
            $row = $this->services->db_utils->stripColumnPrefix('topic_', $row);
            
            $row[ 'types' ] = $this->selectTypes([ 'topic' => $row[ 'id' ] ]);
            
            $row[ 'subject_identifiers' ] = $this->selectSubjectIdentifiers([ 'topic' => $row[ 'id' ] ]);

            $row[ 'subject_locators' ] = $this->selectSubjectLocators([ 'topic' => $row[ 'id' ] ]);

            $row[ 'names' ] = $name->selectAll([ 'topic' => $row[ 'id' ] ]);

            $row[ 'occurrences' ] = $occurrence->selectAll([ 'topic' => $row[ 'id' ] ]);

            $result[ ] = $row;
        }

        return $result;        
    }
    

    protected function selectTypes(array $filters)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $this->services->topicmap->getUrl();

        $sql = $this->services->db->prepare(sprintf
        (
            'select type_type from %s_type'
            . ' where type_topic = :type_topic', 
            $prefix
        ));
        
        $sql->bindValue(':type_topic', $filters[ 'topic' ], \PDO::PARAM_STR);
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $rows = $sql->fetchAll();
        
        return array_column($rows, 'type_type');
    }    


    protected function selectSubjectIdentifiers(array $filters)
    {
        return $this->selectSubjects($filters, 0);
    }
    

    protected function selectSubjectLocators(array $filters)
    {
        return $this->selectSubjects($filters, 1);
    }
    

    protected function selectSubjects(array $filters, $islocator)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $this->services->topicmap->getUrl();
        
        $sql = $this->services->db->prepare(sprintf
        (
            'select subject_value from %s_subject'
            . ' where subject_topic = :subject_topic'
            . ' and subject_islocator = :subject_islocator', 
            $prefix
        ));
        
        $sql->bindValue(':subject_topic', $filters[ 'topic' ], \PDO::PARAM_STR);
        $sql->bindValue(':subject_islocator', $islocator, \PDO::PARAM_INT);
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $rows = $sql->fetchAll();
        
        return array_column($rows, 'subject_value');
    }


    public function insertAll(array $data)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;
        
        $this->services->db->beginTransaction();
        
        $now = date('c');        
        $data[ 'created' ] = $data[ 'updated' ] = $now;
        
        $data[ 'version' ] = 1;
        
        $values = [ ];
        
        foreach ($data as $key => $value)
        {
            $ignore = [ 'types', 'subject_identifiers', 'subject_locators', 'names', 'occurrences' ];
            
            if (in_array($key, $ignore))
                continue;
            
            if (($key === 'created') || ($key === 'updated'))
                $value = $this->services->db_utils->datetimeToDb($value);
            
            $datatype = \PDO::PARAM_STR;
            
            if ($key === 'version')
                $datatype = \PDO::PARAM_INT;
                
            $values[ ] =
            [
                'column' => 'topic_' . $key,
                'value' => $value,
                'datatype' => $datatype
            ];
        }
        
        $sql = $this->services->db_utils->prepareInsertSql($this->services->topicmap->getUrl() . '_topic', $values);
        
        $ok = $sql->execute();
        
        $ok = ($ok === false ? -1 : 1);
        
        if ($ok >= 0)
            $ok = $this->insertTypes($data[ 'id' ], $data[ 'types' ]);
        
        if ($ok >= 0)
            $ok = $this->insertSubjectIdentifiers($data[ 'id' ], $data[ 'subject_identifiers' ]);
        
        if ($ok >= 0)
            $ok = $this->insertSubjectLocators($data[ 'id' ], $data[ 'subject_locators' ]);
        
        if ($ok >= 0)
        {
            $name = new Name($this->services);
            $ok = $name->insertAll($data[ 'id' ], $data[ 'names' ]);
        }

        if ($ok >= 0)
        {
            $occurrence = new Occurrence($this->services);
            $ok = $occurrence->insertAll($data[ 'id' ], $data[ 'occurrences' ]);
        }

        if ($ok < 0)
        {
            $this->services->db->rollBack();
            return $ok;
        }

        $this->services->db->commit();

        return $ok;
    }


    protected function insertTypes($topic_id, array $types)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;
        
        foreach ($types as $type)
        {
            $values = [ ];

            $values[ ] =
            [
                'column' => 'type_topic',
                'value' => $topic_id
            ];
        
            $values[ ] =
            [
                'column' => 'type_type',
                'value' => $type
            ];
        
            $sql = $this->services->db_utils->prepareInsertSql($this->services->topicmap->getUrl() . '_type', $values);
        
            $ok = $sql->execute();
        
            if ($ok === false)
                return -1;
        }
        
        return 1;
    }


    protected function insertSubjectIdentifiers($topic_id, array $subject_identifiers)
    {
        return $this->insertSubjects($topic_id, $subject_identifiers, 0);
    }
    

    protected function insertSubjectLocators($topic_id, array $subject_locators)
    {
        return $this->insertSubjects($topic_id, $subject_locators, 1);
    }
    

    protected function insertSubjects($topic_id, array $subjects, $islocator)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;
        
        foreach ($subjects as $subject)
        {
            $values = [ ];

            $values[ ] =
            [
                'column' => 'subject_topic',
                'value' => $topic_id
            ];
        
            $values[ ] =
            [
                'column' => 'subject_value',
                'value' => $subject
            ];
        
            $values[ ] =
            [
                'column' => 'subject_islocator',
                'value' => intval($islocator),
                'datatype' => \PDO::PARAM_INT
            ];
        
            $sql = $this->services->db_utils->prepareInsertSql($this->services->topicmap->getUrl() . '_subject', $values);
        
            $ok = $sql->execute();
        
            if ($ok === false)
                return -1;
        }
        
        return 1;
    }
}
