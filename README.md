TLS Downgrade Webproxy
======================

Simple docker-compose setup which creates a webproxy for curl/soap (etc.) requests which require further identification using client's certificate with support for TLS v1.0 (configurable in `config/openssl.cnf`).

Useful in case you need to debug connection to a SOAP Server running and accepting only older TLS client certificates while you already require a newer minimum version of the TLS on your development server.

# Setup

First of all you will need to have Docker and Docker-compose installed.
Then prepare the `.env` file, set values of variables:

* __PROXY_TO__ which will be used for the final url replacements (explained below)
* __PORT__ listening port of the docker virtual host
* Optional __PASSPHRASE__ if your certificate requires it

Then copy your certificate as `cert.pem` to `cert/` folder. Be sure to include a certifiate which includes the key and all CAs' paths.

Run the proxy using `docker-compose up`.

# Client setup

If the final webservice is under the url:
https://webservices.com/best/webservice

and you PORT is 82 then set your client's setup as follows (if using PHP):

(this assumes the WSDL is public, you can of course use one from a file)

```php
<?php
$soap = new SoapClient('http://localhost:82/best/webservice?WSDL', [
    'location' => 'http://localhost:82/'
]);
```

and set `PROXY_TO` to `https://webservices.com` (you just need to provide the scheme and domain, without trailing slash, the request uri will be passed-through).

# Warning

Such approach should never be used in any live/production environment: for testing and development purposes only!