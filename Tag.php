<?php
class Tag {
    var $keyword;
    var $count;

    function __construct($keyword, $count) {
        $this->keyword = $keyword;
        $this->count = $count;
    }

    function printTag(){
?>
    <span style="background: silver; border: 1px solid black; padding: 2px;"><?= $this->keyword ?> 
            <span class="glyphicon glyphicon-remove-sign" onclick="alert('clicked');"></span>
        </span>
<?php  
    }
}
