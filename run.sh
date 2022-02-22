#!/usr/bin/env bash

# run cmd
exec /usr/local/bin/rr serve -c /etc/roadrunner/.rr.yaml 3>&1
