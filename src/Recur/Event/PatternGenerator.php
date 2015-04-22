<?php 

namespace Recur\Event;

/**
 * Class responsible for pattern creation.
 *
 * @author Marjana
 */
class PatternGenerator
{
    
    public static function generate(\DateTime $startDate, \Recur\Recur $recur){
        
        
        $begginingOfInterval = $recur->getFreq()->begginigOfInterval($startDate);
        $defaultOffset = $begginingOfInterval->diff($startDate);
        
        //if recur doesn't have lower filters return default offset
        if(!($lowerFilters = $recur->getLowerFilters()))
            return array($defaultOffset);

        $dayOffsets = array();
        $hourOffsets = array();
        $result = array();
        
        foreach($lowerFilters as $filter)
        {
            //filters that are on day level  
            if($filter instanceof \Recur\Filter\Day)
            {        
                $day = clone $begginingOfInterval;
                
                for($i=0; $i<7; $i++, $day->add(new \DateInterval('P1D')))
                {
                    if($filter->filter($day))
                    {
                        $interval = $day->diff($begginingOfInterval);
                        $dayOffsets[] = $interval->d;
                    }
                }
            }
            
            //filters that are on hour level
            if($filter instanceof \Recur\Filter\Hour){
                $hourOffsets = $filter->getHours();
            }
        }

        $result_holder = array();
        
        foreach ($hourOffsets as $hourOffset) 
        {
            $result_holder[] = new \DateInterval('P' . $defaultOffset->d . 'DT' . $hourOffset . "H" . $defaultOffset->i . "M" . $defaultOffset->s . "S");
        }
        
   
        if(!$dayOffsets)
            return $result_holder;
        
        foreach ($dayOffsets as $dO) 
        {
            if ($result_holder) 
            {
                foreach ($result_holder as $r)
                {
                    $result[] = new \DateInterval('P' . $dO . 'DT' . $r->h . "H" . $r->i . "M" . $r->s . "S");
                }
            } 
            else 
            {
                $result[] = new \DateInterval('P' . $dO . 'DT' . $defaultOffset->h . "H" . $defaultOffset->i . "M" . $defaultOffset->s . "S");
            }
        }
  
        return $result;
    }
}
?>