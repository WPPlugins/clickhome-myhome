jQuery(function ($) {
    _.extend(mh, {
        notes: {
            vars: {
                loading: false
            },

            init: function () {
                $("#inputMyHomeNewNoteSubject")
                  .focus(function () {
                      self.showNewNote();
                  })
                  .blur(function () {
                      //self.hideNewNote();
                  });

                $("#buttonMyHomeNewNoteCancel").click(self.clearNewNote);
                $("#buttonMyHomeNewNoteOk").click(self.submitNewNote);
            },

            showNewNote: function(){
                $('.mh-section-notes-new').addClass('mh-show');
                //$("#textareaMyHomeNewNoteBody").parent().show(100);
                //$("#divMyHomeNewNoteButtons").show(100);
            },

            hideNewNote: function(){
                if($("#inputMyHomeNewNoteSubject").val().trim()!=="")
                    return;

                $('.mh-section-notes-new').removeClass('mh-show');
                //$("#textareaMyHomeNewNoteBody").parent().hide(100);
                //$("#divMyHomeNewNoteButtons").hide(100);
            },

            clearNewNote: function(){
                $("#inputMyHomeNewNoteSubject").val("");
                $("#textareaMyHomeNewNoteBody")
                  .val("")
                  .parent().hide(100);
                $("#divMyHomeNewNoteButtons").hide(100);
            },

            submitNewNote: function() {
                var subject = $("#inputMyHomeNewNoteSubject").val().trim();
                var body = $("#textareaMyHomeNewNoteBody").val().trim();

                if (subject === "") {
                    alert("You must provide a subject for the note");
                    return;
                }
                if (body === "") {
                    alert("You must provide a body for the message");
                    return;
                }

                if (self.vars.loading) return;
                self.vars.loading = true;
                $("#divMyHomeLoadingNotes").css("display", "block");

                //var params=<?php //echo json_encode($xhrAttributes['params']); ?>;

                self.xhr.actions[0].myHomeSubject = subject;
                self.xhr.actions[0].myHomeBody = body;

                $.post(self.xhr.url, self.xhr.actions[0], function (html) {
                    self.clearNewNote();
                    $("#divMyHomeNotesList").prepend(html);
                }, "html").fail(function (jqXhr, textStatus, errorThrown) {
                    alert("The note could not be created: " + errorThrown);
                }).always(function () {
                    $("#divMyHomeLoadingNotes").hide();
                    self.vars.loading = false;
                });
            }
        }
    });

    var self = mh.notes;
});
