#!/bin/sh
composer config minimum-stability dev
composer require open-telemetry/sdk:1.0.0beta1 --with-all-dependencies 
composer require php-http/guzzle7-adapter --with-all-dependencies
composer require open-telemetry/api:1.0.0beta1 --with-all-dependencies
composer require open-telemetry/sdk-contrib:1.0.0beta1 --with-all-dependencies   
pickle install --source https://github.com/open-telemetry/opentelemetry-php-instrumentation.git#1.0.0beta2
composer require open-telemetry/opentelemetry-auto-slim:1.0.0beta3 --with-all-dependencies
composer require open-telemetry/opentelemetry-auto-psr15:1.0.0beta2 --with-all-dependencies
composer require open-telemetry/opentelemetry-auto-psr18:1.0.0beta2 --with-all-dependencies
