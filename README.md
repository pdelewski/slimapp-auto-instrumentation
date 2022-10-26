# QuoteService with AutoInstrumentation

This repo contains a slightly modified version of opentelemetry demo quoteservice.
It handles get instead of post request.

In order to run it you have to pull following changes:

`https://github.com/open-telemetry/opentelemetry-php-contrib/pull/81/`

and modify url of repository in `composer.json` to valid path on your system

```
    "repositories": [
        {
            "type": "path",
            "url": "../../slim-auto-instrumentation/opentelemetry-php-contrib/src/Instrumentation/Slim",
            "options": {
                "symlink": false
            }
        },
        {
            "type": "path",
            "url": "../../slim-auto-instrumentation/opentelemetry-php-contrib/src/Instrumentation/Psr15",
            "options": {
                "symlink": false
            }
        }
    ],
```

add packages to `require` section

```
    "open-telemetry/opentelemetry-auto-slim": "@dev",
    "open-telemetry/opentelemetry-auto-psr15": "@dev",
```

install dependencies by
```
    composer install
```

After that, open `http://localhost:8090/getquote` in your browser.

![Alt text](trace.png?raw=true "Trace")

