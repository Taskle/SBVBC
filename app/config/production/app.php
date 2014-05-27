<?php

/** Add Cloudflare proxies (see https://www.cloudflare.com/ips)
 *  so we get secure = true persisting
 */
Request::setTrustedProxies(array(
    '199.27.128.0/21',
	'173.245.48.0/20',
	'103.21.244.0/22',
	'103.22.200.0/22',
	'103.31.4.0/22',
	'141.101.64.0/18',
	'108.162.192.0/18',
	'190.93.240.0/20',
	'188.114.96.0/20',
	'197.234.240.0/22',
	'198.41.128.0/17',
	'162.158.0.0/15',
	'104.16.0.0/12'
));

return array(
	'debug' => false,
	'ssl' => true
);
