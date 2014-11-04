<?php

namespace TopicBank\Ui;

use \TopicBank\Interfaces\iTopic;


class Utils
{
    public static function getReifiesSummary(iTopic $topic)
    {
        $topicmap = $topic->getTopicMap();
        
        $result = '';

        $objects = $topic->getReifiedObject();
    
        if ($objects === false)
            return $result;
    
        $is_reifier = $topic->getIsReifier();
        
        if ($is_reifier === iTopic::REIFIES_NAME)
        {
            $result = sprintf
            (
                'Name “%s” of <a href="%stopic/%s">%s</a>',
                htmlspecialchars($objects[ 'name' ]->getValue()),
                TOPICBANK_BASE_URL,
                htmlspecialchars(urlencode($objects[ 'topic' ]->getId())),
                htmlspecialchars($topicmap->getTopicLabel($objects[ 'topic' ]->getId()))
            );
        }
        elseif ($is_reifier === iTopic::REIFIES_OCCURRENCE)
        {
            $result = sprintf
            (
                'Property “%s: %s” of <a href="%stopic/%s">%s</a>',
                htmlspecialchars($topicmap->getTopicLabel($objects[ 'occurrence' ]->getType())),
                htmlspecialchars($objects[ 'occurrence' ]->getValue()),
                TOPICBANK_BASE_URL,
                htmlspecialchars(urlencode($objects[ 'topic' ]->getId())),
                htmlspecialchars($topicmap->getTopicLabel($objects[ 'topic' ]->getId()))
            );
        }
        elseif ($is_reifier === iTopic::REIFIES_ASSOCIATION)
        {
            $players = [ ];
        
            foreach ($objects[ 'association' ]->getRoles() as $role)
            {
                $players[ ] = sprintf
                (
                    '<a href="%stopic/%s">%s</a>',
                    TOPICBANK_BASE_URL,
                    htmlspecialchars(urlencode($role->getPlayer())),
                    htmlspecialchars($topicmap->getTopicLabel($role->getPlayer()))
                );
            }
            
            $result = sprintf
            (
                'A “%s” association between %s',
                htmlspecialchars($topicmap->getTopicLabel($objects[ 'association' ]->getType())),
                implode(' and ', $players)
            );
        }
        elseif ($is_reifier === iTopic::REIFIES_ROLE)
        {
            $other_players = [ ];
        
            foreach ($objects[ 'association' ]->getRoles() as $role)
            {
                if ($role->getPlayer() === $objects[ 'role' ]->getPlayer())
                    continue;
                
                $other_players[ ] = sprintf
                (
                    '<a href="%stopic/%s">%s</a>',
                    TOPICBANK_BASE_URL,
                    htmlspecialchars(urlencode($role->getPlayer())),
                    htmlspecialchars($topicmap->getTopicLabel($role->getPlayer()))
                );
            }
            
            $result = sprintf
            (
                'Role “%s: <a href="%stopic/%s">%s</a>” in a “%s” association with %s',
                htmlspecialchars($topicmap->getTopicLabel($objects[ 'role' ]->getType())),
                TOPICBANK_BASE_URL,
                htmlspecialchars($objects[ 'role' ]->getPlayer()),
                htmlspecialchars($topicmap->getTopicLabel($objects[ 'role' ]->getPlayer())),
                htmlspecialchars($topicmap->getTopicLabel($objects[ 'association' ]->getType())),
                implode(' and ', $other_players)
            );
        }
        
        return $result;
    }
}
