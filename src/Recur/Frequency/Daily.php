<?php 
namespace Recur\Frequency;

/**
 * Daily frequency
 *
 * @author Marjana
 */
class Daily extends AbstractFrequency{
    
    function __construct($interval = 1){
        $this->interval = $interval;
    }
    
    /**
     * @return \DateInterval
     */
    function getFrequency(){
        return new \DateInterval("P1D");
    }
    
    /**
     * @return \DateInterval
     */
    function getOffset(){
        return new \DateInterval("P".$this->interval."D");
    }
    
    /**
     * @return String
     */
    function getConfigName()
    {
        return "DAILY";
    }
    
    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public function endOfInterval(\DateTime $date){
        $end = clone $date;
        $end->add($this->getFrequency());
        $end->setTime(0,0);
        return $end;
    }
    
    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public function begginigOfInterval(\DateTime $date)
    {
        $start = clone $date;
        $start->setTime(0,0);
        return $start;
    }
}
?>