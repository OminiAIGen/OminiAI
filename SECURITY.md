# MagicAI Security Response

## Upload RCE advisory

Releases containing the public `POST /files/upload` route introduced on 19 September 2025 must be treated as vulnerable until this security update is installed. An unauthenticated attacker could upload a PHP file beneath the web root and execute commands as the PHP worker account.

Before investigating, block the upload and translation mutation routes at the proxy or WAF and prevent PHP execution beneath `public/uploads`. Preserve disk snapshots, access/error logs, file timestamps, ownership, running-process information, and hashes. Do not invoke a suspected shell.

For Nginx, place this rule before the normal PHP location:

```nginx
location ~* ^/uploads/.*\.(?:php[0-9]*|phtml|pht|phar|phps|cgi|shtml|asp|aspx|jsp)$ {
    return 403;
}

location ^~ /uploads/ {
    location ~ /\. {
        deny all;
    }
    try_files $uri =404;
}
```

The release includes an Apache `.htaccess` equivalent. Verify server-level configuration independently; `.htaccess` is ignored when Apache overrides are disabled.

## Read-only compromise scan

Run the scanner from a preserved copy or trusted host and pass every readable vhost and rotated access log explicitly:

```shell
php artisan security:audit-compromise \
  --path=/var/www/site-a/public \
  --path=/var/www/site-b/public \
  --log=/var/log/nginx/access.log \
  --log=/var/log/nginx/access.log.1
```

The command outputs JSON and never modifies findings. It checks known sample hashes, executable/configuration extensions, PHP and command-execution content, the reported IP addresses, `POST /files/upload`, PHP requests below `/uploads`, and `MAGICAI_RCE_VERIFIED`.

Also review CDN/proxy logs from at least 2 September 2026, cron and systemd units, user crontabs, SSH keys, `.user.ini`, `.htaccess`, PHP `auto_prepend_file`, temporary directories, modified application/vendor files, database administrators, API tokens, and unexpected processes.

If compromise is possible, rebuild the host from a known-good image. Treat every secret readable by the affected Unix account as exposed. From a clean system, revoke and rotate provider API keys, database credentials, OAuth/payment/mail/cloud secrets, SSH and control-panel credentials, sessions, and administrator credentials. Rotate `APP_KEY` only with a migration plan for sessions and encrypted data. Rotate replacement credentials after containment so they are not stolen again.

Run each Laravel installation under a separate Unix account and PHP-FPM pool. Use per-site `0750` directories and `0640` files, protect `.env` with owner-only access where practical, remove cross-site shared writable directories, restrict outbound network access, and use least-privilege database and cloud credentials.

Report vulnerabilities privately to the LiquidThemes security team before public disclosure. Include the affected version, reproduction steps, logs, and hashes; never email active secrets or executable malware samples without prior coordination.

## Temporary Laravel 10 advisory exceptions

The application remains on Laravel 10 for this release. Composer audit permits `PKSA-m5cs-t1y6-qpcs`, `PKSA-3r5d-mb8f-1qw9`, and `PKSA-mdq4-51ck-6kdq`, plus the low-severity PHP-JWT advisory `PKSA-y2cr-5h3j-g3ys` while the Apple Socialite provider prevents upgrading to JWT 7. The application uses PHP-JWT only for Apple RS256 verification, not the advisory's deprecated encryption path. The security owner must review these exceptions by 31 October 2026 and track both dependency upgrades. Signed URLs are not used as an authorization boundary, and outgoing mail is rejected when any address, display name, subject, or custom header contains control characters. Any new advisory must fail CI.
