var temp = window.location.search.substring(1);
var page = temp.split("=");

function Search() {
    $("#problems").load("dataAccess.php",
            {
                func: "loadProblems",
                page: page[1],
                data: $("#searchBar").val()

            });
}

function RemoveTag(element) {
    $.ajax({
        url: "dataAccess.php",
        type: "get",
        data: {
            func: "removeTag",
            data: {"tagId": $(element).attr("id"), "problem": $(element).closest("div.content").attr("id")}
        },
        success: function (response) {
            $(element).parent("span").remove();
        },
        error: function (xhr) {
            alert("Something went wrong.");
        }
    });
}

function AddNewTag(element) {
    var newTag = $(element).prev("input").val();
    $.ajax({
        url: "dataAccess.php",
        type: "get",
        data: {
            func: "AddNewTag",
            data: {"newTag": newTag, "problem": $(element).closest("div.content").attr("id")}
        },
        success: function (response) {
            if (response == -1) {
                alert("Can not repeat tags");
                return;
            }
            $(element).closest("div.col-sm-4").prev("div").append(response);
        },
        error: function (xhr) {
            alert("it didn't work :(");
        }
    });
}

function ToggleTagEntry(element) {
    var tagEntry = $(element).closest("div.col-sm-4").find("div.tagEntry");
    tagEntry.toggle();
    $(tagEntry).find("input").focus();
}

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
