$(function () {
    $("#container").hide();
    $("#btnEject").on('click', function () {
        // tableProblem(463);
        if ($("#temporadacmb").val() == 0) {
            alertify.alert('Warning!', 'Debes seleccionar una estacion del año por lo menos.', function () {
            });
            return;
        }
        $.ajax({
            type: 'post',
            url: 'simulacion',
            data: {
                content: 'text', data: $("#frmSimulacion").serializeObject()
            },
            dataType: "json",
            success: function (data) {
                console.log(data);
                $('#tableDetails').html(data.table);
                tableProblem(data.eject);
            },
            error: function(p,r,m){
                console.log(p.responseText);
                console.log(r);
                console.log(m);
            }
        });

    });

    function tableProblem(data2) {
        $.ajax({
            type: 'post',
            url : 'table',
            data: {
                content: 'text', data: data2
            },
            dataType : "json",
            success: function(data){
                console.log(data);
                if (data.code == 400) {
                            $("#container").show();
                            $('#tableProblem').html(data.tableProblem);
                            let myData = [];
                            $.each($.parseJSON(data.chart), function (key, value) {
                                myData.push([value.problema, (value.cant_problema / value.sum_problem)]);
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
            error: function(p,r,m){
                console.log(p.responseText);
                console.log(r);
                console.log(m);
            }
        });


    }

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
                        enabled: false
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

});