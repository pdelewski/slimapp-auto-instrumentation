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
        }
    ],
```


After that, open `http://localhost:8090/getquote` in your browser.

