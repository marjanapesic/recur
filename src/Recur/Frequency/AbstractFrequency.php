<?php 
namespace Recur\Frequency;

/**
 * Abstract frequency
 *
 * @author Marjana
 */
abstract class AbstractFrequency{
    
    /**
     * @var int
     */
    protected $interval = 1;

    abstract function getFrequency();
    
    abstract function getOffset();
    
    abstract function getConfigName();
    
    abstract function begginigOfInterval(\DateTime $date);
    
    abstract function endOfInterval(\DateTime $date);
    
    /**
     * @return int
     */
    function getInterval(){
        return $this->interval;
    }
    
    /**
     * @param int $interval
     * @return void
     */
    public function setInterval($interval){
        $this->interval = $interval;
    }
}
?>