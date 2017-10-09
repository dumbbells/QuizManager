var temp = window.location.search.substring(1);
var page = temp.split("=");

function reload() {
    MathJax.Hub.Queue(["Typeset", MathJax.Hub, "problems"]);
}

function undoDeletion() {
    if (confirm("Undo most recent deletion?")) {
        $("#problems").load("dataAccess.php",
                {
                    func: "undoDeletion"
                },
                reload
                );
    }
}

function Delete(element) {
    if (confirm("Delete this question?"))
    {
        alert(page.toString());
        var id = $(element).parents(".content").attr("id");
        $("#problems").load("dataAccess.php",
                {
                    page: page[1],
                    data: id,
                    func: "deleteProblem",
                },
                reload

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
            page: page[1],
            data: pid1 + " down",
            func: "swap",
        },
                reload);
    }
}

function MoveUp(element) {
    var controller = $(element).parents("div.controller");
    var pid1 = controller.find("div.content").attr("id");
    if (controller.prev('.controller')[0]) {
        var pid2 = controller.prev().find("div.content").attr("id");
        var current = controller.find("div.content").html();
        controller.find("div.content").html(controller.prev().find("div.content").html());
        controller.prev().find("div.content").html(current);
        $.post("dataAccess.php", {
            data: pid1 + " " + pid2,
            func: "swap"
        });
    } else {
        $("#problems").load("dataAccess.php", {
            page: page[1],
            data: pid1 + " up",
            func: "swap"
        },
                reload);
    }
}

function Edit(element) {
    var content = $(element).parents(".content");
    $(content).find("p").load("dataAccess.php", {
        data: {"id": content.attr("id"),
            "data": content.find("textArea").val()
        },
        func: "edit"
    }, function () {
        content.find("textArea").html(content.find("p").html());
        content.find("form").toggle();
        reload();
    });
    return false;
}