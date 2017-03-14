<?php

use Laravel\Forge\Forge;

require_once('vendor/autoload.php');

$apiKey = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjAyNTcwMTM5ODcxMTg0YWI0YTU4ODQwZWQ5YzRhZjExZTg4ZDcxODJkZWFlY2U5ZmY4ODI3YjRkYzRjNDE1NWYxNTVlMzYxOTRmYTYxNmVjIn0.eyJhdWQiOiIxIiwianRpIjoiMDI1NzAxMzk4NzExODRhYjRhNTg4NDBlZDljNGFmMTFlODhkNzE4MmRlYWVjZTlmZjg4MjdiNGRjNGM0MTU1ZjE1NWUzNjE5NGZhNjE2ZWMiLCJpYXQiOjE0ODE4MjcwODEsIm5iZiI6MTQ4MTgyNzA4MSwiZXhwIjoxNzk3MzU5ODgxLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.ISAkfCY6b_H5fQnP6kmGcMNlX_pd6t5PJxIlkAHcKkNhTk9O_21dj98zy6hZmSYdbwNzd4zHx9IiGUzMWVVtXSmn1IrSANnZlqbObsoqXjyIf4tH7gYm7FbqADi6R1bppI7hd9qSPvOmv9YCnn-qFv2qMjxLO8oacFOZD7nigJv0N-clOgFP_qZS08ItiuRU6XCve2sX79XhFT4N8k0SxmyLZxlE7b7JOGQnn4yNiXa9Lsp2-GHTMf_faHT5hzdBeh2I8SVt1g2H9j4Xr4AgItZCvYaDLbuz_1uvS6tq8jPEZsVKBYOKXXVD2HcWCrkPOJQCfxmshhiZOSXzBleAGvNLS2hFrozSuQapTScKSdm2_7taNJdWErPiBfcMx1XMT9CGxJAmD4PN9Iw78C8H87m1LiNuujd3bUqVti48E2tuS12syP9OUCkJ7TBQJDVkvgThCgnVQpAqGmzncYCXq8sa3soYfGM-6SsFb6p8xWxGY8rsbwY6fJ5YQsbLZ8L2WS8ECuGNhDlaqtpmjWQJ56AjMqEqrodtWOHLeyRt6VWBfIM0m2nDGq8nFhlko7h6jbw11ijI2jd8NtvQReZUXWgefMqvir1gVa3hAP7jvU67muDKLjMkQXy9mhdzj46XdbqU12vyrenljigkVH_Drqk2kfmWZqaO3LD3v8o-f9I';

$guzzle = new \GuzzleHttp\Client([
    'base_uri' => 'http://forge.dev/api/v1/',
    'http_errors' => false,
    'headers' => [
        'Authorization' => 'Bearer '.$apiKey,
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
    ]
]);

$forge = new Forge($apiKey, $guzzle);

echo '<pre>';

//try {

var_dump(
    $forge->createSSHKey(1, [
        'name' => 'test',
        'key' => 'test'
    ], true)
);

//} catch (Exception $e) {
//    var_dump($e->getMessage());
//}

echo '</pre>';