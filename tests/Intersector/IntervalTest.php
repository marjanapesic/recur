<?php
namespace Recur\Test;

use Recur\Recur;
use Recur\Event\Event;
use Recur\Intersector\Interval;

class IntervalTest extends \PHPUnit_Framework_TestCase {

    protected $interval;
    
    function setUp()
    {
        $this->interval = new Interval();
    }
    
    function testInterval(){
        $recur = new Recur(array('FREQ' => 'DAILY'));
        $event = new Event(new \DateTime('20150421T161215'), $recur);
        
        $expected = array(new \DateTime('20150421T161215'), new \DateTime('20150422T161215'), new \DateTime('20150423T161215'));
        $result = $this->interval->intersect($event, new \DateTime('20150421T121215'), new \DateTime('20150423T211215'));
        $this->assertEquals($expected, $result);
        
        $event = new Event(new \DateTime('20150421T161215'), $recur);
        $expected = array();
        $result = $this->interval->intersect($event, new \DateTime('20150421T101215'), new \DateTime('20150421T121215'));
        $this->assertEquals($expected, $result);
        
        
        $recur = new Recur(array('FREQ' => 'DAILY', 'INTERVAL' => '2'));
        $event = new Event(new \DateTime('20150421T161215'), $recur);
        
        $expected = array(new \DateTime('20150421T161215'), new \DateTime('20150423T161215'), new \DateTime('20150425T161215'));
        $result = $this->interval->intersect($event, new \DateTime('20150421T121215'), new \DateTime('20150426T211215'));
        $this->assertEquals($expected, $result);
        
        
        $recur = new Recur(array('FREQ' => 'DAILY', 'INTERVAL' => '2', 'BYDAY' => 'SA, SU'));
        $event = new Event(new \DateTime('20150421T161215'), $recur);
        
        $expected = array(new \DateTime('20150425T161215'), new \DateTime('20150503T161215'));
        $result = $this->interval->intersect($event, new \DateTime('20150421T121215'), new \DateTime('20150504T211215'));
        $this->assertEquals($expected, $result);
        
        
        $recur = new Recur(array('FREQ' => 'DAILY', 'COUNT'=> '2', 'INTERVAL' => '2', 'BYDAY' => 'SA, SU', 'BYHOUR' => '12, 21'));
        $event = new Event(new \DateTime('20150421T161215'), $recur);
        
        $expected = array(new \DateTime('20150425T121215'), new \DateTime('20150425T211215'));
        $result = $this->interval->intersect($event, new \DateTime('20150421T121215'), new \DateTime('20150425T211215'));
        $this->assertEquals($expected, $result);
        
        $recur = new Recur(array('FREQ' => 'WEEKLY', 'COUNT'=> '2', 'INTERVAL' => '2'));
        $event = new Event(new \DateTime('20150421T161215'), $recur);
        
        $expected = array(new \DateTime('20150421T161215'), new \DateTime('20150505T161215'));
        $result = $this->interval->intersect($event, new \DateTime('20150421T121215'), new \DateTime('20150525T211215'));
        $this->assertEquals($expected, $result);
        
        
        
        $recur = new Recur(array('FREQ' => 'WEEKLY', 'UNTIL'=> new \DateTime('20150512T120005'), 'INTERVAL' => '2'));
        $event = new Event(new \DateTime('20150421T161215'), $recur);
        
        $expected = array(new \DateTime('20150421T161215'), new \DateTime('20150505T161215'));
        $result = $this->interval->intersect($event, new \DateTime('20150421T121215'), new \DateTime('20150525T211215'));
        $this->assertEquals($expected, $result);
        
        
        $recur = new Recur(array('FREQ' => 'WEEKLY', 'UNTIL'=> new \DateTime('20150512T120005'), 'INTERVAL' => '2', 'BYDAY' => 'SA', 'BYHOUR'=>'12'));
        $event = new Event(new \DateTime('20150421T161215'), $recur);
        
        $expected = array(new \DateTime('20150425T121215'), new \DateTime('20150509T121215'));
        $result = $this->interval->intersect($event, new \DateTime('20150417T121215'), new \DateTime('20150528T211215'));
        $this->assertEquals($expected, $result);
        
        
        $recur = new Recur(array('FREQ' => 'DAILY', 'UNTIL'=> new \DateTime('20150423T020005'), 'INTERVAL' => '2', 'BYHOUR' => '0,1,23'));
        $event = new Event(new \DateTime('20150421T231215'), $recur);
        
        $expected = array(new \DateTime('20150421T231215'), new \DateTime('20150423T001215'), new \DateTime('20150423T011215'));
        $result = $this->interval->intersect($event, new \DateTime('20150421T121215'), new \DateTime('20150525T211215'));
        $this->assertEquals($expected, $result);
        
        
        $recur = new Recur(array('FREQ' => 'HOURLY', 'UNTIL'=> new \DateTime('20150423T020005'), 'INTERVAL' => '2', 'BYHOUR' => '0,1,23'));
        $event = new Event(new \DateTime('20150421T221215'), $recur);
        
        $expected = array(new \DateTime('20150422T001215'), new \DateTime('20150423T001215'));
        $result = $this->interval->intersect($event, new \DateTime('20150421T121215'), new \DateTime('20150525T211215'));
        $this->assertEquals($expected, $result);
        
        
        $recur = new Recur(array('FREQ' => 'HOURLY', 'UNTIL'=> new \DateTime('20150423T020005'), 'INTERVAL' => '10', 'BYDAY' => 'SA'));
        $event = new Event(new \DateTime('20150417T221215'), $recur);
        
        $expected = array(new \DateTime('20150418T081215'), new \DateTime('20150418T181215'));
        $result = $this->interval->intersect($event, new \DateTime('20150417T121215'), new \DateTime('20150525T211215'));
        $this->assertEquals($expected, $result);
        
        
        $expected = array(new \DateTime('20150418T081215'));
        $result = $this->interval->intersect($event, new \DateTime('20150417T121215'), new \DateTime('20150418T081215'));
        $this->assertEquals($expected, $result);
        
        $expected = array();
        $result = $this->interval->intersect($event, new \DateTime('20150417T121215'), new \DateTime('20150418T071215'));
        $this->assertEquals($expected, $result);
    }
}