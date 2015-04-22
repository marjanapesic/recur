<?php 
namespace Recur\Event;

/**
 * Event object represents a recurring event. 
 *
 * @author Marjana
 */
class Event {

    /**
     *
     * @var DateTime start of event
     */
    protected $startDate;
    
    /**
     *
     * @var \Recur\Recur as recurring resource
     */
    protected $recur;
    
    /**
     *
     * @var Array of \DateInterval pattern
     * 
     * It contains array of \DateInterval objects which represents pattern of occurences.
     * Date intervals added to beggining of repetion interval form
     * occurrences of repetitive event.
     */
    protected $pattern;
    
    
    public function __construct(\DateTime $startDate, \Recur\Recur $recur)
    {
        $this->startDate = clone $startDate;
        $this->recur = $recur;
    }
    
    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }
    
    /**
     * @return Recur
     */
    public function getRecur()
    {
        return $this->recur;
    }
    
    /**
     * @return Array of DateInterval
     */
    public function getPattern()
    {
        if(!$this->pattern)
            $this->pattern = PatternGenerator::generate($this->startDate, $this->recur);   
        
        return $this->pattern;
    }
}
?>