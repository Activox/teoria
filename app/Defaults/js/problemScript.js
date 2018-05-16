$(function () {
    $.ajax({
        type: 'post',
        url: 'getReportProblem',
        data: {
            content: 'json'
        },
        dataType: "json",
        success: function (data) {
            // PRODUCT :::::::::::::::::::::::::::::::::::::::
            $("#tableQty").html(data.qty);
            chart("Cantidad de problemas", 'Qty');

            // PRODUCT :::::::::::::::::::::::::::::::::::::::
            $("#tableProduct").html(data.product);
            chart("Productos con mas problemas", 'Product');

            // STYLE ::::::::::::::::::::::::::::::::::::::::::
            $("#tableStyle").html(data.style);
            chart("Estilo con mas problemas", 'Style');

            // MODULE :::::::::::::::::::::::::::::::::::::::::
            $("#tableModule").html(data.module);
            chart("Modulo con mas problemas", 'Module');
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