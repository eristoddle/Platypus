$(function() {
    if ($("#gRank-score").val()) {
        $("#gRank-display-status").html('Complete. Score: ' + $("#gRank-score").val());
        $("#gRankShow").removeClass("btn-warning").html("Re-take gRank Survey");
    } else {
        $("#gRank-display-status").html('Incomplete');
        $("#gRankShow").addClass("btn-warning").html("Complete the gRank Survey");
    }

    $(".gRankModal").each(function () {
        $('body').append($(this));
    });

    $(".gRankModal").modal({
        "keyboard": true,
        "show": false
    });

    $("#gRankContainer").delegate("a#gRankShow", "click", function() {
        $("#gRankStepZero").modal("show");
        return false;
    });

    // Initial Step
    $("#gRankStepZero").delegate("a#s0-next", "click", function() {
        $("#gRankStepZero").modal("hide");
        $("#gRankStepOne").modal("show");
    });

    // Step One (Experience)
    $("#gRankStepOne").delegate("input[type=radio]", "click", function(){
        $("#s1-next").show("puff");
        $("#gRankStepTwo").data('activeSection', this.value);
    });

    $("#gRankStepOne").delegate("a#s1-next", "click", function() {
        $("#gRankStepOne").modal("hide");
        $("#gRankStepTwo").modal("show");
        $("#stepTwoQuestions-" + $("#gRankStepTwo").data('activeSection') ).show();
    });

    $("#gRankStepOne").delegate("a#s1-back", "click", function() {
        $("#gRankStepOne").modal("hide");
        $("#gRankStepZero").modal("show");
    });

    $('#gRankStepOne').on('hidden', function () {
        // Reset checkboxes
        $("#gRankStepOne input[type=radio]:checked").each(function () {
            this.checked = false;
        });

        // Hide the "finish" footer.
        $("#s1-next").hide();        
    })


    // Step Two (Level of play, athleticism, skill)
    $("#gRankStepTwo").delegate("input[type=radio]", "click", function(){
        var activeSection = $("#gRankStepTwo").data('activeSection');

        if ($("div#stepTwoQuestions-" + activeSection + " div.questionGroup:not(:has(:radio:checked))").length) {
            // at least one group is blank
        } else {
            // $("#gRankStepTwo div.modal-footer").slideDown();
            $("#s2-next").show("puff");
            $("#gRankRunningTotal").addClass('badge-info');
        }

        var currentTotal = 0;
        $("#gRankStepTwo input[type=radio]:checked").each(function () {
            currentTotal += $(this).data('score');
        });
        $("#gRankRunningTotalValue").html(currentTotal.toFixed(1));
    });

    $("#gRankStepTwo").delegate("a#s2-next", "click", function() {
        var activeSection = $("#gRankStepTwo").data('activeSection');
        var selectedOptions = $("div#stepTwoQuestions-" + activeSection + " input[type=radio]:checked");

        // First clear all of the boxes
        $("input[id*=gRank-answers]").each(function() {
            $(this).val('');
        });

        // Now hide the display boxes
        // $("span[id*=gRank-display]").each(function() {
        //     if (this.id != 'gRank-display-status') {
        //         $(this).html("");
        //         $(this).hide();
        //     }
        // });

        $("#gRank-answers-experience").val(activeSection);
        // $("#gRank-display-experience").html(activeSection).slideDown();

        for (var i = 0; i < selectedOptions.length; i++) {
            var dataVals = $(selectedOptions[i]).data();

            $("#gRank-answers-" + dataVals.category).val(dataVals.index);
            // $("#gRank-display-" + dataVals.category).html(dataVals.index).slideDown();
        }

        // We're done, update the status
        $("#gRank-score").val($("#gRankRunningTotalValue").html());
        $("#gRank-display-status").html('Complete. Score: ' + $("#gRank-score").val());
        $("#gRankShow").removeClass("btn-warning").html("Re-take gRank Survey");

        $("#gRankStepTwo").modal("hide");
    });

    $("#gRankStepTwo").delegate("a#s2-back", "click", function() {
        $("#gRankStepTwo").modal("hide");
        $("#gRankStepOne").modal("show");
    });

    $('#gRankStepTwo').on('hidden', function () {
        // Reset checkboxes
        $("#gRankStepTwo input[type=radio]:checked").each(function () {
            this.checked = false;
        });

        // Hide question groups
        $("#gRankStepTwo .controls").each(function () {
            $(this).hide();
        });

        // Hide the "finish" footer.
        $("#s2-next").hide();

        // Reset Score
        $("#gRankRunningTotal").removeClass('badge-info')
        $("#gRankRunningTotalValue").html("0.0");
    })
});