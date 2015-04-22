<?php 
namespace Recur\Intersector;

use Recur\Event\Event;

/**
 * Interval Intersector
 *
 * @author Marjana
 */
class Interval {
    
    /**
     * @param \Recur\Event\Event $event
     * @param \DateTime $interval_start
     * @param \DateTime $interval_end
     * 
     * @return Array of \DateTime objects
     */
    public function intersect(Event $event, \DateTime $interval_start, \DateTime $interval_end)
    {
        
        if($interval_start > $interval_end || $interval_end < $event->getStartDate() || ($event->getRecur()->getUntil() && $event->getRecur()->getUntil() < $interval_start))
        {
            return array();
        }
        
        $eventList = array();
        $iterator = new \Recur\Event\Iterator($event);
        
        foreach($iterator as $occurrence)
        {   
            if($occurrence < $interval_start) continue;
            if($occurrence <= $interval_end){
                $eventList[] = clone $occurrence;
                continue;
            }
            break;
        }
        return $eventList;
    }  
}
?>