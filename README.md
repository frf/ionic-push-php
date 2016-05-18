# Ionic push php sdk

This package may be helpful for sending ionic push notifications, the fork version v2 ionic.io the https://github.com/dmitrovskiy/ionic-push-php

## Install

Via Composer

``` bash
$ composer require fabiorf/ionic-push-php
```

## Usage
## New feature -- v2 ionic.io push
``` php
$pusher = new Dmitrovskiy\IonicPush\PushProcessor(
    'APP_ID',
    'API_TOKEN',
    'API_PROFILE'
);

$devices = array(
    //...
);

$notification = [
                    'message'=>"Hello world!",
                    'title'=>'Hi',
                    'android'=>['message'=>"Hello world!",
                                'title'=>'Hi']
                ];

$pusher->notify($devices, $notification);
```

```
JSON Exemple

{
  "tokens": ["DEV-49e60667-a91f-40ee-b3b0-e11d8bc55384"],
 ## "profile": "fake_push_profile",
  "notification": {
    "title": "Hi",
  ##  "message": "Hello world!",
    "android": {
      "title": "Hey",
      "message": "Hello Android!"
    }
  }
}
```
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
