    php artisan key:generate
    composer install
    php artisan migrate
    php artisan serve
    php artisan websocket:serve



.env config:

    PUSHER_APP_ID=yahapappid
    PUSHER_APP_KEY=D4C11397CF5822DDA8516843BFE7AE0944E36A01
    PUSHER_APP_SECRET=yahayappsecret
    PUSHER_HOST=192.168.100.3
    PUSHER_PORT=6001
    PUSHER_SCHEME=http
    PUSHER_APP_CLUSTER=mt1
    PUSHER_ENCRYPTED=false
    PUSHER_USETLS=false