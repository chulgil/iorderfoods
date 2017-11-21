#!/bin/bash
NOW=$(date +"%Y-%m-%d %H:%M")
echo "----------------------------------------------------"
echo $NOW " Reminder push notification- Cronjob"

curl -f -s -S -k https://super.iorderfoods.com/CronJobs/bookingReminder
curl -f -s -S -k https://super.iorderfoods.com/CronJobs/orderReminder

