//
// Bars chart
//

var DepositCharts = (function() {

    //
    // Variables
    //

    var $chart = $('#chart-deposit');


    //
    // Methods
    //

    // Init chart
    function initChart($chart) {

        // Create chart
        var depositChart = new Chart($chart, {
            type: 'bar',
            data: {
                labels: ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Player Deposit',

                    data: [25, 20, 30, 22, 17, 29],
                    backgroundColor: '#ff6384'
                }, {
                    label: 'Player Withdraw',

                    data: [23, 54, 11, 76, 2, 54],
                    backgroundColor: '#7193ff'
                }],

            },
        });

        // Save to jQuery object
        $chart.data('chart', depositChart);
    }


    // Init chart
    if ($chart.length) {
        initChart($chart);
    }

})();
var BarsChart = (function() {

    //
    // Variables
    //

    var $chart = $('#chart-withdraw');


    //
    // Methods
    //

    // Init chart
    function initChart($chart) {

        // Create chart
        var withdrawChart = new Chart($chart, {
            type: 'bar',
            data: {
                labels: ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Agent Deposit',
                    data: [25, 20, 30, 22, 17, 29],
                    backgroundColor: '#ff6384'

                }, {
                    label: 'Agent Withdraw',
                    data: [23, 44, 12, 4, 13, 29],
                    backgroundColor: '#7193ff'

                }]
            }
        });

        // Save to jQuery object
        $chart.data('chart', withdrawChart);
    }


    // Init chart
    if ($chart.length) {
        initChart($chart);
    }

})();