<?php
/**
 * CashSecond - Pure PHP SMTP Mailer
 * Lightweight, zero-dependency SMTP socket client supporting SSL/TLS, AUTH LOGIN,
 * UTF-8 HTML + Text bodies, custom headers, and detailed diagnostics.
 */

class SmtpMailer
{
    /**
     * Send email using SMTP configuration
     */
    public static function send(string $to, string $subject, string $htmlBody, string $plainTextBody = '', array $customConfig = []): array
    {
        $configFile = __DIR__ . '/../config/smtp.php';
        $config = file_exists($configFile) ? require $configFile : [];
        if (!empty($customConfig)) {
            $config = array_merge($config, $customConfig);
        }

        $host       = $config['host']        ?? 'smtp.gmail.com';
        $port       = (int)($config['port']  ?? 587);
        $encryption = strtolower($config['encryption'] ?? 'tls');
        $user       = $config['username']    ?? '';
        $pass       = $config['password']    ?? '';
        $fromEmail  = $config['from_email']  ?? ($user ?: 'leads@cashsecond.in');
        $fromName   = $config['from_name']   ?? 'CashSecond Leads';
        $timeout    = (int)($config['timeout'] ?? 15);

        $logsDir = __DIR__ . '/../logs';
        if (!is_dir($logsDir)) {
            @mkdir($logsDir, 0755, true);
        }
        $logPath = $logsDir . '/smtp_debug.log';

        $log = function ($msg) use ($logPath) {
            @file_put_contents($logPath, date('Y-m-d H:i:s') . " " . $msg . "\n", FILE_APPEND | LOCK_EX);
        };

        // If credentials are completely empty (no host or no user), fallback to standard PHP mail()
        if (empty($host) || (empty($user) && empty($pass) && $port !== 25)) {
            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "From: {$fromName} <{$fromEmail}>\r\n";
            $headers .= "Reply-To: {$fromEmail}\r\n";
            $sent = @mail($to, $subject, $htmlBody, $headers);
            $log("Fallback to native mail() for {$to}: " . ($sent ? "SUCCESS" : "FAILED"));
            return [
                'success' => $sent,
                'method'  => 'native_mail_fallback',
                'error'   => $sent ? '' : 'mail() returned false'
            ];
        }

        $remoteSocket = ($encryption === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;
        $context = stream_context_create([
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ]
        ]);

        $log("Connecting to {$remoteSocket} (Timeout: {$timeout}s)...");
        $socket = @stream_socket_client($remoteSocket, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context);

        if (!$socket) {
            $err = "Connection failed to {$remoteSocket}: [{$errno}] {$errstr}";
            $log("ERROR: {$err}");
            // Try mail() fallback
            @mail($to, $subject, $htmlBody, "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\nFrom: {$fromName} <{$fromEmail}>\r\n");
            return ['success' => false, 'method' => 'smtp', 'error' => $err];
        }

        stream_set_timeout($socket, $timeout);

        $read = function () use ($socket, $log) {
            $data = '';
            while ($str = fgets($socket, 515)) {
                $data .= $str;
                if (substr($str, 3, 1) === ' ') break;
            }
            $log("<< " . trim($data));
            return $data;
        };

        $write = function ($cmd, $hideLog = false) use ($socket, $log) {
            $log(">> " . ($hideLog ? '********' : trim($cmd)));
            fputs($socket, $cmd . "\r\n");
        };

        $initial = $read();
        if (substr($initial, 0, 3) !== '220') {
            fclose($socket);
            $log("ERROR: Invalid welcome string: {$initial}");
            return ['success' => false, 'method' => 'smtp', 'error' => "Invalid welcome: {$initial}"];
        }

        // EHLO
        $clientDomain = $_SERVER['SERVER_NAME'] ?? 'localhost';
        $write("EHLO {$clientDomain}");
        $ehlo = $read();

        // STARTTLS if requested and on port 587 or encryption = tls
        if ($encryption === 'tls' || ($port === 587 && strpos($ehlo, 'STARTTLS') !== false)) {
            $write("STARTTLS");
            $tlsRes = $read();
            if (substr($tlsRes, 0, 3) === '220') {
                $crypto = @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT);
                if (!$crypto) {
                    fclose($socket);
                    $log("ERROR: TLS handshake failed.");
                    return ['success' => false, 'method' => 'smtp', 'error' => 'TLS handshake failed'];
                }
                $log("TLS connection established.");
                // Re-send EHLO after TLS handshake
                $write("EHLO {$clientDomain}");
                $read();
            }
        }

        // AUTH LOGIN if username is configured
        if (!empty($user)) {
            $write("AUTH LOGIN");
            $authRes = $read();
            if (substr($authRes, 0, 3) === '334') {
                $write(base64_encode($user));
                $uRes = $read();
                if (substr($uRes, 0, 3) === '334') {
                    $write(base64_encode($pass), true);
                    $pRes = $read();
                    if (substr($pRes, 0, 3) !== '235') {
                        fclose($socket);
                        $log("ERROR: Authentication failed: {$pRes}");
                        return ['success' => false, 'method' => 'smtp', 'error' => "SMTP Authentication failed: {$pRes}"];
                    }
                    $log("SMTP Authentication successful for {$user}");
                }
            }
        }

        // MAIL FROM
        $write("MAIL FROM:<{$fromEmail}>");
        $fromRes = $read();
        if (substr($fromRes, 0, 3) !== '250') {
            fclose($socket);
            $log("ERROR: MAIL FROM rejected: {$fromRes}");
            return ['success' => false, 'method' => 'smtp', 'error' => "MAIL FROM rejected: {$fromRes}"];
        }

        // RCPT TO
        $write("RCPT TO:<{$to}>");
        $toRes = $read();
        if (substr($toRes, 0, 3) !== '250' && substr($toRes, 0, 3) !== '251') {
            fclose($socket);
            $log("ERROR: RCPT TO rejected: {$toRes}");
            return ['success' => false, 'method' => 'smtp', 'error' => "RCPT TO rejected: {$toRes}"];
        }

        // DATA
        $write("DATA");
        $dataRes = $read();
        if (substr($dataRes, 0, 3) !== '354') {
            fclose($socket);
            $log("ERROR: DATA rejected: {$dataRes}");
            return ['success' => false, 'method' => 'smtp', 'error' => "DATA rejected: {$dataRes}"];
        }

        // Build MIME message with Multipart Alternative (Plain Text + Rich HTML)
        $boundary = '=_CashSecond_' . md5(uniqid(rand(), true));
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $encodedFromName = '=?UTF-8?B?' . base64_encode($fromName) . '?=';

        $msgHeaders  = "From: {$encodedFromName} <{$fromEmail}>\r\n";
        $msgHeaders .= "To: <{$to}>\r\n";
        $msgHeaders .= "Subject: {$encodedSubject}\r\n";
        $msgHeaders .= "Date: " . date('r') . "\r\n";
        $msgHeaders .= "MIME-Version: 1.0\r\n";
        $msgHeaders .= "Message-ID: <" . md5(uniqid(rand(), true)) . "@cashsecond.in>\r\n";
        $msgHeaders .= "X-Mailer: CashSecond-SMTP-Engine/2.0\r\n";
        $msgHeaders .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n\r\n";

        $msgBody  = "--{$boundary}\r\n";
        $msgBody .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $msgBody .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $msgBody .= $plainTextBody . "\r\n\r\n";

        $msgBody .= "--{$boundary}\r\n";
        $msgBody .= "Content-Type: text/html; charset=UTF-8\r\n";
        $msgBody .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $msgBody .= $htmlBody . "\r\n\r\n";

        $msgBody .= "--{$boundary}--\r\n";

        // Dot termination
        $fullPayload = $msgHeaders . $msgBody . "\r\n.";
        $write($fullPayload);
        $finalRes = $read();

        $write("QUIT");
        $read();
        fclose($socket);

        $isOk = (substr($finalRes, 0, 3) === '250');
        $log("Mail delivery to {$to}: " . ($isOk ? "SUCCESS (250 OK)" : "FAILED ({$finalRes})"));

        return [
            'success'  => $isOk,
            'method'   => 'smtp',
            'response' => trim($finalRes),
            'error'    => $isOk ? '' : $finalRes
        ];
    }
}
