var width = 930,
    height = 600;

var cola = cola.d3adaptor()
    .linkDistance(90)
    .avoidOverlaps(true)
    .size([width, height]);

$(function() {

    // Select field
    $('.selectpicker').selectpicker()

    // Collapse slide down/up
    function collapseOpen(button) {
        var glyphicon = button.children('.glyphicon')
        glyphicon.removeClass('glyphicon-triangle-bottom').addClass('glyphicon-triangle-top')
    }
    function collapseClose(button) {
        var glyphicon = button.children('.glyphicon')
        glyphicon.removeClass('glyphicon-triangle-top').addClass('glyphicon-triangle-bottom')
    }
    function bindBtnCollapse() {
        $('.btn-collapse').on('click', function () {
            if ($($(this).attr('data-target')).hasClass('in')) {
                collapseClose($(this))
            } else {
                collapseOpen($(this))
            }
        })
    }
    bindBtnCollapse()

    // Word card button group show/hide
    function bindBtnGrp() {
        $('.w-item').on('mouseover', function () {
            $(this).children('.sns-btn-wrap').show()
        })
        $('.w-item').on('mouseleave', function () {
            $(this).children('.sns-btn-wrap').hide()
        })
    }
    bindBtnGrp()

    // Searchbox
    var wordSearch = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('word'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: '/ajax/search?name=%QUERY',
            wildcard: '%QUERY'
        }
    });
    $('#w-search').typeahead({
        minLength: 2
    }, {
        name: 'word-search',
        display: 'name',
        source: wordSearch,
        templates: {
            header: '<div class="tt-suggestion tt-notfound">Add word<button type="button" id="btn-add-w" class="btn btn-success btn-sm" data-toggle="modal" data-target="#add-w-modal"><span class="glyphicon glyphicon-plus"></span></button></div>',
            notFound: '<div class="tt-suggestion tt-notfound">Add word<button type="button" id="btn-add-w" class="btn btn-success btn-sm" data-toggle="modal" data-target="#add-w-modal"><span class="glyphicon glyphicon-plus"></span></button></div>',
        },
    });
    $('#w-search').bind('typeahead:select', function(ev, suggestion) {
        // window.location.href = '/home?lang=' + suggestion.lang + '&name=' + suggestion.name;
        window.location.href = '/home?wid=' + suggestion._id.$id;
    });

    // Connection links
    $('a.conn').on('click', function() {
        // From sense highlight
        $('#left-list .w-item').removeClass('focus')
        var fromId = $(this).attr('from-id')
        var fromSense = $(this).attr('from-sns')
        if (fromSense.length > 0) {
            $('#w-' + fromId + '-' + fromSense).addClass('focus')
        }

        // To sense highlight
        $('.right-list .w-item').removeClass('focus')
        var toId = $(this).attr('to-id')
        var toSense = $(this).attr('to-sns')
        if (toSense.length > 0) {
            $('#w-' + toId + '-' + toSense).addClass('focus')
        }

        // Scroll to word
        var dest = $('#w-' + toId)
        $("#right-list").scrollTo(dest, 300)

        // Un-collapse word content
        dest.find('.w-content').collapse('show')

        // Adjust collapse/expand toggle button status
        // Word content panel toggle button
        var button = dest.find('.btn-w-content.btn-collapse')
        if (!$(button.attr('data-target')).hasClass('in')) {
            collapseOpen(button)
        }
        // Sentences panel toggle button
        var buttons = dest.find('.btn-snt.btn-collapse')
        buttons.each(function() {
            if ($($(this).attr('data-target')).hasClass('in')) {
                collapseOpen($(this))
            }
        })
    })
    $('a.conn').on('dblclick', function() {
    })

    // Reusable variables
    var wId   = $('#left-list .w-id').attr('value')
    var wLang = $('#left-list .w-lang').attr('value')
    var wName = $('#left-list .w-title h3').html()

    /***** Word modal ******/

    $('#add-w-modal').on('show.bs.modal', function(event) {
        $('#add-w-name').val($('#w-search').val())
    })
    $('#add-w-modal').on('shown.bs.modal', function(event) {
        $('#add-w-snss').focus()
    })
    $('#add-w-btn').on('click', function(event) {
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            method: "POST",
            url: "/word/add",
            dataType: 'json',
            data: {
                lang:  $('#add-w-lang').val(),
                name:  $('#add-w-name').val(),
                snss:  $('#add-w-snss').val(),
                _csrf: csrfToken
            },
            success: function(response) {
                ajaxNotify('success', 'Word added. <a href="' + response.link + '">Go to</a>')
            },
            error: function(xhr, ajaxOptions, thrownError) {
                ajaxNotify('danger', 'Word not added. ' + xhr.responseText)
            },
        })
    })

    /***** Sense modal ******/

    $('#sns-modal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var sense  = button.closest('.w-item')
        var word   = button.closest('.w-card')

        // When edit sense
        if (button.hasClass('btn-edit-sns')) {
            $('#sns-id').val(sense.children('.w-sns-name').html())
            $('#sns-id').attr('disabled', 'disabled')
            $('#sns-expl').val(sense.children('.w-sns-expl').val().replace(/\\n/g, "\n"))
            $('#sns-snts').val(sense.children('.w-sns-snts').val().replace(/\\n/g, "\n"))

        // When add sense
        } else {
            $('#sns-id').val('')
            $('#sns-id').removeAttr('disabled')
            $('#sns-expl').val('')
            $('#sns-snts').val('')
        }
    })
    $('#sns-modal').on('shown.bs.modal', function(event) {
        $('#sns-id').focus();
    })
    // Add sense action
    $('#save-sns-btn').on('click', function(event) {
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            method: "POST",
            url: "/sense/save",
            dataType: 'json',
            data: {
                wid:   wId,
                sns:   $('#sns-id').val(),
                expl:  $('#sns-expl').val(),
                snts:  $('#sns-snts').val(),
                _csrf: csrfToken
            },
            success: function(response) {
                ajaxNotify('success', 'Sense saved.')
                $('#sns-modal').modal('hide')

                // Update page
                var snsId = '#w-' + $('#sns-w-id').val() + '-' + $('#sns-id').val().replace('.', '_')
                if ($(snsId).length > 0) {
                    $(snsId).replaceWith(response)
                } else {
                    $('#left-list .w-content').append(response)
                }
                bindBtnGrp()
                bindBtnCollapse()
            },
            error: function(xhr, ajaxOptions, thrownError) {
                ajaxNotify('danger', 'Sense not saved. ' + xhr.responseText)
            },
        })
    })
    // Delete sense action
    $('#del-sns-btn').on('click', function(event) {
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            method:   "POST",
            url:      "/sense/delete",
            dataType: "json",
            data: {
                wid:   wId,
                sns:   $('#sns-id').val(),
                _csrf: csrfToken
            },
            success: function(response) {
                ajaxNotify('success', 'Sense deleted.')
                $('#sns-modal').modal('hide')

                // Update page
                var snsId = '#w-' + $('#sns-w-id').val() + '-' + $('#sns-id').val().replace('.', '_')
                $(snsId).remove()
            },
            error: function(xhr, ajaxOptions, thrownError) {
                ajaxNotify('danger', 'Sense not deleted. ' + xhr.responseText)
            },
        })
    })
    // Add senses in batch action
    $('#save-sns-multi-btn').on('click', function(event) {
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            method: "POST",
            url: "/sense/addmulti",
            dataType: 'json',
            data: {
                wid:   wId,
                snss:  $('#sns-multi').val(),
                _csrf: csrfToken
            },
            success: function(response) {
                ajaxNotify('success', 'Senses added.')
                location.reload(true)
            },
            error: function(xhr, ajaxOptions, thrownError) {
                ajaxNotify('danger', 'Senses not added. ' + xhr.responseText)
            },
        })
    })

    /***** Connection modal ******/

    $('#conn-modal').on('show.bs.modal', function(event) {
        $('#conn-from-lang').val(wLang)
        $('#conn-from-lang').selectpicker('refresh')
        $('#conn-from-name').val(wName)

        $('#conn-to-name').val('');
        $('#conn-to-sns').val('');
    })
    $('#conn-modal').on('shown.bs.modal', function(event) {
        $('#conn-to-name').focus()
    })
    // Add connection
    $('#add-conn-btn').on('click', function(event) {
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            method:   "POST",
            url:      "/connection/add",
            dataType: "json",
            data: {
                fid:   wId,
                flang: wLang,
                fname: wName,
                fsns:  $('#conn-from-sns').val(),
                type:  $('#conn-type').val(),
                tlang: $('#conn-to-lang').val(),
                tname: $('#conn-to-name').val(),
                tsns:  $('#conn-to-sns').val(),
                _csrf: csrfToken
            },
            success: function(response) {
                ajaxNotify('success', 'Connection added.')
                $('#conn-modal').modal('hide')

                // Update page
                $('#empty-holder').before(response)
                bindBtnGrp()
                bindBtnCollapse()
            },
            error: function(xhr, ajaxOptions, thrownError) {
                ajaxNotify('danger', 'Connection not added. ' + xhr.responseText)
            },
        })
    })

    $('#edit-conn-modal').on('show.bs.modal', function(event) {
        var btn  = $(event.relatedTarget)
        var word = btn.closest('.w-card')

        $('#edit-conn-from-lang').val(wLang)
        $('#edit-conn-from-lang').selectpicker('refresh')
        $('#edit-conn-from-name').val(wName)
        $('#edit-conn-from-sns').val(btn.attr('f-sns'))
        $('#edit-conn-from-sns').selectpicker('refresh')
        $('#edit-conn-type').val(btn.attr('conn-type'))
        $('#edit-conn-type').selectpicker('refresh')
        $('#edit-conn-to-id').val(word.children('.w-id').attr('value'))
        $('#edit-conn-to-lang').val(word.children('.w-lang').attr('value'))
        $('#edit-conn-to-lang').selectpicker('refresh')
        $('#edit-conn-to-name').val(word.find('.w-title h3 a').html())

        // Populate word senses selection
        var senses = btn.attr('t-snss').split(',')
        var options = ''
        $.each(senses, function(key, value){
            options += '<option value="' + value + '"'
            if (value == btn.attr('t-sns')) {
                options += ' selected'
            }
            options += '>' + value.replace('_', '.') + '</option>'
        })
        $('#edit-conn-to-sns').html(options)
        $('#edit-conn-to-sns').selectpicker('refresh')
    })
    // Edit connection
    $('#up-conn-btn').on('click', function(event) {
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            method:   "POST",
            url:      "/connection/update",
            dataType: "json",
            data: {
                fid:   wId,
                flang: wLang,
                fname: wName,
                fsns:  $('#edit-conn-from-sns').val(),
                type:  $('#edit-conn-type').val(),
                tid:   $('#edit-conn-to-id').val(),
                tlang: $('#edit-conn-to-lang').val(),
                tname: $('#edit-conn-to-name').val(),
                tsns:  $('#edit-conn-to-sns').val(),
                _csrf: csrfToken
            },
            success: function(response) {
                ajaxNotify('success', 'Connection updated.')
                location.reload(true)
            },
            error: function(xhr, ajaxOptions, thrownError) {
                ajaxNotify('danger', 'Connection not updated. ' + xhr.responseText)
            },
        })
    })
    // Delete connection
    $('#del-conn-btn').on('click', function(event) {
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            method:   "POST",
            url:      "/connection/delete",
            dataType: "json",
            data: {
                fid:   wId,
                flang: wLang,
                fname: wName,
                fsns:  $('#edit-conn-from-sns').val(),
                type:  $('#edit-conn-type').val(),
                tid:   $('#edit-conn-to-id').val(),
                tlang: $('#edit-conn-to-lang').val(),
                tname: $('#edit-conn-to-name').val(),
                tsns:  $('#edit-conn-to-sns').val(),
                _csrf: csrfToken
            },
            success: function(response) {
                ajaxNotify('success', 'Connection deleted.')
                location.reload(true)
            },
            error: function(xhr, ajaxOptions, thrownError) {
                ajaxNotify('danger', 'Connection not deleted. ' + xhr.responseText)
            },
        })
    })

    function ajaxNotify(type, text) {
        $('.top-right').notify({
            message:  {html: text},
            type:     type,
            closable: false,
        }).show()
    }

    /***** Word graph modal ******/

    function calcStringWidth(string) {
        var ruler = $("#string-ruler")
        ruler.html(string)
        return ruler.width() + 20
    }

    function drawGraph(graph) {
        $('#graph-wrapper').html('')

        var svg = d3.select("#graph-wrapper").append("svg")
            .attr("width", width)
            .attr("height", height);

        graph.groups.forEach(function (g) { g.padding = 0.01 })

        cola.nodes(graph.nodes)
            .links(graph.links)
            .groups(graph.groups)
            .start()

        var group = svg.selectAll(".group")
            .data(graph.groups)
            .enter().append("rect")
            .attr("rx", 8).attr("ry", 8)
            .attr("class", "group")
            .style("fill", function (d, i) { return d.color })

        var link = svg.selectAll(".link")
            .data(graph.links)
            .enter().append("line")
            .attr("class", function (d) { return "link " + d.class })

        var pad = 3;
        var node = svg.selectAll(".node")
            .data(graph.nodes)
            .enter().append("rect")
            .attr("class", function (d) { return "node " + d.class })
            .attr("width", function (d) { return calcStringWidth(d.name) })
            .attr("height", function (d) { return 30 })
            .attr("rx", 3).attr("ry", 3)
            .style("fill", function (d) { return d.color })
            .on("dblclick", function (d) { if (d.id) { initGraph(d.id) } })
            .call(cola.drag)

        var label = svg.selectAll(".label")
            .data(graph.nodes)
            .enter().append("text")
            .attr("class", function (d) { return "label " + d.class })
            .text(function (d) { return d.name })
            .on("dblclick", function (d) { if (d.id) { initGraph(d.id) } })
            .call(cola.drag)

        node.append("title")
            .text(function (d) { return d.name })

        cola.on("tick", function () {
            link.attr("x1", function (d) { return d.source.x; })
                .attr("y1", function (d) { return d.source.y; })
                .attr("x2", function (d) { return d.target.x; })
                .attr("y2", function (d) { return d.target.y; })

            node.attr("x", function (d) { return d.x - calcStringWidth(d.name) / 2 })
                .attr("y", function (d) { return d.y - 30 / 2 })

            group.attr("x", function (d) { return d.bounds.x - 2 * pad })
                .attr("y", function (d) { return d.bounds.y - 2 * pad })
                .attr("width", function (d) { return d.bounds.width() + 4 * pad })
                .attr("height", function (d) { return d.bounds.height() + 4 * pad })

            label.attr("x", function (d) { return d.x })
                .attr("y", function (d) {
                     var h = this.getBBox().height
                     return d.y + h/4
                })
        })
    }

    function initGraph(wordId) {
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            method:   "POST",
            url:      "/graph/load",
            dataType: "json",
            data: {
                wid:   wordId,
                _csrf: csrfToken
            },
            success: function(graph) {
                drawGraph(graph)
            },
            error: function(xhr, ajaxOptions, thrownError) {
                ajaxNotify('danger', 'Graph not loaded. ' + xhr.responseText)
            },
        })
    }

    $('#graph-modal').on('show.bs.modal', function(event) {
        initGraph(wId)
    })
    $('#graph-modal').on('hide.bs.modal', function(event) {
        cola.stop()
        $('#graph-wrapper').html('')
    })
})

// Enable tab input in textareas
$(document).delegate('textarea', 'keydown', function(e) {
    var keyCode = e.keyCode || e.which;

    if (keyCode == 9) {
        e.preventDefault();
        var start = $(this).get(0).selectionStart;
        var end = $(this).get(0).selectionEnd;

        // set textarea value to: text before caret + tab + text after caret
        $(this).val($(this).val().substring(0, start)
                    + "\t"
                    + $(this).val().substring(end));

        // put caret at right position again
        $(this).get(0).selectionStart =
        $(this).get(0).selectionEnd = start + 1;
    }
});