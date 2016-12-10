#! /bin/bash

for file in migration/*.sql
do
  cat $file | sqlite3 storage/db.sqlite
done
