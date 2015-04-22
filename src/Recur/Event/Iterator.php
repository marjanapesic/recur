<?php
namespace Recur\Event;

/**
 * Iterator of recurring events
 *
 * @author Marjana
 */
class Iterator implements \Iterator
{

    /**
     * @var Recur\Event\Event
     */
    protected $event;

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var Array of buffered dates of occurences of event
     */
    protected $events = array();

    /**
     * @var int count of deleted events from events array
     */
    protected $eventsDeleted = 0;

    
    public function __construct(Event $event)
    {
        $this->event = $event;
        $this->postion = 0;
    }

    
    /**
     * @return DateTime of current occurence of event
     */
    public function current()
    {
        return $this->events[$this->position - $this->eventsDeleted];
    }

    /**
     * @return int current position
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @return void
     */
    public function next()
    {
       
        if (! isset($this->events[$this->position - $this->eventsDeleted + 1])) {
          
            $eventsCount = count($this->events);
            $this->events = $this->getInstances($this->current());

            unset($this->events[0]);
            $this->events = array_values($this->events);
            
            $this->eventsDeleted += $eventsCount;
        }
        
        $this->position ++;
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->position = 0;
        $this->events = $this->getInstances(clone $this->event->getStartDate());
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return isset($this->events[$this->position - $this->eventsDeleted]);
    }

    
    /**
     * @return boolean if ocurrence is in limits defined in Recur 
     */
    public function inLimit($date, $counter = 0)
    {
        if ($this->event->getRecur()->getUntil()) {
            return $date <= $this->event->getRecur()->getUntil();
        }
        if ($this->event->getRecur()->getCount()) {
            return (($this->position + $counter) <= $this->event->getRecur()->getCount());
        }
        return true;
    }

    /**
     * @return Array of dates of event occurences. Default limit is 10 occurences. 
     */
    public function getInstances(\DateTime $fromDate, $countLimit = 10)
    {
        $counter = 0;
        $eventList = array();
              
        if(($upperFilters = $this->event->getRecur()->getUpperFilters()))
        {
            $passedUpperFilters = false;
            while(!$passedUpperFilters)
            {
                $passedUpperFilters = true;
                foreach($upperFilters as $upperFilter){
                    $passedUpperFilters &= $upperFilter->filter($fromDate);
                    if(!$passedUpperFilters) break;
                }
                if(!$passedUpperFilters)
                    $this->event->getRecur()->addOffset($fromDate);
            }
        }
  
        $date = clone $this->event->getRecur()->getFreq()->begginigOfInterval($fromDate);
        
        //iterate from beggining of interval, with step defined in frequency, until ocurrences are in limit of recur and countLimit is reached
        //step is calculated depending on upper filters
        for ($date; $this->inLimit($date, $counter + 1) && $counter < $countLimit; $this->event->getRecur()->addOffset($date)) {
            
            $passedLevelFilters = true;
 
            foreach ($this->event->getRecur()->getLevelFilters() as $filter) 
            {
                $passedLevelFilters &= $filter->filter($date);
                if(!$passedLevelFilters) break;
            }
           
            
            if ($passedLevelFilters)
            {
                if ($this->event->getRecur()->getLowerFilters()) 
                {
                    $patterns = $this->event->getPattern();
                    foreach ($patterns as $pattern) {
                        $newOccurrence = clone $date;
                        $newOccurrence->add($pattern);
                        if ($this->inLimit($newOccurrence, $counter + 1) && $newOccurrence >= $fromDate) {
                            $counter ++;
                            $eventList[] = clone $newOccurrence;
                        }
                    }
                } 

                else 
                {    
                    $newOccurrence = clone $date;
                    $patterns = $this->event->getPattern();
                    $counter ++;
                    $eventList[] = $newOccurrence->add($patterns[0]);
                }
            }  
            
        }
  
        return $eventList;
    }
}

?>