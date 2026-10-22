# Employee Org Chart Endpoint

A plain-PHP endpoint (no frameworks or libraries) that returns an employee's manager, direct reports, or full reporting tree.

## Running it

**Requirements:** PHP 8.4+ with `pdo_mysql`, and MySQL 8.0+.

1. **Enable `pdo_mysql` in PHP.** The app connects to MySQL through PDO, and this extension is often disabled by default (especially on Windows).

   > [!IMPORTANT]
   > If `pdo_mysql` isn't enabled, every request fails with a database error.

   Find your `php.ini`:

   ```bash
   php --ini
   ```

   Open the file listed as "Loaded Configuration File" and remove the `;` from this line:

   ```ini
   extension=pdo_mysql
   ```

   If no `php.ini` is loaded, copy `php.ini-development` (in your PHP folder) to `php.ini` first. Then check that it's enabled; this should print `pdo_mysql`:

   ```bash
   php -m | grep pdo_mysql
   ```

2. **Load the database.** `mysql.sql` creates the `php_test` database and seeds the `employees` table:

   ```bash
   mysql -u root < mysql.sql
   # or, with MySQL in Docker:
   docker exec -i php-test mysql < mysql.sql
   ```

3. **Start the server** from the project root:

   ```bash
   php -S localhost:8000
   ```

4. **Open a URL**, for example: http://localhost:8000/index.php?action=manager&employee_id=1010

By default it connects to MySQL on `127.0.0.1:3306` as `root` with no password. To use something else, set any of these environment variables before starting the server: `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`.

## Usage

```
GET /index.php?action=<action>&employee_id=<id>
```

| Parameter     | Values |
|---------------|--------|
| `action`      | `manager`, `direct_reports`, `everyone_below` (case-insensitive) |
| `employee_id` | An employee's ID. In the sample data, IDs run from **1001** (Maria Chen, CEO) to **1018**. |

Results are plain text. Errors are JSON with a 400 (bad or missing parameters), 404 (employee not found) or 500 (server error) status.

### Spec examples

| Example | Action | ID | Output |
|---------|--------|----|--------|
| Manager of Ben Walsh | `manager` | [1010](http://localhost:8000/index.php?action=manager&employee_id=1010) | `Manager of Ben Walsh: Priya Patel` |
| Direct reports of David Okafor | `direct_reports` | [1002](http://localhost:8000/index.php?action=direct_reports&employee_id=1002) | `Direct reports of David Okafor: Priya Patel, Tom Becker` |
| Everyone below Tom Becker | `everyone_below` | [1006](http://localhost:8000/index.php?action=everyone_below&employee_id=1006) | `Chloe Martin, Daniel Reyes (Level 1)` |

### Other test cases

| Case | Action | ID | Expected |
|------|--------|----|----------|
| CEO has no manager | `manager` | [1001](http://localhost:8000/index.php?action=manager&employee_id=1001) | No manager found |
| Inactive report is excluded | `direct_reports` | [1008](http://localhost:8000/index.php?action=direct_reports&employee_id=1008) | Frank Lopez (Grace Liu is inactive) |
| Tree past an inactive manager | `everyone_below` | [1005](http://localhost:8000/index.php?action=everyone_below&employee_id=1005) | Ana Torres, Ben Walsh (Level 1); Isabel Cruz, Jack Murphy (Level 3) |
| Whole company | `everyone_below` | [1001](http://localhost:8000/index.php?action=everyone_below&employee_id=1001) | Levels 1, 2, 3 and 5 |
| No one below | `everyone_below` | [1012](http://localhost:8000/index.php?action=everyone_below&employee_id=1012) | No subordinates found |
| Employee doesn't exist | `manager` | [99999](http://localhost:8000/index.php?action=manager&employee_id=99999) | 404 |
| Unknown action | `boss` | [1010](http://localhost:8000/index.php?action=boss&employee_id=1010) | 400 |
| Invalid ID | `manager` | [-5](http://localhost:8000/index.php?action=manager&employee_id=-5) | 400 |

## Notes

- Inactive employees are left out of `direct_reports` and `everyone_below`, but their active reports still appear at their real level. An inactive employee can still be looked up directly by ID, so past data stays visible.
- Database errors are logged to the PHP error log with the failing query or connection; the client only sees a generic 500.
