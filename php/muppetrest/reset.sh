#! /bin/bash

cat schema.sql | sqlite3 storage/db.sqlite
cat data.sql | sqlite3 storage/db.sqlite
