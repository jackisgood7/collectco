<?php
return [
    'message' => [
        'wrong_credentials' => 'Invalid Credentials',
        'invalid_format' => 'Invalid Format',
        'collection_unavailable' => 'One of the collection has been removed or trade to other users'
    ],
    'responseCode' => [
        'success' => 0,
        'fail' => 1
    ],
    'collection_types' => [
        '1' => array(
            'name' => 'Books',
            'mime' => ['*']
        ),
        '2' => array(
            'name' => 'Music',
            'mime' => ['audio/mpeg','audio/3gpp','audio/midi','audio/x-midi']
        ),
        '3' => array(
            'name' => 'Files',
            'mime' => ['*']
        ),
        '4' => array(
            'name' => 'Images',
            'mime' => ['image/jpeg','image/png']
        )
    ]
];