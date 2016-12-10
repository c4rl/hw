#! /bin/bash

./migrate.sh
./node_modules/mocha/bin/mocha spec_api/muppet_spec.js
