#!/bin/bash
while ! nc -z project-amqp 5672; do sleep 3; done
