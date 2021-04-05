<div id="debug-variables">
	<div class="row">
        <h1>Variables</h1>
    </div>
	<div class="row">
		<div class="col">
			<details>
				<summary>Session</summary>
				<code>
					<pre><?php print_r($_SESSION); ?></pre>
				</code>
			</details>
		</div>
		<div class="col">
			<details>
				<summary>Cookies</summary>
				<code>
					<pre><?php print_r($_COOKIE); ?></pre>
				</code>
			</details>
		</div>
	</div>
	<div class="row">
		<div class="col">
			<details>
				<summary>Server</summary>
				<code>
					<pre><?php print_r($_SERVER); ?></pre>
				</code>
			</details>
		</div>
	</div>
	<div class="row">
		<div class="col">
			<details>
				<summary>Ini</summary>
				<code>
					<pre><?php print_r(ini_get_all()); ?></pre>
				</code>
			</details>
		</div>
	</div>
</div>