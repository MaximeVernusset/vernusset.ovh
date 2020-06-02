<div class="row">
    <h1>Collector</h1>
</div>
<div class="row">
    <form id="collector-form" class="w-100">
        <div class="form-group">
            <textarea id="<?=LINKS?>" class="form-control" name="<?=LINKS?>" placeholder="<?=LINKS?>" rows="<?=count($links)?>" autofocus><?=join(PHP_EOL, $links)?></textarea>
            <small class="form-text text-muted">List of download links (<code>http(s)://...</code>)</small>
        </div>
        <input type="submit" id="submitButton" class="btn btn-primary" value="Submit" onclick="postLinks(event)">
    </form>
</div>
<div class="row">
    <p id="toast" class="text-muted"></p>
</div>

<script>
    function postLinks(event) {
        event.preventDefault();
        const submitButton = $('#submitButton');
        $.post('api/pyLoadOverlay/collect/', $('#collector-form').serialize(), 'json')
            .fail((response) => {
                const toastSection = $('#toast');
                toastSection.text(response.statusText);
                submitButton.addClass('btn-danger');
                setTimeout(() => {
                    submitButton.removeClass('btn-danger');
                    toastSection.empty();
                }, 2500);
            })
            .done((response) => {
                submitButton.addClass('btn-success');
                setTimeout(() => {
                    document.location = 'index.php?action=pyload/monitor';
                }, 750);
            });
    }
</script>