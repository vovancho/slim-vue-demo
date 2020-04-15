#!/bin/bash
while ! nc -z amqp 5672; do sleep 3; done
