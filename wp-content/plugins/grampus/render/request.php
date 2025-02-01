<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?=GSE()::trans($type)?></title>
</head>
<body style="margin:0;padding:0;background-color:#f2f2f2;font-size:14px;">
	<div style="box-sizing:border-box;display:inline-block;width:100%;"></div>
	<div style="box-sizing:border-box;border:1px solid rgba(0,0,0,0.05);margin:30px;background-color:#fff;padding:0;">
		<div style="box-sizing:border-box;width:100%;background-color:#3a3d42;padding:20px;">
			<h1 style="box-sizing:border-box;margin:0;color:#fff;font-size:15px;font-weight:600;text-transform:uppercase;"><?=GSE()::trans($type)?></h1>
		</div>
		<div style="padding:20px;">
			<div style="box-sizing:border-box;border:1px solid rgba(0,0,0,0.05);border-radius:2px;margin:10px 0 20px 0;background-color:#fff;padding:0;">
				<div style="padding:10px;">
					<?php
						foreach ($data as $key => $value)
						{
							if(!is_array($value))
							{
					?>
						<div style="margin:5px 0;">
							<strong style="display:block;"><?=GSE()::trans($key)?>:</strong>
							<?=GSE()::wrap($value)?>
						</div>
					<?php
							}
							else
							{
					?>
						<div style="margin:5px 0;">
							<strong style="display:block;"><?=GSE()::trans($key)?>:</strong>
							<?php foreach($value as $_v)
							{
								echo GSE()::wrap($_v);
							}
							?>
						</div>
					<?php
							}
						}
					?>
				</div>
			</div>
		</div>
	</div>
	<div style="box-sizing:border-box;display:inline-block;width:100%;"></div>
</body>
</html>