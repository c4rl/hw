#! /bin/bash

./vendor/phpspec/phpspec/bin/phpspec run
./reset.sh
./node_modules/mocha/bin/mocha spec_api/muppet_spec.js
