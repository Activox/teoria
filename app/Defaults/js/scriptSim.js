$(function () {
    $("#container").hide();

    $("#btnEject").on('click', function () {
        // tableProblem(463);
        if ($("#temporadacmb").val() == 0 || $("#clientecmb").val() == 0 || $("#productocmb").val() == 0 || $("#stockcmb").val() == 0) {
            alertify.alert('Warning!', 'Porfavor completar todos los campos.', function () {
            });
            return;
        }

        $.ajax({
            type: 'post',
            url: 'doProcess',
            data: {
                content: 'json', data: $("#frmSimulacion").serializeObject()
            },
            dataType: "json",
            success: function (data) {
                console.log(data);
                $('#tableDetails').html(data.table);
                tableProblem(data.eject);
            },
            error: function (p, r, m) {
                console.log(p.responseText);
                console.log(r);
                console.log(m);
            }
        });

    });

    $("#clientecmb").on('change', function () {
        $.ajax({
            type: 'post',
            url: 'getProductStyle',
            data: {
                content: 'json', data: $(this).val()
            },
            dataType: "json",
            success: function (data) {
                $("#productocmb").html(data.product);
                $("#stockcmb").html(data.stock);
            },
            error: function (p, r, m) {
                console.log(p.responseText);
                console.log(r);
                console.log(m);
            }
        });
    });

    $("#productocmb").on('change', function () {
        $.ajax({
            type: 'post',
            url: 'getStyle',
            data: {
                content: 'text', data: $("#frmSimulacion").serializeObject()
            },
            dataType: "text",
            success: function (data) {
                $("#stockcmb").html(data);
            },
            error: function (p, r, m) {
                console.log(p.responseText);
                console.log(r);
                console.log(m);
            }
        });
    });

    /* get problem table. */
    function tableProblem(data2) {
        $.ajax({
            type: 'post',
            url: 'table',
            data: {
                content: 'text', data: data2
            },
            dataType: "json",
            success: function (data) {
                console.log(data);
                if (data.code == 400) {
                    $("#container").show();
                    $('#tableProblem').html(data.tableProblem);
                    let myData = [];
                    $.each($.parseJSON(data.chart), function (key, value) {
                        myData.push([value.problema, (value.cant_problema / value.sum_problem),value.problema]);
                    });
                    chart(myData);
                } else {
                    $('#tableProblem').html('<div class="callout callout-success text-align-center">\n' +
                        '                                        <h3 style="text-align: center;"><i class="fa fa-info"></i> Ningun dato\n' +
                        '                                            encontrado</h3>\n' +
                        '                                    </div>');
                    $("#container").hide();
                }
            },
            error: function (p, r, m) {
                console.log(p.responseText);
                console.log(r);
                console.log(m);
            }
        });


    }

    /* generate the charts */
    function chart(data) {
        Highcharts.chart('container', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Problemas encontrados en la simulacion'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: 'Brands',
                colorByPoint: true,
                data: data
            }]
        });
    }

    /* load the customer comboBox */
    $.ajax({
        type: 'post',
        url: 'getCustomer',
        data: {
            content: 'text', data: $(this).val()
        },
        dataType: "text",
        success: function (data) {
            $("#clientecmb").html(data);
        },
        error: function (p, r, m) {
            console.log(p.responseText);
            console.log(r);
            console.log(m);
        }
    });
});