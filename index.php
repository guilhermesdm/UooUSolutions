<?php
$url = "https://api.apify.com/v2/key-value-stores/TyToNta7jGKkpszMZ/records/LATEST?disableRedirect=true";
$response = json_decode(file_get_contents($url), true);
$date = $response['lastUpdatedAtApify'];
$input_date = strtotime($date);
$list = [];
$listDeath = [];

foreach ($response['infectedByRegion'] as $key => $state) {
    $dataPoints = array("label" => $state['state'], "y" => $state['count']);
    $dataPoints2 = array("label" => $state['state'], "y" => $response['deceasedByRegion'][$key]['count']);
    array_push($list, $dataPoints);
    array_push($listDeath, $dataPoints2);
};
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title> Covid </title>

</head>
<body>
<div class="dateUpdate">
    <small>Última atualização: <?= date('d/M/Y H:i:s', $input_date); ?></small>
</div>
<div class="switch">
    <img src="icons/sun.svg" id="icon">
</div>
<h1 class="title">Covid-19 no Brasil</h1>
<div class="data">
    <div class="summary">
        <p> INFECTADOS: <?= number_format($response['infected'], 0); ?></p>
        <p> RECUPERADOS: <?= number_format($response['recovered'], 0); ?></p>
        <p> ÓBITOS: <?= number_format($response['deceased'], 0); ?></p>
    </div>
</div>
<table class="tabela" cellspacing="0">
    <thead>
    <tr>
        <th style="text-align: center" scope="col">Estados</th>
        <th style="text-align: center" scope="col">Casos</th>
        <th style="text-align: center" scope="col">Óbitos</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($response['infectedByRegion'] as $key => $state): ?>
        <tr>
            <td style="text-align: center; font-weight: bold"><?= $state['state']; ?></td>
            <td style="text-align: center"><?= number_format($state['count'], 0); ?></td>
            <td style="text-align: center"><?= number_format($response['deceasedByRegion'][$key]['count'], 0); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<script>
    window.onload = function () {
        var chart = new CanvasJS.Chart("chartContainer", {
            title: {
                text: "Casos de COVID por estado",
                fontFamily: "Roboto",
                margin: 30
            },
            theme: "dark1",
            animationEnabled: true,
            toolTip: {
                shared: true,
                reversed: true
            },
            axisY: {
                title: "Quantidade de casos",
                suffix: ""
            },
            legend: {
                cursor: "pointer",
                itemclick: toggleDataSeries
            },
            data: [
                {
                    type: "stackedColumn",
                    name: "Mortes",
                    showInLegend: true,
                    yValueFormatString: "#,##0",
                    dataPoints: <?php echo json_encode($listDeath, JSON_NUMERIC_CHECK); ?>
                }, {
                    type: "stackedColumn",
                    name: "Casos",
                    showInLegend: true,
                    yValueFormatString: "#,##0",
                    dataPoints: <?php echo json_encode($list, JSON_NUMERIC_CHECK); ?>
                },
            ]
        });
        chart.render();
        function toggleDataSeries(e) {
            if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                e.dataSeries.visible = false;
            } else {
                e.dataSeries.visible = true;
            }
            e.chart.render();
        }
        let icon = document.getElementById("icon");
        icon.onclick = function () {
            document.body.classList.toggle("light-theme");
            if (document.body.classList.contains("light-theme")){
                icon.src = "icons/moon.svg";
            } else {
                icon.src = "icons/sun.svg"
            }
            chart.options.theme = (chart.theme === "light2") ? "dark1" : "light2";
            chart.render()
        }
    }
</script>
<div id="chartContainer" style="height: 500px; width: 70%;" class="graph"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html>