<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$quotes = [
    [
        "_id" => "CtXK_S3aJG",
        "content" => "Imagination allows us to escape the predictable. It enables us to reply to the common wisdom that we cannot soar by saying, 'Just watch!'",
        "author" => "Bill Bradley",
        "tags" => ["Wisdom"],
        "authorSlug" => "bill-bradley",
        "length" => 137,
        "dateAdded" => "2020-10-14",
        "dateModified" => "2023-04-14"
    ],
    [
        "_id" => "A1B2C3D4E5",
        "content" => "The only way to do great work is to love what you do.",
        "author" => "Steve Jobs",
        "tags" => ["Motivation"],
        "authorSlug" => "steve-jobs",
        "length" => 52,
        "dateAdded" => "2019-05-21",
        "dateModified" => "2023-04-14"
    ],
    [
        "_id" => "F6G7H8I9J0",
        "content" => "Success is not final, failure is not fatal: It is the courage to continue that counts.",
        "author" => "Winston Churchill",
        "tags" => ["Success"],
        "authorSlug" => "winston-churchill",
        "length" => 87,
        "dateAdded" => "2018-11-30",
        "dateModified" => "2023-04-14"
    ],
    [
        "_id" => "K1L2M3N4O5",
        "content" => "Happiness is not something ready made. It comes from your own actions.",
        "author" => "Dalai Lama",
        "tags" => ["Happiness"],
        "authorSlug" => "dalai-lama",
        "length" => 67,
        "dateAdded" => "2021-02-10",
        "dateModified" => "2023-04-14"
    ],
    [
        "_id" => "P6Q7R8S9T0",
        "content" => "In the middle of difficulty lies opportunity.",
        "author" => "Albert Einstein",
        "tags" => ["Opportunity"],
        "authorSlug" => "albert-einstein",
        "length" => 44,
        "dateAdded" => "2020-07-18",
        "dateModified" => "2023-04-14"
    ],
    [
        "_id" => "U1V2W3X4Y5",
        "content" => "What you get by achieving your goals is not as important as what you become by achieving your goals.",
        "author" => "Zig Ziglar",
        "tags" => ["Goals"],
        "authorSlug" => "zig-ziglar",
        "length" => 97,
        "dateAdded" => "2017-09-12",
        "dateModified" => "2023-04-14"
    ],
    [
        "_id" => "Z6A7B8C9D0",
        "content" => "The best way to predict the future is to invent it.",
        "author" => "Alan Kay",
        "tags" => ["Future"],
        "authorSlug" => "alan-kay",
        "length" => 48,
        "dateAdded" => "2016-03-22",
        "dateModified" => "2023-04-14"
    ],
    [
        "_id" => "E1F2G3H4I5",
        "content" => "You miss 100% of the shots you don’t take.",
        "author" => "Wayne Gretzky",
        "tags" => ["Motivation"],
        "authorSlug" => "wayne-gretzky",
        "length" => 41,
        "dateAdded" => "2015-08-05",
        "dateModified" => "2023-04-14"
    ],
    [
        "_id" => "J6K7L8M9N0",
        "content" => "It does not matter how slowly you go as long as you do not stop.",
        "author" => "Confucius",
        "tags" => ["Perseverance"],
        "authorSlug" => "confucius",
        "length" => 62,
        "dateAdded" => "2014-12-01",
        "dateModified" => "2023-04-14"
    ],
    [
        "_id" => "O1P2Q3R4S5",
        "content" => "Believe you can and you’re halfway there.",
        "author" => "Theodore Roosevelt",
        "tags" => ["Belief"],
        "authorSlug" => "theodore-roosevelt",
        "length" => 41,
        "dateAdded" => "2013-06-17",
        "dateModified" => "2023-04-14"
    ]
];

$randomQuote = $quotes[random_int(0, count($quotes) - 1)];

echo json_encode($randomQuote);
