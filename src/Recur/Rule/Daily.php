<?php 

namespace Recur\Rule;

use Recur\Recur;

/**
 * Recur type with daily repetion
 *
 * @author Marjana
 */
class Daily extends Recur {

    public function __construct(){
        parent::__construct(array("FREQ" => "DAILY"));
    }
}
?>