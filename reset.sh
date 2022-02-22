#!/usr/bin/env bash
# RoadRunner v2 resets workers asynchronously and this will help to create watcher in phpstorm
/usr/local/bin/rr reset http -c /etc/roadrunner/.rr.yaml &
