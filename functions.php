<?php
function pagination($pages, $currentPage)
{
    echo "<div class='text-center'><ul class='pagination'>";
    if ($currentPage > 1) {
        echo "<li><a href='http://localhost/Project1/index.php?page="
        . ($currentPage - 1)
        . "'><span class='glyphicon glyphicon-menu-left'></span></a></li>";
    }
    for ($x = 1; $x <= $pages; $x++) {
        if ($x == $currentPage) {
            echo "<li class='disabled'><a href='' style='pointer-events: none;'>";
        }
        else {
            echo "<li><a href='http://localhost/Project1/index.php?page=$x'>";
        }
        echo "$x</a></li>";
    }
    if ($currentPage < $pages) {
        echo "<li><a href='http://localhost/Project1/index.php?page="
        . ($currentPage + 1)
        . "'><span class='glyphicon glyphicon-menu-right'></span></a></li>";
    }
    echo "</ul></div>";
}

function renderProblems($pages, $firstOnPage, $result)
{
    ?>
    <div class = 'container' id = 'problems'>
        <?php
        $x = $firstOnPage;
        pagination($pages, ($x / 20) + 1);
        while ($row = $result->fetch_assoc()) {
            $x++;
            ?>
            <div class='row controller' style="border-bottom: 1px solid #ccc; min-height: 100px;">
                <div class="col-sm-1"><h4><?php echo $x; ?></h4></div>
                <div class="content" id="<?php echo $row["pid"]; ?>">
                    <div class='col-sm-7'>
                        <p><?php echo $row["content"]; ?></p>
                        <form style="display:none" onsubmit="return Edit(this);">
                            <textarea name="Text1" rows="5" style="min-width: 100%"><?php echo $row["content"]; ?></textarea>
                            <br />
                            <input class="btn btn-info pull-right" type="submit" value="Save changes">
                        </form>
                        <?php loadTags($row["keywords"]); ?>
                    </div>
                    <div class='col-sm-4'>
                        <div class='btn-group pull-right'>
                            <button class='btn btn-info' onclick="$(this).parentsUntil('div.controller').find('form').toggle();">
                                <span class="glyphicon glyphicon-font"></span>
                            </button>
                            <button class="btn btn-info" onclick="Delete(this);">
                                <span class="glyphicon glyphicon-minus"></span>
                            </button>
                            <button class="btn btn-info" onclick="MoveUp(this);">
                                <span class="glyphicon glyphicon-chevron-up"></span>
                            </button>
                            <button class="btn btn-info" onclick="MoveDown(this);">
                                <span class="glyphicon glyphicon-chevron-down"></span>
                            </button>
                            <button class="btn btn-info" onclick="ToggleTagEntry(this);">
                                <span class="glyphicon glyphicon-plus"></span>
                            </button>
                        </div>
                        <div class="pull-right tagEntry" style="display:none;">
                            <span><input type="text" style="width:176px;"> 
                                <span class="glyphicon glyphicon-check" onclick="AddNewTag(this);"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div><?php } ?>
        <div><?php pagination($pages, ($firstOnPage / 20) + 1); ?></div>
    </div> 
    <?php
}
