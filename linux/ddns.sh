#!/bin/bash

PUBLIC_URL='http://your.domain.com/ddns/'
PERSONAL_KEY='YOUR_PERSONAL_KEY'

DDNS_RESULT=`wget -qO- "$PUBLIC_URL/?key=$PERSONAL_KEY"`
echo [`date +"%Y-%m-%d %H:%M:%S"`] $DDNS_RESULT >> /var/log/ddns.log