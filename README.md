### RUN LOCAL DEVELOPMENT
- copy .env.example to .env
- run `docker-compose up -d`
- enter bash container `docker exec -it <nama container - docker ps> bash`
- run migration `php artisan migrate`
- run permission synch config `php artisan permission:config-synch`

### INITIAL ADMIN ACCESS COMMAND
- Access docker container `docker exec -it app bash`
- `php artisan admin:initial-access`


### Development Front End (Vue JS)
- Due to lack of performance, run this command outside docker/container. You need install node js on your host
- First install dependency npm `yarn`
- `yarn watch`
- auto refresh browser will open to 3001
