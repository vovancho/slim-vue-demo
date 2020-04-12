#!/bin/bash

status_code=$(curl -XGET -I --write-out %{http_code} --silent --output /dev/null 'http://localhost:9200/_ilm/policy/project_policy')
attempt=1

printf "\nStatus is $status_code\n"

until [[ "$status_code" == "200" || "$status_code" == "404" || $attempt -gt 5 ]]; do
 printf "Attempt: $attempt. Elastic Search is unavailable - waiting 10 seconds\n"
 sleep 10
 status_code=$(curl -XGET -I --write-out %{http_code} --silent --output /dev/null 'http://localhost:9200/_ilm/policy/project_policy')
 printf "Status is $status_code.\n"
 ((attempt++))
done

if [[ "$status_code" -ge 404 ]] ; then
 sleep 5
 printf "Try add policy\n"
 curl -XPUT -H 'Content-Type: application/json' 'http://localhost:9200/_ilm/policy/project_policy' -d@/tmp/logstash/project_policy_template.json
 printf "\n"
else
 printf "Status not equal 404. Status is $status_code\n"
fi
