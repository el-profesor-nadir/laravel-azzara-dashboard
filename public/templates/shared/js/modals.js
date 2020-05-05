$('#delete').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) 
    var recipient = button.data('form-id') 
    var element = button.data('element') 
    var modal = $(this)
    modal.find('.modal-title').html('<i class="icon fa fa-ban"></i> Danger')
    modal.find('.modal-body').html('Are you sure you want to delete ? <br>' + element)
    document.getElementById('modal-delete-btn-confirm').setAttribute("onclick","document.getElementById('"+ recipient +"').submit();");
})