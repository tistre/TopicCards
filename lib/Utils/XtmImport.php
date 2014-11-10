<?php

namespace TopicBank\Utils;


class XtmImport
{
    public static function importObjects($xml, \TopicBank\Interfaces\iTopicMap $topicmap)
    {
        $dom = new \DOMDocument();
        
        $ok = $dom->loadXML($xml);
        
        if ($ok === false)
            return false;

        $result = [ ];
        
        foreach ($dom->documentElement->childNodes as $node)
        {
            if ($node->nodeType != XML_ELEMENT_NODE)
                continue;
                
            if ($node->tagName === 'topic')
            {
                $result[ ] = self::importTopic($node, $topicmap);
            }
            elseif ($node->tagName === 'association')
            {
                $result[ ] = self::importAssociation($node, $topicmap);
            }
        }
        
        return $result;
    }
    
    
    protected static function importTopic(\DOMElement $context_node, \TopicBank\Interfaces\iTopicMap $topicmap)
    {
        $topic = $topicmap->newTopic();

        if ($context_node->hasAttribute('id'))
            $topic->setId($context_node->getAttribute('id'));

        self::importTypes($context_node, $topic);
        self::importSubjectIdentifiers($context_node, $topic);
        self::importSubjectLocators($context_node, $topic);
        self::importNames($context_node, $topic);
        self::importOccurrences($context_node, $topic);
        
        return $topic;
    }
    
    
    protected static function importAssociation(\DOMElement $context_node, \TopicBank\Interfaces\iTopicMap $topicmap)
    {
        $association = $topicmap->newAssociation();

        if ($context_node->hasAttribute('id'))
            $association->setId($context_node->getAttribute('id'));

        if ($context_node->hasAttribute('reifier'))
            $association->setReifier($context_node->getAttribute('reifier'));

        $association->setType(self::getType($context_node));
        $association->setScope(self::getScope($context_node));

        self::importRoles($context_node, $association);
        
        return $association;
    }
    
    
    protected static function importTypes(\DOMElement $context_node, \TopicBank\Interfaces\iTopic $topic)
    {
        $topic_refs = [ ];
        
        foreach ($context_node->getElementsByTagName('instanceOf') as $node)
        {
            $topic_ref = self::getTopicRef($node);
            
            if (strlen($topic_ref) > 0)
                $topic_refs[ ] = $topic_ref;
        }
        
        $topic->setTypes($topic_refs);
    }
    
    
    protected static function importSubjectIdentifiers(\DOMElement $context_node, \TopicBank\Interfaces\iTopic $topic)
    {
        self::importSubjects('subjectIdentifier', $context_node, $topic);
    }
    
    
    protected static function importSubjectLocators(\DOMElement $context_node, \TopicBank\Interfaces\iTopic $topic)
    {
        self::importSubjects('subjectLocator', $context_node, $topic);
    }
    
    
    protected static function importSubjects($what, \DOMElement $context_node, \TopicBank\Interfaces\iTopic $topic)
    {
        $hrefs = [ ];
        
        foreach ($context_node->getElementsByTagName($what) as $node)
        {
            if (! $node->hasAttribute('href'))
                continue;
                
            $hrefs[ ] = $node->getAttribute('href');
        }
        
        $method = sprintf('set%ss', $what);
        
        $topic->$method($hrefs);
    }


    protected static function importNames(\DOMElement $context_node, \TopicBank\Interfaces\iTopic $topic)
    {
        $names = [ ];
        
        foreach ($context_node->getElementsByTagName('name') as $node)
        {
            $name = $topic->newName();
            
            $name->setType(self::getType($node));
            $name->setScope(self::getScope($node));
            
            foreach ($node->getElementsByTagName('value') as $subnode)
                $name->setValue($subnode->nodeValue);

            if ($node->hasAttribute('reifier'))
                $name->setReifier($node->getAttribute('reifier'));
                
            $names[ ] = $name;
        }
        
        $topic->setNames($names);
    }
    
    
    protected static function importOccurrences(\DOMElement $context_node, \TopicBank\Interfaces\iTopic $topic)
    {
        $occurrences = [ ];
        
        foreach ($context_node->getElementsByTagName('occurrence') as $node)
        {
            $occurrence = $topic->newOccurrence();
            
            $occurrence->setType(self::getType($node));
            $occurrence->setScope(self::getScope($node));
            
            foreach ($node->getElementsByTagName('resourceData') as $subnode)
            {
                // XXX support inline XML?
                $occurrence->setValue($subnode->nodeValue);
                
                $occurrence->setDataType
                (
                    $topic->getTopicMap()->getTopicBySubjectIdentifier($subnode->getAttribute('datatype'))
                );
            }
                
            if ($node->hasAttribute('reifier'))
                $occurrence->setReifier($node->getAttribute('reifier'));
                
            $occurrences[ ] = $occurrence;
        }
        
        $topic->setOccurrences($occurrences);
    }
    
    
    protected static function importRoles(\DOMElement $context_node, \TopicBank\Interfaces\iAssociation $association)
    {
        $roles = [ ];
        
        foreach ($context_node->getElementsByTagName('role') as $node)
        {
            $role = $association->newRole();
            
            $role->setType(self::getType($node));
            $role->setPlayer(self::getTopicRef($node));
            
            if ($node->hasAttribute('reifier'))
                $role->setReifier($node->getAttribute('reifier'));
                
            $roles[ ] = $role;
        }
        
        $association->setRoles($roles);
    }
    
    
    protected static function getType(\DOMElement $node)
    {
        foreach ($node->getElementsByTagName('type') as $subnode)
            return self::getTopicRef($subnode);
        
        return false;
    }


    protected static function getScope(\DOMElement $node)
    {
        $result = [ ];
        
        foreach ($node->getElementsByTagName('scope') as $subnode)
        {
            $scope = self::getTopicRef($subnode);
            
            if (strlen($scope) > 0)
                $result[ ] = $scope;
        }
        
        return $result;
    }


    protected static function getTopicRef(\DOMElement $node)
    {
        foreach ($node->childNodes as $subnode)
        {
            if ($subnode->nodeType != XML_ELEMENT_NODE)
                continue;
                
            if ($subnode->tagName !== 'topicRef')
                continue;

            if (! $subnode->hasAttribute('href'))
                continue;
    
            return $subnode->getAttribute('href');
        }
        
        return false;
    }
}
