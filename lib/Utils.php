<?php

namespace TopicBankUi;

use TopicCards\Interfaces\iTopic;


class Utils
{
    public static function getReifiesSummary(iTopic $topic)
    {
        $topicmap = $topic->getTopicMap();
        
        $result = '';

        $is_reifier = $topic->isReifier($reifies_what, $dummy);
        
        if (! $is_reifier)
            return $result;
        
        $objects = $topic->getReifiedObject($reifies_what);
    
        if ($objects === false)
            return $result;
    
        if ($reifies_what === iTopic::REIFIES_NAME)
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
        elseif ($reifies_what === iTopic::REIFIES_OCCURRENCE)
        {
            $result = sprintf
            (
                'Property “%s: %s” of <a href="%stopic/%s">%s</a>',
                htmlspecialchars($topicmap->getTopicLabel($objects[ 'occurrence' ]->getTypeId())),
                htmlspecialchars($objects[ 'occurrence' ]->getValue()),
                TOPICBANK_BASE_URL,
                htmlspecialchars(urlencode($objects[ 'topic' ]->getId())),
                htmlspecialchars($topicmap->getTopicLabel($objects[ 'topic' ]->getId()))
            );
        }
        elseif ($reifies_what === iTopic::REIFIES_ASSOCIATION)
        {
            $players = [ ];
        
            foreach ($objects[ 'association' ]->getRoles() as $role)
            {
                $players[ ] = sprintf
                (
                    '<a href="%stopic/%s">%s</a>',
                    TOPICBANK_BASE_URL,
                    htmlspecialchars(urlencode($role->getPlayerId())),
                    htmlspecialchars($topicmap->getTopicLabel($role->getPlayerId()))
                );
            }
            
            $result = sprintf
            (
                'A “%s” association between %s',
                htmlspecialchars($topicmap->getTopicLabel($objects[ 'association' ]->getTypeId())),
                implode(' and ', $players)
            );
        }
        elseif ($reifies_what === iTopic::REIFIES_ROLE)
        {
            $other_players = [ ];
        
            foreach ($objects[ 'association' ]->getRoles() as $role)
            {
                if ($role->getPlayerId() === $objects[ 'role' ]->getPlayerId())
                    continue;
                
                $other_players[ ] = sprintf
                (
                    '<a href="%stopic/%s">%s</a>',
                    TOPICBANK_BASE_URL,
                    htmlspecialchars(urlencode($role->getPlayerId())),
                    htmlspecialchars($topicmap->getTopicLabel($role->getPlayerId()))
                );
            }
            
            $result = sprintf
            (
                'Role “%s: <a href="%stopic/%s">%s</a>” in a “%s” association with %s',
                htmlspecialchars($topicmap->getTopicLabel($objects[ 'role' ]->getTypeId())),
                TOPICBANK_BASE_URL,
                htmlspecialchars($objects[ 'role' ]->getPlayerId()),
                htmlspecialchars($topicmap->getTopicLabel($objects[ 'role' ]->getPlayerId())),
                htmlspecialchars($topicmap->getTopicLabel($objects[ 'association' ]->getTypeId())),
                implode(' and ', $other_players)
            );
        }
        
        return $result;
    }
}
