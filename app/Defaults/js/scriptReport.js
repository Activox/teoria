$(function () {
    if ($("#problemInp").val() == 1) {
        request('reportProblem');
    } else if ($("#earnInp").val() == 1) {
        request('reportEarn');
    } else {
        request('reportProduccion');
    }

    function request($request) {
        $.get('' + $request + '', {content: ($request == 'reportEarn' ? 'json' : 'text')}, function (data) {
            if ($request == 'reportEarn') {
                console.log(data);
                if (data.code == 400) {
                    $("#table").html(data.table);
                    let title = "Productos con mas inversion de retorno";
                    let myData = [];
                    $.each($.parseJSON(data.chart), function (key, value) {
                        myData.push([value.description, (value.earning / data.total_earning)]);
                    });
                    chartEarning(title, myData);
                } else {
                    $("#table").html(
                        '<div class="callout callout-success text-align-center"> ' +
                        '<h3 style="text-align: center;">' +
                        '<i class="fa fa-info"></i> Ningun dato encontrado' +
                        '</h3> ' +
                        '</div>');
                    $("#container").hide();
                }
            } else {
                $("#table").html(data);
                if ($request == 'reportProblem') {
                    title = 'Estadistica sobre problemas de produccion';
                } else {
                    title = 'Estadisticas sobre Produccion por Modulo';
                }
                chart(title);
            }
        }, ($request == 'reportEarn' ? 'json' : 'text'));
    }

    /* load chart reports */
    function chart(title) {
        Highcharts.chart('container', {
            data: {
                table: 'datatable'
            },
            chart: {
                type: 'column'
            },
            title: {
                text: '' + title + ''
            },
            yAxis: {
                allowDecimals: false,
                title: {
                    text: 'qty'
                }
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.series.name + '</b><br/>' +
                        this.point.y + ' ' + this.point.name.toLowerCase();
                }
            }
        });
    }

    function chartEarning(title, data) {
        Highcharts.chart('container', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: '' + title + ''
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
});