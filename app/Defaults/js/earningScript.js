$(function () {
    $.ajax({
        type: 'post',
        url: 'reportGanancia',
        data: {
            content: 'json'
        },
        dataType: "json",
        success: function (data) {
             // PRODUCT :::::::::::::::::::::::::::::::::::::::
            if (data.product.code == 400) {
                $("#tableProduct").html(data.product.table);
                let title = "Productos con mas inversion de retorno";
                let myData = [];
                $.each($.parseJSON(data.product.chart), function (key, value) {
                    myData.push([value.description, (value.earning / data.product.total_earning)]);
                });
                chartEarning(title, myData, 'Product');
            } else {
                $("#tableProduct").html(
                    '<div class="callout callout-success text-align-center"> ' +
                    '<h3 style="text-align: center;">' +
                    '<i class="fa fa-info"></i> Ningun dato encontrado' +
                    '</h3> ' +
                    '</div>');
                $("#containerProduct").hide();
            }
            // STYLE ::::::::::::::::::::::::::::::::::::::::::
            if (data.style.code == 400) {
                $("#tableStyle").html(data.style.table);
                let title = "Estilo con mas inversion de retorno";
                let myData = [];
                $.each($.parseJSON(data.style.chart), function (key, value) {
                    myData.push([value.description, (value.earning / data.style.total_earning)]);
                });
                chartEarning(title, myData, 'Style');
            } else {
                $("#tableStyle").html(
                    '<div class="callout callout-success text-align-center"> ' +
                    '<h3 style="text-align: center;">' +
                    '<i class="fa fa-info"></i> Ningun dato encontrado' +
                    '</h3> ' +
                    '</div>');
                $("#containerStyle").hide();
            }
            // MODULE :::::::::::::::::::::::::::::::::::::::::
            if (data.module.code == 400) {
                $("#tableModule").html(data.module.table);
                let title = "Modulo con mas inversion de retorno";
                let myData = [];
                $.each($.parseJSON(data.module.chart), function (key, value) {
                    myData.push([value.description, (value.earning / data.module.total_earning)]);
                });
                chartEarning(title, myData, 'Module');
            } else {
                $("#tableModule").html(
                    '<div class="callout callout-success text-align-center"> ' +
                    '<h3 style="text-align: center;">' +
                    '<i class="fa fa-info"></i> Ningun dato encontrado' +
                    '</h3> ' +
                    '</div>');
                $("#containerModule").hide();
            }
        },
        error: function (p, r, m) {
            console.log(p.responseText);
            console.log(r);
            console.log(m);
        }
    });

    function chartEarning(title, data, container) {
        let $container = 'container' + container + '';
        Highcharts.chart($container, {
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