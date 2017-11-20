<?php
class Tag {
    var $keyword;
    var $count;
    var $id;

    function __construct($keyword, $count, $id) {
        $this->keyword = $keyword;
        $this->count = $count;
        $this->id = $id;
    }

    function printTag(){
?>
    <span style="background: silver; border: 1px solid black; padding: 2px;"><?= $this->keyword ?> 
            <span id='<?php echo $this->id; ?>' class="glyphicon glyphicon-remove-sign" onclick="RemoveTag(this);"></span>
        </span>
<?php  
    }
}
