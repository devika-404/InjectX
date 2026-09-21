<?php

require_once 'db.php';


/* =========================================
   GET SCAN ID
========================================= */

$scan_id = $_GET['scan_id'] ?? null;

if (!$scan_id) {

    header('Location: index.php');
    exit();

}


/* =========================================
   FETCH SCAN DETAILS
========================================= */

$scan_stmt = $con->prepare(
    'SELECT * FROM scans WHERE id = ?'
);

$scan_stmt->execute([$scan_id]);

$scan = $scan_stmt->fetch();


if (!$scan) {

    die('Scan session not found.');

}


/* =========================================
   FETCH VULNERABILITIES
========================================= */

/*
 * IMPORTANT:
 * Vulnerabilities belong to a scan through
 * vuln.scan_id.
 */

$vuln_stmt = $con->prepare(
    'SELECT * FROM vuln WHERE scan_id = ?'
);

$vuln_stmt->execute([$scan_id]);

$vul = $vuln_stmt->fetchAll();

?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>InjectX - Scan Result</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        body {

            font-family: Arial, Helvetica, sans-serif;

            background: #080b12;

            color: #e6edf3;

            min-height: 100vh;

        }


        /* =================================
           HEADER
        ================================= */

        header {

            height: 70px;

            background: #0d1117;

            border-bottom: 1px solid #1f2937;

            display: flex;

            align-items: center;

            padding: 0 30px;

        }


        header h1 {

            font-size: 24px;

            letter-spacing: 3px;

            color: #00ff88;

        }


        /* =================================
           LAYOUT
        ================================= */

        .layout {

            display: flex;

            min-height: calc(100vh - 70px);

        }


        /* =================================
           SIDEBAR
        ================================= */

        aside {

            width: 220px;

            background: #0d1117;

            border-right: 1px solid #1f2937;

            padding: 30px 15px;

        }


        aside div {

            display: flex;

            flex-direction: column;

            gap: 8px;

        }


        aside a {

            color: #8b949e;

            text-decoration: none;

            padding: 13px 15px;

            border-radius: 6px;

            font-size: 14px;

            border: 1px solid transparent;

        }


        aside a:hover {

            color: #00ff88;

            background: #111820;

            border-color: #1f3b2e;

        }


        /* =================================
           MAIN
        ================================= */

        main {

            flex: 1;

            padding: 40px;

            max-width: 1200px;

        }


        /* =================================
           REPORT HEADER
        ================================= */

        .report-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 25px;

        }


        .report-label {

            color: #00ff88;

            font-size: 12px;

            text-transform: uppercase;

            letter-spacing: 2px;

            margin-bottom: 8px;

        }


        .report-header h2 {

            font-size: 26px;

            color: #f0f6fc;

        }


        .new-scan-btn {

            background: #00ff88;

            color: #06100b;

            padding: 12px 20px;

            border-radius: 6px;

            text-decoration: none;

            font-size: 13px;

            font-weight: bold;

        }


        .new-scan-btn:hover {

            background: #00d977;

        }


        /* =================================
           SCAN INFO
        ================================= */

        .info-card {

            background: #0d1117;

            border: 1px solid #1f2937;

            border-radius: 10px;

            padding: 25px;

            margin-bottom: 30px;

        }


        .section-title {

            color: #00ff88;

            text-transform: uppercase;

            font-size: 12px;

            letter-spacing: 2px;

            margin-bottom: 20px;

        }


        .scan-info {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 20px;

            padding-bottom: 20px;

            border-bottom: 1px solid #1f2937;

        }


        .info-label {

            display: block;

            color: #6e7681;

            font-size: 11px;

            text-transform: uppercase;

            margin-bottom: 7px;

        }


        .info-value {

            color: #e6edf3;

            font-size: 14px;

            word-break: break-all;

        }


        /* =================================
           STATUS
        ================================= */

        .status-area {

            margin-top: 22px;

        }


        .status-label {

            color: #8b949e;

            text-transform: uppercase;

            font-size: 11px;

            margin-bottom: 12px;

        }


        .status-box {

            display: flex;

            align-items: center;

            gap: 15px;

            padding: 15px;

            border-radius: 7px;

        }


        .status-box.vulnerable {

            background: #211111;

            border: 1px solid #4a2424;

        }


        .status-box.secure {

            background: #0c2118;

            border: 1px solid #1f4d35;

        }


        .status-icon {

            width: 40px;

            height: 40px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 50%;

            font-size: 20px;

            font-weight: bold;

        }


        .vulnerable .status-icon {

            background: #3b1717;

            color: #ff4d4d;

        }


        .secure .status-icon {

            background: #123d28;

            color: #00ff88;

        }


        .status-text b {

            display: block;

            margin-bottom: 5px;

        }


        .vulnerable .status-text b {

            color: #ff6b6b;

        }


        .secure .status-text b {

            color: #00ff88;

        }


        .status-text p {

            color: #8b949e;

            font-size: 13px;

        }


        /* =================================
           VULNERABILITY TITLE
        ================================= */

        .vulnerability-title {

            color: #00ff88;

            text-transform: uppercase;

            letter-spacing: 2px;

            font-size: 12px;

            margin-bottom: 15px;

        }


        /* =================================
           VULNERABILITY CARD
        ================================= */

        .vulnerability-card {

            background: #0d1117;

            border: 1px solid #3b2020;

            border-left: 3px solid #ff4d4d;

            border-radius: 8px;

            padding: 22px;

            margin-bottom: 20px;

        }


        .vulnerability-header {

            display: flex;

            justify-content: space-between;

            margin-bottom: 20px;

        }


        .vulnerability-name {

            color: #ff6b6b;

            font-weight: bold;

        }


        .vulnerability-number {

            color: #8b949e;

            margin-right: 10px;

        }


        .threat {

            color: #ff4d4d;

            font-size: 11px;

            font-weight: bold;

        }


        .detail {

            margin-bottom: 16px;

        }


        .detail-label {

            display: block;

            color: #6e7681;

            font-size: 11px;

            text-transform: uppercase;

            margin-bottom: 6px;

        }


        code {

            display: block;

            background: #080b12;

            border: 1px solid #1f2937;

            padding: 11px;

            border-radius: 5px;

            color: #ff7b72;

            word-break: break-all;

        }


        .details {

            color: #c9d1d9;

            line-height: 1.6;

            font-size: 13px;

        }


        /* =================================
           REMEDIATION GUIDE
        ================================= */

        .remediation-title {

            color: #00ff88;

            text-transform: uppercase;

            letter-spacing: 2px;

            font-size: 12px;

            margin-top: 30px;

            margin-bottom: 15px;

        }


        .remediation-card {

            background: #0d1117;

            border: 1px solid #1f2937;

            border-left: 3px solid #00ff88;

            border-radius: 8px;

            padding: 25px;

            margin-bottom: 30px;

        }


        .remediation-card p {

            color: #c9d1d9;

            line-height: 1.7;

            margin-bottom: 20px;

        }


        .code-title {

            color: #8b949e;

            font-size: 12px;

            margin-bottom: 8px;

        }


        pre {

            background: #080b12;

            border: 1px solid #1f2937;

            padding: 18px;

            border-radius: 6px;

            overflow-x: auto;

            color: #00ff88;

            line-height: 1.6;

        }


        /* =================================
           SECURE RESULT
        ================================= */

        .secure-result {

            background: #0d1117;

            border: 1px solid #1f4d35;

            border-left: 3px solid #00ff88;

            border-radius: 8px;

            padding: 30px;

            text-align: center;

        }


        .secure-result .check {

            color: #00ff88;

            font-size: 35px;

            margin-bottom: 10px;

        }


        .secure-result h3 {

            color: #00ff88;

            margin-bottom: 10px;

        }


        .secure-result p {

            color: #8b949e;

            line-height: 1.6;

        }


        @media(max-width: 800px) {

            .layout {

                flex-direction: column;

            }

            aside {

                width: 100%;

                padding: 12px 15px;

            }

            aside div {

                flex-direction: row;

            }

            main {

                padding: 20px;

            }

            .report-header {

                flex-direction: column;

                align-items: flex-start;

                gap: 20px;

            }

            .scan-info {

                grid-template-columns: 1fr;

            }

        }

    </style>

</head>


<body>


<!-- =================================
     HEADER
================================= -->

<header>

    <h1>INJECTX</h1>

</header>


<div class="layout">


<!-- =================================
     SIDEBAR
================================= -->

<aside>

    <div>

        <a href="index.php">
            New Scan
        </a>

        <a href="history.php">
            Scan History
        </a>

    </div>

</aside>


<!-- =================================
     MAIN CONTENT
================================= -->

<main>


<!-- REPORT HEADER -->

<div class="report-header">

    <div>

        <div class="report-label">
            Security Analysis
        </div>

        <h2>
            Scan Report #<?php echo htmlspecialchars($scan['id']); ?>
        </h2>

    </div>


    <a
        href="index.php"
        class="new-scan-btn"
    >
        NEW SCAN
    </a>

</div>



<!-- =================================
     SCAN INFORMATION
================================= -->

<div class="info-card">

    <div class="section-title">
        Scan Info
    </div>


    <div class="scan-info">

        <div>

            <span class="info-label">
                Target URL
            </span>

            <span class="info-value">
                <?php echo htmlspecialchars($scan['target_url']); ?>
            </span>

        </div>


        <div>

            <span class="info-label">
                Execution Date
            </span>

            <span class="info-value">
                <?php echo htmlspecialchars($scan['created_at']); ?>
            </span>

        </div>

    </div>



    <!-- STATUS -->

    <div class="status-area">

        <div class="status-label">
            Overall Status
        </div>


        <?php if ($scan['total_vuln'] > 0): ?>

            <div class="status-box vulnerable">

                <div class="status-icon">
                    !
                </div>

                <div class="status-text">

                    <b>
                        Vulnerable
                    </b>

                    <p>
                        <?php echo $scan['total_vuln']; ?>
                        threat(s) detected
                    </p>

                </div>

            </div>

        <?php else: ?>

            <div class="status-box secure">

                <div class="status-icon">
                    ✓
                </div>

                <div class="status-text">

                    <b>
                        Secure
                    </b>

                    <p>
                        No SQL injection vulnerabilities detected
                    </p>

                </div>

            </div>

        <?php endif; ?>

    </div>

</div>



<!-- =================================
     VULNERABILITY RESULTS
================================= -->

<?php if (!empty($vul)): ?>


    <div class="vulnerability-title">
        Detected Vulnerabilities
    </div>


    <?php foreach ($vul as $index => $vulnerability): ?>


        <div class="vulnerability-card">


            <div class="vulnerability-header">

                <div>

                    <span class="vulnerability-number">
                        #<?php echo $index + 1; ?>
                    </span>

                    <span class="vulnerability-name">

                        <?php
                        echo htmlspecialchars(
                            $vulnerability['sqli_type']
                        );
                        ?>

                        SQL Injection

                    </span>

                </div>


                <span class="threat">
                    THREAT DETECTED
                </span>

            </div>



            <!-- PARAMETER -->

            <div class="detail">

                <span class="detail-label">
                    Vulnerable Parameter
                </span>

                <code>
                    <?php
                    echo htmlspecialchars(
                        $vulnerability['para_name']
                    );
                    ?>
                </code>

            </div>



            <!-- PAYLOAD -->

            <div class="detail">

                <span class="detail-label">
                    Payload Triggered
                </span>

                <code>
                    <?php
                    echo htmlspecialchars(
                        $vulnerability['payload']
                    );
                    ?>
                </code>

            </div>



            <!-- DETAILS -->

            <div class="detail">

                <span class="detail-label">
                    Detection Details
                </span>

                <p class="details">

                    <?php
                    echo htmlspecialchars(
                        $vulnerability['details']
                    );
                    ?>

                </p>

            </div>


        </div>


    <?php endforeach; ?>



    <!-- =================================
         REMEDIATION GUIDE
    ================================= -->

    <div class="remediation-title">
        Remediation Guide
    </div>


    <div class="remediation-card">

        <p>

            Replace raw dynamic SQL query concatenation
            with PHP PDO prepared statements to prevent
            SQL injection attacks.

        </p>


        <div class="code-title">
            Secure PDO Implementation
        </div>


        <pre>$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_GET['id']]);
$user = $stmt->fetch();</pre>

    </div>


<?php else: ?>


    <!-- =================================
         NO VULNERABILITY
    ================================= -->

    <div class="secure-result">

        <div class="check">
            ✓
        </div>

        <h3>
            No Vulnerabilities Found
        </h3>

        <p>

            The scanner tested the URL parameters against
            SQL Injection payloads and found no detectable
            SQL injection vulnerabilities.

        </p>

    </div>


<?php endif; ?>


</main>

</div>

</body>

</html>