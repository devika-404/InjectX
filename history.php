<?php
    require_once 'db.php';

    $stmt=$con->query('select * from scans order by created_at desc');
    $scans=$stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>InjectX-Scan History</title>

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

        /* HEADER */

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
            font-weight: 700;
        }

        /* LAYOUT */

        .layout {
            display: flex;
            min-height: calc(100vh - 70px);
        }

        /* SIDEBAR */

        aside {
            width: 220px;
            background: #0d1117;
            border-right: 1px solid #1f2937;
            padding: 30px 15px;
        }

        aside > div {
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
            transition: 0.2s ease;
        }

        aside a:hover {
            color: #00ff88;
            background: #111820;
            border-color: #1f3b2e;
        }

        aside a.active {
            color: #00ff88;
            background: #111820;
            border-color: #1f3b2e;
        }

        /* MAIN */

        main {
            flex: 1;
            padding: 40px;
            overflow-x: auto;
        }

        /* PAGE HEADER */

        .page-header {
            margin-bottom: 30px;
        }

        .page-label {
            color: #00ff88;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        .page-header h2 {
            color: #f0f6fc;
            font-size: 27px;
            margin-bottom: 10px;
        }

        .page-header p {
            color: #8b949e;
            font-size: 14px;
        }

        /* TABLE CARD */

        .history-card {
            background: #0d1117;
            border: 1px solid #1f2937;
            border-radius: 10px;
            overflow: hidden;
            min-width: 800px;
        }

        /* TABLE */

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #111820;
        }

        th {
            color: #00ff88;
            text-align: left;
            padding: 16px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid #1f2937;
            white-space: nowrap;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #1f2937;
            color: #c9d1d9;
            font-size: 13px;
        }

        tbody tr {
            transition: 0.2s ease;
        }

        tbody tr:hover {
            background: #111820;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        /* TARGET URL */

        .target-url {
            max-width: 300px;
            word-break: break-all;
            color: #e6edf3;
        }

        /* SCAN ID */

        .scan-id {
            color: #8b949e;
        }

        /* DATE */

        .date {
            color: #8b949e;
            white-space: nowrap;
        }

        /* STATUS */

        .status-completed {
            display: inline-block;
            color: #00ff88;
            background: #0c2118;
            border: 1px solid #1f4d35;
            padding: 5px 9px;
            border-radius: 5px;
            font-size: 11px;
            text-transform: uppercase;
        }

        /* VULNERABILITY COUNT */

        .vuln-found {
            color: #ff6b6b;
            background: #211111;
            border: 1px solid #4a2424;
            padding: 5px 9px;
            border-radius: 5px;
            font-size: 11px;
        }

        .secure {
            color: #00ff88;
            background: #0c2118;
            border: 1px solid #1f4d35;
            padding: 5px 9px;
            border-radius: 5px;
            font-size: 11px;
        }

        /* VIEW REPORT */

        .view-report {
            display: inline-block;
            color: #00ff88;
            text-decoration: none;
            border: 1px solid #1f4d35;
            padding: 7px 11px;
            border-radius: 5px;
            font-size: 11px;
            text-transform: uppercase;
            transition: 0.2s ease;
        }

        .view-report:hover {
            background: #0c2118;
            border-color: #00ff88;
        }

        /* EMPTY STATE */

        .empty-state {
            background: #0d1117;
            border: 1px solid #1f2937;
            border-radius: 10px;
            padding: 50px 30px;
            text-align: center;
        }

        .empty-icon {
            color: #00ff88;
            font-size: 35px;
            margin-bottom: 15px;
        }

        .empty-state h3 {
            color: #f0f6fc;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #8b949e;
            margin-bottom: 20px;
        }

        .launch-link {
            display: inline-block;
            background: #00ff88;
            color: #06100b;
            text-decoration: none;
            padding: 11px 18px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 12px;
        }

        .launch-link:hover {
            background: #00d977;
        }

        @media (max-width: 700px) {

            .layout {
                flex-direction: column;
            }

            aside {
                width: 100%;
                padding: 12px 15px;
            }

            aside > div {
                flex-direction: row;
            }

            main {
                padding: 20px;
            }
        }

    </style>

</head>

<body>

    <!-- HEADER -->

    <header>
        <h1>INJECTX</h1>
    </header>


    <!-- MAIN LAYOUT -->

    <div class="layout">

        <!-- SIDEBAR -->

        <aside>

            <div>

                <a href="index.php">
                    New Scan
                </a>

                <a href="history.php" class="active">
                    Scan History
                </a>

            </div>

        </aside>


        <!-- CONTENT -->

        <main>

            <!-- PAGE HEADER -->

            <div class="page-header">

                <div class="page-label">
                    Security Records
                </div>

                <h2>
                    Scan History
                </h2>

                <p>
                    Review all previous target scans and their vulnerability findings.
                </p>

            </div>


            <?php if(!empty($scans)): ?>

                <!-- HISTORY TABLE -->

                <div class="history-card">

                    <table>

                        <thead>

                            <tr>

                                <th>
                                    Scan ID
                                </th>

                                <th>
                                    Target URL
                                </th>

                                <th>
                                    Date & Time
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Action
                                </th>

                                <th>
                                    Report
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach($scans as $scan): ?>

                                <tr>

                                    <!-- SCAN ID -->

                                    <td class="scan-id">
                                        #<?php echo htmlspecialchars($scan['id']); ?>
                                    </td>


                                    <!-- TARGET URL -->

                                    <td class="target-url">
                                        <?php echo htmlspecialchars($scan['target_url']); ?>
                                    </td>


                                    <!-- DATE & TIME -->

                                    <td class="date">
                                        <?php echo htmlspecialchars($scan['created_at']); ?>
                                    </td>


                                    <!-- STATUS -->

                                    <td>

                                        <?php if (strtolower(trim($scan['status'])) === 'completed'): ?>

                                            <span class="status-completed">
                                                Completed
                                            </span>

                                        <?php else: ?>

                                            <span class="status-completed">
                                                <?php echo htmlspecialchars($scan['status']); ?>
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- ACTION / VULNERABILITY RESULT -->

                                    <td>

                                        <?php if($scan['total_vuln'] > 0): ?>

                                            <span class="vuln-found">
                                                <?php echo htmlspecialchars($scan['total_vuln']); ?>
                                                Found
                                            </span>

                                        <?php else: ?>

                                            <span class="secure">
                                                Secure
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- REPORT -->

                                    <td>

                                        <a
                                            href="result.php?scan_id=<?php echo urlencode($scan['id']); ?>"
                                            class="view-report"
                                        >
                                            VIEW REPORT
                                        </a>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>


            <?php else: ?>

                <!-- EMPTY STATE -->

                <div class="empty-state">

                    <div class="empty-icon">
                        ◇
                    </div>

                    <h3>
                        No Scan History Found
                    </h3>

                    <p>
                        Launch a new vulnerability scan from the dashboard.
                    </p>

                    <a href="index.php" class="launch-link">
                        LAUNCH NEW SCAN
                    </a>

                </div>

            <?php endif; ?>

        </main>

    </div>

</body>

</html>