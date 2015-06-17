$(function() {

	// Select field
	$('.selectpicker').selectpicker()

	// Connection link click
	$('a.conn').on('click', function() {
		var dest = $($(this).attr('dest'))
		$("#right-list").scrollTo(dest, 300)

		// Un-collapse word content
		dest.find('.w-content').collapse('show')
		// Toggle button
		var button = dest.find('.btn-collapse')
		if (!$(button.attr('data-target')).hasClass('in')) {
			collapseOpen(button)
		}
	})
	$('a.conn').on('dblclick', function() {
	})

	// Button group show/hide
	$('.w-item').on('mouseover', function () {
		$(this).children('.sense-btn-wrap').show()
	})
	$('.w-item').on('mouseleave', function () {
		$(this).children('.sense-btn-wrap').hide()
	})

	// Collapse slide down/up
	$('.btn-collapse').on('click', function () {
		if ($($(this).attr('data-target')).hasClass('in')) {
			collapseClose($(this))
		} else {
			collapseOpen($(this))
		}
	})
	function collapseOpen(button) {
		var glyphicon = button.children('.glyphicon')
		glyphicon.removeClass('glyphicon-triangle-bottom').addClass('glyphicon-triangle-top')
	}
	function collapseClose(button) {
		var glyphicon = button.children('.glyphicon')
		glyphicon.removeClass('glyphicon-triangle-top').addClass('glyphicon-triangle-bottom')
	}

	// Word model
	$('#add-w-modal').on('show.bs.modal', function(event) {
		$('#add-w').val($('#w-search').val())
	})
	$('#add-w-modal').on('shown.bs.modal', function(event) {
		$('#add-w').focus()
	})

	// Sense modal
	$('#sense-modal').on('show.bs.modal', function(event) {
		var button = $(event.relatedTarget)
		var sense  = button.closest('.w-item')
		var word   = button.closest('.w-card')
		
		$('#sense-w-id').val(word.children('.w-id').attr('value'))

		// Edit sense
		if (button.hasClass('btn-edit-sense')) {
			$('#sense-id').val(sense.children('.w-sense-name').html())
			$('#sense-id').attr('disabled', 'disabled')
			$('#sense-data').val(sense.children('.w-sense-data').val().replace(/\\n/g, "\n"))
			$('#sent-data').val(sense.children('.w-sent-data').val().replace(/\\n/g, "\n"))

		// Add sense
		} else {
			$('#sense-id').val('')
			$('#sense-id').removeAttr('disabled')
			$('#sense-data').val('')
			$('#sent-data').val('')
		}
	})
	$('#sense-modal').on('shown.bs.modal', function(event) {
		$('#sense-id').focus();
	})

	// Connection modal
	$('#conn-modal').on('show.bs.modal', function(event) {
		var button = $(event.relatedTarget)
		var word   = button.closest('.w-card')

		$('#conn-from-id').val(word.children('.w-id').attr('value'))
		$('#conn-from-lang').val(word.children('.w-lang').attr('value'))
		// Set option as selected
		$('#conn-from-lang').selectpicker('refresh')
		$('#conn-from-word').val(word.find('.w-title h3').html())
	})
	$('#conn-modal').on('shown.bs.modal', function(event) {
		$('#conn-to-word').focus()
	})
})