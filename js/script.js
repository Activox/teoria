/**
 * Jquery Document
 */
$(document).ajaxStart(function () {
    Pace.restart()
})
$(document).ready(function () {
    /**
     * Initialization elements
     */
    //Initialize Select2 Elements
    $('.select2').select2({tags: true});
    //Money Euro
    $('[data-mask]').inputmask();

    //Date picker
    $('.daterangepiker').datepicker({autoclose: true})
    // do a cuadre de caja
    $("#cuadreCaja").on('click', function () {
        alertify.confirm('Confirmar Cuadre de Caja', '¿Seguro que quiere cuadrar la caja?',
            function () {
                window.open("cuadreCaja");
                window.location.reload();
            }, function () {
                alertify.error('Cancel')
            });
    });
});

/**
 * Create combo box for generic fields.
 * @param $id
 * @param $post
 * @param $params
 */
ajax = function ($id, $post, $params) {
    $.ajax({
        dataType: 'json',
        url: '' + $post + '',
        data: {
            content: 'text',
            id: $params
        },
        success: function (response) {
            html = '<option value = "" disabled selected> Choose your option </option>';
            $.each(response.data, function (index, value) {
                html += '<option value = "' + value.id_record + '"> ' + value.description + '</option>';
            });
            $("#" + $id + "").html(html);
        }
    });
};

/**
 * This function get the value of id's
 * @param value
 * @returns {Array}
 * @constructor
 */
function values(value) {
    let array = value.split(',');
    let newarray = new Array();
    for (let n = 0; n < array.length; n++) {
        newarray['' + array[n] + ''] = $('' + array[n] + '').val();
    }
    return newarray;
};

/**
 * convert form to object.
 * @returns object
 */

$.fn.serializeObject = function () {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function () {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};


