<?php 
namespace Recur\Frequency;

/**
 * Weekly frequency
 *
 * @author Marjana
 */
class Weekly extends AbstractFrequency{
    
    protected $begginingOfWeek = 'monday';
    
    function __construct($interval = 1){
        $this->interval = $interval;
    }
    
    /**
     * @return \DateInterval
     */
    function getFrequency(){
        return new \DateInterval("P7D");
    }
    
    /**
     * @return \DateInterval
     */
    function getOffset(){
        return new \DateInterval("P".($this->interval*7)."D");
    }
    
    /**
     * @return String
     */
    function getConfigName()
    {
        return "WEEKLY";
    }
    
    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public function endOfInterval(\DateTime $date){
        $end = clone $date;
        
        $end->modify('next '.($this->begginingOfWeek));
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
        $start->modify('next '.($this->begginingOfWeek));
        $start->setTime(0,0);
        $start->sub($this->getFrequency());
        return $start;
    }
}
?>