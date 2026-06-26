# Configuration

codPass stores its configuration in a single XML file:

```
app/config/config.xml
```

This file is **created by the web installer** the first time you set up the
application (see [INSTALL.md](INSTALL.md)). You normally do not edit it by hand
— most settings are managed from the web UI under the configuration/admin
section, which rewrites `config.xml` for you. Treat the file as sensitive: it
contains connection details and secrets.

## Main configurable areas

The following areas exist in codPass and are configurable after installation:

### Authentication

- **MySQL** — built-in user accounts stored in the application database.
- **LDAP / OpenLDAP** — authenticate against an LDAP directory (server, base
  DN, bind details, optional group filtering).
- **Active Directory** — LDAP-based authentication against Microsoft AD.

These backends can be combined so users authenticate against the directory
while accounts and permissions are managed in codPass.

### Mail

SMTP settings used for email notifications and account-related requests:
enable/disable mail, SMTP server and port, optional SMTP authentication, and
the "from" address.

### Backup

Application backup of the database and configuration. Backups are written under
`app/backup/` and protected by a generated hash so they cannot be downloaded by
guessing the filename.

### Encryption

codPass encrypts stored account passwords. The master password is set during
installation and is used to derive the encryption used for secret data; there
is also a session-encryption option. Changing the master password re-encrypts
the stored secrets.

---

For installation and requirements, see [INSTALL.md](INSTALL.md). For an index
of all documentation, see [README.md](README.md).
