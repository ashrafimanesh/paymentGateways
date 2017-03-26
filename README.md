# paymentGateways
php payment gateways libraries

Usage:
1: Read src/config/app.php

	If your project has env function can define config:

		1-1: define payment_gateway_logPath in env
		1-2: define payment_gateway_logDateFormat in env
		1-3: define payment_gateway_proxy_enable in env
		1-4: define payment_gateway_proxy_type in env
		1-5: define payment_gateway_proxy_curlProxyAddress in env
		1-6: define payment_gateway_proxy_soapProxyAddress in env

2: See src/samples

    2-1: update vendor/autoload.php path in src/samples/loader.php

    2-2: src/samples/pay.php

    2-3: src/samples/confirm.php
