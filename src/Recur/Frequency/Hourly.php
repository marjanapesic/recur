<?php 
namespace Recur\Frequency;

/**
 * Hourly frequency
 *
 * @author Marjana
 */
class Hourly extends AbstractFrequency{
    
    function __construct($interval = 1){
        $this->interval = $interval;
    }
    
    /**
     * @return \DateInterval
     */
    function getFrequency(){
        return new \DateInterval("PT1H");
    }
    
    /**
     * @return \DateInterval
     */
    function getOffset(){
        return new \DateInterval("PT".$this->interval."H");
    }
    
    /**
     * @return String
     */
    function getConfigName()
    {
        return "HOURLY";
    }
    
    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public function endOfInterval(\DateTime $date){
        $end = clone $date;
        $end->add($this->getFrequency());
        $end->setTime($end->format('H'), 0);
        return $end;
    }
    
    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public function begginigOfInterval(\DateTime $date)
    {
        $start = clone $date;
        $start->setTime($start->format('H'), 0);
        return $start;
    }
}
?>