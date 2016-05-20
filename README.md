PHP ProcessManager for Request-Response Applications
====================================================

PHP-PM is a process manager for Request-Response Frameworks running in a ReactPHP environment.
The approach of this is to kill the expensive bootstrap of PHP (declaring symbols) and bootstrap of feature-rich frameworks.

More information can be found in the article: [Bring High Performance Into Your PHP App (with ReactPHP)](http://marcjschmidt.de/blog/2014/02/08/php-high-performance.html)

### Command

```bash
./bin/ppm start --help
Usage:
 start [--bridge[="..."]] [--host[="..."]] [--slave-host[="..."]] [--slave-port-offset[="..."]] [--master-port[="..."]] [--port[="..."]] [--workers[="..."]] [--app-env[="..."]] [--bootstrap[="..."]] [working-directory]

Arguments:
 working-directory     The root of your appplication. (default: "./")

Options:
 --bridge              The bridge we use to convert a ReactPHP-Request to your target framework. (default: "HttpKernel")
 --host                Load-Balancer host. (default: "127.0.0.1")
 --slave-host          Slave processes host. (default: "127.0.0.1")
 --slave-port-offset   Slave processes port offset. (default: 5501)
 --master-port         Master process port. (default: 5500)
 --port                Load-Balancer port. (default: 8080)
 --workers             Worker count. Default is 8. Should be minimum equal to the number of CPU cores. (default: 8)
 --app-env             The environment that your application will use to bootstrap (if any) (default: "dev")
 --bootstrap           The class that will be used to bootstrap your application (default: "PHPPM\\Bootstraps\\Symfony")
 --help (-h)           Display this help message
 --quiet (-q)          Do not output any message
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version
 --ansi                Force ANSI output
 --no-ansi             Disable ANSI output
 --no-interaction (-n) Do not ask any interactive question
```

### Example

```bash
$ ./bin/ppm start ~/my/path/to/symfony/ --bridge=httpKernel
```

Each worker starts its own HTTP Server which listens on port 5501, 5502, 5503 etc. Default port range is `5501 -> 5500+<workersCount>`.
Worker port offset could be changed with `--slave-port-offset` param.

### Setup 1. Use external Load-Balancer

![ReactPHP with external Load-Balancer](doc/reactphp-external-balancer.jpg)

Example config for NGiNX:

```nginx
upstream backend  {
    server 127.0.0.1:5501;
    server 127.0.0.1:5502;
    server 127.0.0.1:5503;
    server 127.0.0.1:5504;
    server 127.0.0.1:5505;
    server 127.0.0.1:5506;
}

server {
    root /path/to/symfony/web/;
    server_name servername.com;
    location / {
        try_files $uri @backend;
    }
    location @backend {
        proxy_pass http://backend;
    }
}
```

### Setup 2. Use internal Load-Balancer

This setup is slower as we can't load balance incoming connections as fast as NGiNX it does,
but it's perfect for testing purposes.

![ReactPHP with internal Load-Balancer](doc/reactphp-internal-balancer.jpg)
