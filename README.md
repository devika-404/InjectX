
# InjectX

## Lightweight Automated SQL Injection Scanner

InjectX is a lightweight web application security scanning project designed to identify potential SQL Injection vulnerabilities in HTTP query parameters.

The project supports automated testing using Error-Based SQL Injection and Time-Based Blind SQL Injection detection techniques.

## Features

- Automated scanning of target URLs with query parameters
- Error-Based SQL Injection detection
- Time-Based Blind SQL Injection detection
- Rapid Scan mode
- Deep Scan mode
- Baseline response-time comparison
- Scan result storage using MySQL
- Result and scan history pages
- Manual verification support using Burp Suite

## Technologies Used

- PHP
- MySQL
- cURL
- HTML
- CSS
- Burp Suite

## Scanning Modes

### Rapid Scan

Rapid Scan performs error-based SQL Injection testing and checks the response for possible database error messages.

### Deep Scan

Deep Scan performs error-based testing along with time-based testing to identify potential blind SQL Injection vulnerabilities.

## Project Workflow

1. Enter a target URL containing query parameters.
2. The application parses the URL and its parameters.
3. Test payloads are sent to the target application.
4. Responses are analyzed for error indicators and response delays.
5. Scan results are stored and displayed to the user.

## Testing Environment

The project was tested using deliberately vulnerable and secure web applications.

Burp Suite was used for manual inspection and verification of HTTP requests and responses.

## Limitations

- Supports a limited set of SQL Injection detection techniques.
- Uses predefined test payloads.
- May produce false positives or false negatives.
- Detection behavior can vary depending on the database system.
- It is not a complete replacement for professional security testing tools.

## Ethical Use

InjectX should only be used for authorized security testing.

Do not scan websites, applications, or systems without explicit permission from the owner.

## Project Status

Academic cybersecurity internship project.
