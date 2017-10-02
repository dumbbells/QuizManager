function undoDeletion() {
    if (confirm("Undo most recent deletion?")) {
        $("#problems").load("dataAccess.php",
                {
                    func: "undoDeletion"
                }
        );
    }
}

function Delete(element) {
    if (confirm("Delete this question?"))
    {
        var id = $(element).parents(".content").attr("id");
        $("#problems").load("dataAccess.php",
                {
                    data: id,
                    func: "deleteProblem",
                }

        );

    }
}

function MoveDown(element) {
    var controller = $(element).parents("div.controller");
    var pid1 = controller.find("div.content").attr("id");
    if (controller.next('.controller')[0]) {
        var pid2 = controller.next().find("div.content").attr("id");
        var current = controller.find("div.content").html();
        controller.find("div.content").html(controller.next().find("div.content").html());
        controller.next().find("div.content").html(current);
        $.post("dataAccess.php", {
            data: pid1 + " " + pid2,
            func: "swap"
        });

    } else {
        $("#problems").load("dataAccess.php", {
            data: pid1 + " down",
            func: "swap"
        });
    }
}

function MoveUp(element) {
    var controller = $(element).parents("div.controller");
    var pid1 = controller.find("div.content").attr("id");
    if (controller.prev('.controller')[0]) {
        var pid2 = controller.next().find("div.content").attr("id");
        var current = controller.find("div.content").html();
        controller.find("div.content").html(controller.prev().find("div.content").html());
        controller.prev().find("div.content").html(current);

        //post goes here
    } else {
        console.log("not on screen");
    }
}

function Edit(element) {
    var content = $(element).parents(".content");
    $(content).find("p").load("dataAccess.php", {
        data: {"id": content.attr("id"),
            "data": content.find("textArea").val()
        },
        func: "edit"
    }, function(){
        content.find("textArea").html(content.find("p").html());
        content.find("form").toggle();
    });
    return false;
}