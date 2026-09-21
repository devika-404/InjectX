<?php
    require 'db.php';
?>

<?php
    require 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InjectX-Dashboard</title>

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

        .layout {
            display: flex;
            min-height: calc(100vh - 70px);
        }

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

        main {
            flex: 1;
            padding: 40px;
        }

        .scan-card {
            background: #0d1117;
            border: 1px solid #1f2937;
            border-radius: 10px;
            padding: 35px;
            max-width: 850px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        }

        .scan-label {
            color: #00ff88;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 12px;
        }

        .scan-card h2 {
            font-size: 27px;
            margin-bottom: 35px;
            color: #f0f6fc;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            font-size: 13px;
            color: #8b949e;
            margin-bottom: 9px;
        }

        input,
        select {
            width: 100%;
            background: #080b12;
            color: #e6edf3;
            border: 1px solid #30363d;
            border-radius: 6px;
            padding: 13px 14px;
            outline: none;
            font-size: 14px;
        }

        input:focus,
        select:focus {
            border-color: #00ff88;
            box-shadow: 0 0 0 2px rgba(0, 255, 136, 0.08);
        }

        select {
            cursor: pointer;
        }

        .form-bottom {
            display: flex;
            align-items: end;
            gap: 20px;
        }

        .scan-type {
            flex: 1;
        }

        button {
            background: #00ff88;
            color: #06100b;
            border: none;
            padding: 13px 22px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 13px;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: 0.2s ease;
        }

        button:hover {
            background: #00d977;
            transform: translateY(-1px);
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

            .scan-card {
                padding: 25px;
            }

            .form-bottom {
                flex-direction: column;
                align-items: stretch;
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
                <a href="index.php" class="active">New Scan</a>
                <a href="history.php">Scan History</a>
            </div>
        </aside>

        <!-- CONTENT -->
        <main>

            <div class="scan-card">

                <div class="scan-label">
                    New Scan
                </div>

                <h2>Start New Vulnerability Scan</h2>

                <!-- SCAN FORM -->
                <form action="scan.php" method="POST">

                    <!-- TARGET URL -->
                    <div class="form-group">
                        <label for="target_url">Target URL</label>

                        <input
                            type="text"
                            id="target_url"
                            name="target_url"
                            placeholder="https://example.com/page?id=1"
                            required
                        >
                    </div>

                    <!-- SCAN TYPE + BUTTON -->
                    <div class="form-bottom">

                        <div class="scan-type">
                            <label for="scan_type">Scan Depth</label>

                            <select id="scan_type" name="scan_type">
                                <option value="full">Deep Scan</option>
                                <option value="fast">Rapid Scan</option>
                            </select>
                        </div>

                        <button type="submit">
                            LAUNCH SCAN
                        </button>

                    </div>

                </form>

            </div>

        </main>

    </div>

</body>
</html>