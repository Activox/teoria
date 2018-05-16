$(function () {
    $.ajax({
        type: 'post',
        url: 'getReportProduction',
        data: {
            content: 'json'
        },
        dataType: "json",
        success: function (data) {

            // PRODUCT :::::::::::::::::::::::::::::::::::::::
            $("#tableProduct").html(data.product);
            chart("Productos con mas produccion", 'Product');

            // STYLE ::::::::::::::::::::::::::::::::::::::::::
            $("#tableStyle").html(data.style);
            chart("Estilo con mas produccion ", 'Style');

            // MODULE :::::::::::::::::::::::::::::::::::::::::
            $("#tableModule").html(data.module);
            chart("Modulo con mas produccion ", 'Module');
        },
        error: function (p, r, m) {
            console.log(p.responseText);
            console.log(r);
            console.log(m);
        }
    });

    function chart(title, container) {
        let $container = 'container' + container + '';
        Highcharts.chart($container, {
            data: {
                table: 'datatable' + container + ''
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