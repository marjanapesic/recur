<?php
namespace Recur\Test;

use Recur\Recur;
use Recur\Exception\InvalidRuleConfig;
use Recur\Filter\Day as DayFilter;
use Recur\Filter\Hour as HourFilter;


class RecurTest extends \PHPUnit_Framework_TestCase {
    
    protected $recur;
    
    function setUp() 
    {
        $this->recur = new Recur(array('FREQ' => "DAILY", 'COUNT' => 5, 'BYDAY' => 'SA, SU', 'BYHOUR' => '0,23'));
    }
    
    function tearDown()
    {
        unset($this->recur);
    }
    
    function testGetConfigArray()
    {
        $result = $this->recur->getConfigArray();
        $expected = array('FREQ' => 'DAILY', "COUNT" => 5, 'BYDAY' => 'SA,SU', 'BYHOUR' => '0,23');
        $this->assertEquals($expected, $result);
    }
    
    /**
     * @expectedException \Recur\Exception\InvalidRuleConfig
     */
    function testInvalidRule(){
        $this->recur = new Recur(array('FREQ' => "DAILY", 'COUNT' => 0));
    }
    
    function testHasDayFilters()
    {    
        $this->assertTrue($this->recur->hasDayFilters());
        $this->recur =  new Recur(array('FREQ' => "DAILY", 'COUNT' => 5));
        $this->assertFalse($this->recur->hasDayFilters());
    }
    
    function testGetDayFilters()
    {
        $dayFilter = new DayFilter(false, false, false, false, false, true, true);
        $recurDayFilters = $this->recur->getDayFilters();
        
        $this->assertTrue(count($recurDayFilters) == 1);
        $this->assertEquals($dayFilter, $recurDayFilters[0]);
    }
    
    function testHasHourFilters()
    {
        $this->assertTrue($this->recur->hasHourFilters());
        $this->recur =  new Recur(array('FREQ' => "DAILY", 'COUNT' => 5, 'BYDAY' => 'SA'));
        $this->assertFalse($this->recur->hasHourFilters());        
    }
    
    
    function testGetHourFilters()
    {
        $hourFilter = new HourFilter(array(0,23));
        $recurHourFilters = $this->recur->getHourFilters();
    
        $this->assertTrue(count($recurHourFilters) == 1);
        $this->assertEquals($hourFilter, $recurHourFilters[0]);
    }
    
    function testGetUpperFilters()
    {
        $this->assertEquals(array(), $this->recur->getUpperFilters());
        $this->recur =  new Recur(array('FREQ' => "HOURLY", 'COUNT' => 5, 'BYDAY' => 'SA', 'BYHOUR' => '0,2,5'));
        $dayFilters = $this->recur->getDayFilters();
        $this->assertEquals(new DayFilter(false, false, false, false, false, true, false), $dayFilters[0]);
        
    }
    
    public function testGetLevelFilters()
    {
        $this->assertEquals(array(new DayFilter(false, false, false, false, false, true, true)), $this->recur->getLevelFilters());
        $this->recur =  new Recur(array('FREQ' => "WEEKLY", 'COUNT' => 5, 'BYDAY' => 'SA', 'BYHOUR' => '0,2,5'));
        $this->assertEquals(array(), $this->recur->getLevelFilters());
        $this->recur =  new Recur(array('FREQ' => "HOURLY", 'COUNT' => 5, 'BYDAY' => 'SA', 'BYHOUR' => '0,2,5'));
        $this->assertEquals($this->recur->getHourFilters(), $this->recur->getLevelFilters());
    }
    
    public function testGetLowerFilters()
    {
        $this->assertEquals(array(new HourFilter(array(0,23))), $this->recur->getLowerFilters());
        $this->recur = new Recur(array('FREQ' => "WEEKLY", 'COUNT' => 5, 'BYDAY' => 'SA', 'BYHOUR' => '0,2,5'));
        $this->assertEquals(
            array(
                new DayFilter(false, false, false, false, false, true, false),
                new HourFilter(array(0,2,5))
            ), 
            $this->recur->getLowerFilters()
        );
        $this->recur = new Recur(array('FREQ' => 'HOURLY', 'BYHOUR' => '2,5'));
        $this->assertEquals(array(), $this->recur->getLowerFilters());
    }
    
    
    public function testAddOffset()
    {
        $eventDate = new \DateTime('20150412T135325');
        $this->recur->addOffset($eventDate);
        $this->assertEquals(new \DateTime('20150413T135325'), $eventDate);
        
        $this->recur =  new Recur(array('FREQ' => "HOURLY", 'COUNT' => 5, 'BYDAY' => 'SA', 'BYHOUR' => '0,2,5'));
        $eventDate = new \DateTime('20150418T235325');
        $this->recur->addOffset($eventDate);
        $this->assertEquals(new \DateTime('20150425T005325'), $eventDate);
    }
    

    
}