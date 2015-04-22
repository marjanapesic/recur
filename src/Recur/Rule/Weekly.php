<?php 

namespace Recur\Rule;

use Recur\Recur;

/**
 * Recur type with weekly repetion
 *
 * @author Marjana
 */
class Weekly extends Recur {

    public function __construct(){
        parent::__construct(array("FREQ" => "WEEKLY"));
    }
}
?>