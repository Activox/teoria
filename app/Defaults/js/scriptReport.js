$(function () {
    if ($("#problemInp").val() == 1) {
        request('reportProblem');
    } else {
        request('reportProduccion');
    }

    function request($request) {
        $.post('' + $request + '', {content: 'text'}, function (data) {
            $("#table").html(data);
            if ($request == 'reportProblem') {
                title = 'Estadistica sobre problemas de produccion';
            } else {
                title = 'Estadisticas sobre Produccion por Modulo';
            }
            chart(title);
        });
    }

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
});