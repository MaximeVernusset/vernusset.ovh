<div id="pyLoadOverlay-monitor-section">
    <div class="row">
        <h1>Monitor</h1>
    </div>
    <div id="monitoring">
        <div id="downloads" class="row">
            <div class="col-xl-7 remove-col-padding">
                <div class="btn-toolbar mb-2" role="toolbar" aria-label="Controls">
                    <div class="btn-group btn-group-sm mb-1 mr-1" role="group" aria-label="Play/pause group">
                        <button id="play-button" class="btn btn-sm btn-outline-success" onclick="startDownload(this)"><i class="fa fa-play" aria-hidden="true"></i>&nbsp;Start queue</button> 
                        <button id="pause-button" class="btn btn-sm btn-outline-warning" onclick="pauseDownload(this)"><i class="fa fa-pause" aria-hidden="true"></i>&nbsp;Pause queue</button>
                    </div>
                    <div class="btn-group btn-group-sm mb-1 mr-1" role="group" aria-label="Clean queue group">
                        <button class="btn btn-sm btn-outline-info" onclick="cleanQueue(this)"><i class="fa fa-eraser" aria-hidden="true"></i>&nbsp;Clean queue</button>
                    </div>
                    <div class="input-group input-group-sm ml-0 mb-1 mr-1">
                        <div class="input-group-prepend"><span class="input-group-text">Speed limit (kB/s)</span></div>
                        <input type="number" id="speed-limit" class="form-control" aria-label="Speed limit">
                        <div class="input-group-append"><button id="limit-speed-button" class="btn btn-secondary" onclick="limitSpeed(this)" aria-label="Limit speed"><i class="fa fa-check" aria-hidden="true"></i></button></div>
                    </div>
                    <div class="input-group input-group-sm ml-0 mb-1">
                        <div class="input-group-prepend"><span class="input-group-text">Max downloads</span></div>
                        <input type="number" id="max-downloads" class="form-control" aria-label="Max parallel downloads">
                        <div class="input-group-append"><button id="max-downloads-button" class="btn btn-secondary" onclick="setMaxParallelDownloads(this)" aria-label="Set maximium parallel downloads"><i class="fa fa-check" aria-hidden="true"></i></button></div>
                    </div>
                </div>
                <div id="current-downloads" class="mt-3">
                </div>
            </div>
            <div class="col-xl-5 text-center">
                <canvas id="download-speed"></canvas>
                <small><a href="<?=getConfig(URL, PYLOAD_CONFIG_FILE)?>"><i class="fas fa-sliders-h"></i>&nbsp;pyLoad interface&nbsp;<i class="fas fa-sliders-h"></i></a></small>
            </div>
        </div>
        <div class="">
            <div id="queue" class="row">
            </div>
        </div>
    </div>

    <script src="public/vendor/chartjs/2.9.3/Chart.min.js"></script>
    <script>
        const ENTER_KEYCODE = '13';
        const FETCH_INTERVAL = 1500;
        const MB_DIVIDER = Math.pow(2, 20);
        const GB_DIVIDER = Math.pow(2, 30);
        const MAX_DL_SPEED_DATA_RETENTION = 60 * 1000 / FETCH_INTERVAL;
        const MAP_DL_STATUS_CSS_COLOR = {
            0: 'text-success',
            2: 'text-info',
            3: 'text-info',
            5: 'text-info',
            7: 'text-info',
            10: 'text-info',
            12: 'text-info',
            13: 'text-info',
            4: 'text-warning',
            9: 'text-warning',
            6: 'text-warning',
            8: 'text-danger',
            1: 'text-danger',
            11: 'text-muted',
            14: 'text-muted'
        };
        
        const downloadSpeedChart = new Chart(document.getElementById('download-speed').getContext('2d'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Download speed',
                    data: [],
                    borderColor: '#28a745',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            min: 0   
                        }
                    }]
                }
            }
        });
        
        let serverStatus;
        let currentDownloads;
        let speedIntervalId;
        let downloadSpeed = 0;

        $(document).ready(() => {
            monitor();
            fetchQueue();
            getDownloadConfig();
            setInterval(monitor, FETCH_INTERVAL);
            setInterval(fetchQueue, FETCH_INTERVAL * 1.5);
            $('#speed-limit').keypress(e => {
                if (e.keyCode == ENTER_KEYCODE || e.which == ENTER_KEYCODE) {
                    $('#limit-speed-button').click();
                }
            });
            $('#max-downloads').keypress(e => {
                if (e.keyCode == ENTER_KEYCODE || e.which == ENTER_KEYCODE) {
                    $('#max-downloads-button').click();
                }
            });
        });

        function monitor() {
            callPyLoadOverlayApi('GET', 'getCurrentDownloads')
                .fail(() => console.log('Failed to get downloads'))
                .done(response => {
                    currentDownloads = response.data.currentDownloads;
                    displayCurrentDownloads();
            });
            callPyLoadOverlayApi('GET', 'getServerStatus')
                .fail(() => console.log('Failed to get server status'))
                .done(response => {
                    serverStatus = response.data.serverStatus;
                    downloadSpeed = (serverStatus.speed / MB_DIVIDER).toFixed(2);
                    updateDownloadSpeedChart();
                    if (serverStatus.download) {
                        $('#play-button').addClass('disabled');
                        $('#pause-button').removeClass('disabled');
                    } else {                    
                        $('#pause-button').addClass('disabled');
                        $('#play-button').removeClass('disabled');
                    }
            });
        }        

        function callPyLoadOverlayApi(method, apiToCall, params = {}) {
            return callApi(method, `api/pyLoadOverlay/monitor/${apiToCall}/`, params);
        }

        function updateDownloadSpeedChart() {
            downloadSpeedChart.data.labels.push('');
            downloadSpeedChart.data.datasets[0].label = `Download speed (${downloadSpeed} MB/s)`;
            downloadSpeedChart.data.datasets[0].data.push(downloadSpeed);
            if (downloadSpeedChart.data.datasets[0].data.length > MAX_DL_SPEED_DATA_RETENTION) {
                downloadSpeedChart.data.datasets[0].data.shift();
                downloadSpeedChart.data.labels.shift();
            }
            downloadSpeedChart.update();
        }

        function getDownloadConfig() {
            callPyLoadOverlayApi('GET', 'getDownloadConfig')
                .fail(() => console.log('Failed to get server download configuration'))
                .done(response => {
                    const configs = response.data.downloadConfig.items;
                    $('#speed-limit').val(configs.filter(config => config.name === 'max_speed')[0].value);
                    $('#max-downloads').val(configs.filter(config => config.name === 'max_downloads')[0].value);
                });
        }

        function startDownload(button) {
            callPyLoadOverlayApi('POST', 'startDownload')
                .fail(() => console.log('Failed to start downloading queue'))
                .done(response => {
                    $('#play-button').addClass('disabled');
                    $('#pause-button').removeClass('disabled');
                });
        }

        function pauseDownload(button) {
            callPyLoadOverlayApi('POST', 'pauseDownload')
                .fail(() => console.log('Failed to pause downloading queue'))
                .done(response => {
                    $('#pause-button').addClass('disabled');
                    $('#play-button').removeClass('disabled');
                });
        }
        
        function cleanQueue(button) {
            const buttonText = $(button).html();
            $(button).addClass('disabled');
            callPyLoadOverlayApi('POST', 'cleanQueue')
                .fail(() => console.log('Failed to clean queue'))
                .done(response => {
                    $(button).html(response.message);
                    setTimeout(() => {
                        $(button).html(buttonText);
                        $(button).removeClass('disabled');
                    }, FETCH_INTERVAL);
                });
        }
        
        function limitSpeed(button) {
            const buttonText = $(button).html();
            const downloadSpeedLimit = {speedLimit: $('#speed-limit').val()};
            callPyLoadOverlayApi('POST', 'limitDownloadSpeed', downloadSpeedLimit)
                .fail(() => {
                    $(button).addClass('text-danger');
                    $(button).html('<i class="fa fa-times" aria-hidden="true"></i>');
                    console.log('Failed to set configuration');
                })
                .done(response => {
                    $(button).addClass('text-success');
                })
                .always(() => {
                    setTimeout(() => {
                        $(button).html(buttonText)
                        $(button).removeClass('text-success text-danger');
                    }, FETCH_INTERVAL);
                });
        }
        
        function setMaxParallelDownloads(button) {
            const buttonText = $(button).html();
            const maxParallelDownloads = {maxParallelDownloads: $('#max-downloads').val()};
            callPyLoadOverlayApi('POST', 'setMaxParallelDownloads', maxParallelDownloads)
                .fail(() => {
                    $(button).addClass('text-danger');
                    $(button).html('<i class="fa fa-times" aria-hidden="true"></i>');
                    console.log('Failed to set configuration');
                })
                .done(response => {
                    $(button).addClass('text-success');
                })
                .always(() => {
                    setTimeout(() => {
                        $(button).html(buttonText)
                        $(button).removeClass('text-success text-danger');
                    }, FETCH_INTERVAL);
                });
        }

        function displayCurrentDownloads() {
            $('#current-downloads').html(currentDownloads.reduce((html, currentDownload) => {
                return `${html}
                    <div class="download-status rounded mb-2 pb-3 pl-1 pr-1">
                        <button class="btn btn-link abort-file pt-0 pb-0" onclick="abortFile(${currentDownload.fid})"><i class="fa fa-times" aria-hidden="true"></i></button>
                        <span class="file-name" data-toggle="tooltip" data-placement="top" title="${currentDownload.packageName}">${currentDownload.name}</span>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: ${currentDownload.percent}%;" aria-valuenow="${currentDownload.percent}" aria-valuemin="0" aria-valuemax="100">${currentDownload.percent}%</div>
                        </div>
                        <small class="float-right">${((currentDownload.size - currentDownload.bleft) / GB_DIVIDER).toFixed(2)}/${(currentDownload.size / GB_DIVIDER).toFixed(2)} GB | ${currentDownload.format_eta}</small>
                    </div>
                `
            }, ''));
        }

        function abortFile(fileId) {
            callPyLoadOverlayApi('POST', 'abortDownload', {fileId})
                .fail(() => {
                    console.log('Failed to abort download');
                })
                .done(response => {
                    console.log('File download aborted');
                });
        }

        function fetchQueue() {
            callPyLoadOverlayApi('GET', 'getQueue')
                .fail(() => {
                    console.log('Failed to get queue');
                })
                .done(response => {
                    let links = '<ul style="list-style-type: none; padding-left: 0;">';
                    response.data.queue.forEach(package => {
                        package.links.forEach(link => {
                            const currentFile = currentDownloads.filter(d => d.fid === link.fid);
                            const startFileButtonDisabled = currentFile && currentFile.length === 1 && currentFile[0].speed > 0 ? 'disabled' : '';
                            links += `
                                <li>
                                    <button class="btn btn-link restart-file pt-0 pb-1 pr-0 pl-0" onclick="startFile(${link.fid}, this)" ${startFileButtonDisabled}><i class="fas fa-redo"></i></button> |
                                    <button class="btn btn-link abort-file pt-0 pb-1 pl-0 pr-0" onclick="deleteFile(${link.fid}, this)"><i class="fas fa-trash-alt"></i></button>
                                    <a href="${link.url}" target="_blank">${link.name}</a>
                                    <span class="download-status rounded pl-1 pr-1">${(link.size / GB_DIVIDER).toFixed(2)} GB - <span class="${MAP_DL_STATUS_CSS_COLOR[link.status]}">${link.statusmsg}</span></span>
                                </li>
                            `
                        });
                    });
                    links += '</ul>';
                    $('#queue').html(links);
                });
        }

        function startFile(fileId, button) {
            callPyLoadOverlayApi('POST', 'restartDownload', {fileId})
                .fail(() => {
                    $(button).addClass('text-danger');
                    console.log('Failed to restart file');
                })
                .done(response => {
                    console.log('File restarted');
                })
                .always(() => {
                    setTimeout(() => {
                        $(button).removeClass('text-danger');
                    }, FETCH_INTERVAL);
                });
        }

        function deleteFile(fileId, button) {
            callPyLoadOverlayApi('POST', 'deleteFile', {fileId})
                .fail(() => {
                    $(button).addClass('text-danger');
                    console.log('Failed to delete file');
                })
                .done(response => {
                    console.log('File deleted');
                })
                .always(() => {
                    setTimeout(() => {
                        $(button).removeClass('text-danger');
                    }, FETCH_INTERVAL);
                });
        }
    </script>
</div>