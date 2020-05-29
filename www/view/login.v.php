<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header"><h3 class="text-center font-weight-light my-4">Login</h3></div>
                <div class="card-body">
                    <form id="login-form" method="post">
                        <div class="form-group">
							<label class="small mb-1" for="<?=USER?>">Username</label>
							<input type="text" id="<?=USER?>" class="form-control py-4" name="<?=USER?>" placeholder="<?=USER?>" autofocus>
						</div>
                        <div class="form-group">
							<label class="small mb-1" for="<?=PASSWORD?>">Password</label>
							<input type="password" id="<?=PASSWORD?>" class="form-control py-4" name="<?=PASSWORD?>" placeholder="<?=PASSWORD?>">
						</div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
								<input type="checkbox" class="form-check-input" id="<?=STAY_CONNECTED?>" name="<?=STAY_CONNECTED?>" checked>
								<label class="form-check-label" for="<?=STAY_CONNECTED?>">Stay connected <small>(for <?=getConfig(SESSION_TIMEOUT)?> minutes)</small></label>
							</div>
                        </div>
                        <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
							<input type="submit" class="btn btn-primary form-control" value="Log in" onclick="login(event)">
						</div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <div class="small">Contact administrator to get an account</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    async function login(event) {
        event.preventDefault();
        const formData = new FormData();
		formData.append('<?=USER?>', $('#<?=USER?>').val());
		formData.append('<?=PASSWORD?>', await sha256($('#<?=PASSWORD?>').val()));
        formData.append('<?=STAY_CONNECTED?>', $('#<?=STAY_CONNECTED?>').is(':checked'));
        fetch('api/login/', {
					method: 'POST',
					body: formData
		}).then(response => {
            if (response.ok) {
                location.reload();
            } else {
                $('#<?=PASSWORD?>').addClass('is-invalid')
            }
        });
    }

    async function sha256(message) {
        const msgBuffer = new TextEncoder('utf-8').encode(message);
        const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        const hashHex = hashArray.map(b => ('00' + b.toString(16)).slice(-2)).join('');
        return hashHex;
    }
</script>