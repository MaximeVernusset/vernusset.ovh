<div id="pyLoadOverlay-monitor-section">
    <div class="row">
        <h1>Monitor</h1>
    </div>
    <div id="monitoring" class="row">
        <div class="col col-sm-7">
            <div class="btn-toolbar mb-2" role="toolbar" aria-label="Controls">
                <div class="btn-group btn-group-sm" role="group" aria-label="Play/pause group">
                    <button id="play-button" class="btn btn-sm btn-outline-success" onclick="startDownload(this)"><i class="fa fa-play"></i>&nbsp;Start download</button>   
                    <button id="pause-button" class="btn btn-sm btn-outline-warning" onclick="pauseDownload(this)"><i class="fa fa-pause"></i>&nbsp;Pause download</button>
                </div>
                <div class="btn-group btn-group-sm ml-1 mr-1" role="group" aria-label="Clean queue group">
                    <button class="btn btn-sm btn-outline-info" onclick="cleanQueue(this)"><i class="fa fa-eraser"></i>&nbsp;Clean queue</button>
                </div>
                <div class="input-group input-group-sm ml-0">
                    <div class="input-group-prepend"><span class="input-group-text">Speed limit (Mo/s)</span></div>
                    <input type="number" id="speed-limit" class="form-control" aria-label="Speed limit">
                    <div class="input-group-append"><button id="limit-speed-button" class="btn btn-secondary" onclick="limitSpeed(this)"><i class="fa fa-check"></i></button></div>
                </div>
            </div>
            <div>
                TODO
            </div>
        </div>
        <div class="col col-sm-5 text-center">
            <canvas id="download-speed" width="100%"></canvas>
            <a href="<?=getConfig(URL, PYLOAD_CONFIG_FILE)?>"><i class="fas fa-sliders-h"></i>&nbsp;pyLoad interface&nbsp;<i class="fas fa-sliders-h"></i></a>
        </div>
    </div>

    <script src="public/vendor/chartjs/2.9.3/Chart.min.js"></script>
    <script>
        const MAX_DL_SPEED_DATA_RETENTION = 60;
        let dlSpeed = 0;
        let speedIntervalId;
        const downloadSpeedChart = new Chart(document.getElementById('download-speed').getContext('2d'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Download speed',
                    data: [],
                    borderColor: 'rgb(50, 200, 50)',
                    borderWidth: 1
                }]
            }
        });

        function getDowloadSpeed() {
            // TODO
            return Math.floor(Math.random() * 10);
        }

        function startDownload(button) {
            // TODO: call API
            $.post('api/pyLoadOverlay/monitor/startDownload/')
                .fail(() => console.log('Failed to clean queue'))
                .done(response => {
                    speedIntervalId = setInterval(() => {
                        downloadSpeedChart.data.labels.push('');
                        dlSpeed = getDowloadSpeed();
                        downloadSpeedChart.data.datasets[0].label = `Download speed (${dlSpeed} Mo/s)`;
                        downloadSpeedChart.data.datasets[0].data.push(dlSpeed);
                        if (downloadSpeedChart.data.datasets[0].data.length > MAX_DL_SPEED_DATA_RETENTION) {
                            downloadSpeedChart.data.datasets[0].data.shift();
                            downloadSpeedChart.data.labels.shift();
                        }
                        downloadSpeedChart.update();
                    }, 1000);

                    $(button).addClass('disabled');
                    $('#pause-button').removeClass('disabled');
                });
        }

        function pauseDownload(button) {
            $.post('api/pyLoadOverlay/monitor/pauseDownload/')
                .fail(() => console.log('Failed to clean queue'))
                .done(response => {
                    clearInterval(speedIntervalId);
                    $(button).addClass('disabled');
                    $('#play-button').removeClass('disabled');
                });
        }
        
        function cleanQueue(button) {
            $.post('api/pyLoadOverlay/monitor/cleanQueue/')
                .fail(() => console.log('Failed to clean queue'))
                .done(response => console.log(response));
            // TODO: remove deleted packages from DOM
        }
        
        function limitSpeed(button) {
            // TODO: call API
        }
    </script>

    <div class="row">
        <div class="container-fluid">
            <div class="row">
                <h2>Features to implement</h2>
            </div>
            <div class="row">
                <ul>
                    <li>Play/Pause download</li>
                    <li>See current download(s)</li>
                    <li>See download speed</li>
                    <li>Configure/enable download speed limit</li>
                    <li>Order download queue</li>
                    <li>Delete finished packages</li>
                    <li>...</li>
                </ul>
            </div>
        </div>
    </div>
</div>