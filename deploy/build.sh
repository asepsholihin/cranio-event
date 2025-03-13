#!/bin/bash
aws ecr get-login-password --region ap-southeast-1 | docker login --username AWS --password-stdin 667803110472.dkr.ecr.ap-southeast-1.amazonaws.com
docker build --platform linux/amd64 -t platform-sistem-jejakimani-prod -f deploy/Dockerfile .

docker tag platform-sistem-jejakimani-prod 667803110472.dkr.ecr.ap-southeast-1.amazonaws.com/platform-sistem-jejakimani-prod
docker push 667803110472.dkr.ecr.ap-southeast-1.amazonaws.com/platform-sistem-jejakimani-prod
