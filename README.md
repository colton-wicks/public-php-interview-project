# Employee Org Chart Endpoint

A plain-PHP endpoint (no frameworks or libraries) that returns an employee's manager, direct reports, or full reporting tree.

## Running it

**Requirements:** PHP 8.4+ with `pdo_mysql`, and MySQL 8.0+.

1. **Load the database.** `mysql.sql` creates the `php_test` database and seeds the `employees` table:

   ```bash
   mysql -u root < mysql.sql
   # or, with MySQL in Docker:
   docker exec -i php-test mysql < mysql.sql
   ```

2. **Start the server** from the project root:

   ```bash
   php -S localhost:8000
   ```

3. **Open a URL**, for example: http://localhost:8000/index.php?action=manager&employee_id=1010

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

| Example | URL | Output |
|---------|-----|--------|
| Manager of Ben Walsh | [`?action=manager&employee_id=1010`](http://localhost:8000/index.php?action=manager&employee_id=1010) | `Manager of Ben Walsh: Priya Patel` |
| Direct reports of David Okafor | [`?action=direct_reports&employee_id=1002`](http://localhost:8000/index.php?action=direct_reports&employee_id=1002) | `Direct reports of David Okafor: Priya Patel, Tom Becker` |
| Everyone below Tom Becker | [`?action=everyone_below&employee_id=1006`](http://localhost:8000/index.php?action=everyone_below&employee_id=1006) | `Chloe Martin, Daniel Reyes (Level 1)` |

### Other test cases

| Case | URL | Expected |
|------|-----|----------|
| CEO has no manager | [`?action=manager&employee_id=1001`](http://localhost:8000/index.php?action=manager&employee_id=1001) | No manager found |
| Inactive report is excluded | [`?action=direct_reports&employee_id=1008`](http://localhost:8000/index.php?action=direct_reports&employee_id=1008) | Frank Lopez (Grace Liu is inactive) |
| Tree past an inactive manager | [`?action=everyone_below&employee_id=1005`](http://localhost:8000/index.php?action=everyone_below&employee_id=1005) | Ana Torres, Ben Walsh (Level 1); Isabel Cruz, Jack Murphy (Level 3) |
| Whole company | [`?action=everyone_below&employee_id=1001`](http://localhost:8000/index.php?action=everyone_below&employee_id=1001) | Levels 1, 2, 3 and 5 |
| No one below | [`?action=everyone_below&employee_id=1012`](http://localhost:8000/index.php?action=everyone_below&employee_id=1012) | No subordinates found |
| Employee doesn't exist | [`?action=manager&employee_id=99999`](http://localhost:8000/index.php?action=manager&employee_id=99999) | 404 |
| Unknown action | [`?action=boss&employee_id=1010`](http://localhost:8000/index.php?action=boss&employee_id=1010) | 400 |
| Invalid ID | [`?action=manager&employee_id=-5`](http://localhost:8000/index.php?action=manager&employee_id=-5) | 400 |

## Notes

- Inactive employees are left out of `direct_reports` and `everyone_below`, but their active reports still appear at their real level. An inactive employee can still be looked up directly by ID, so past data stays visible.
- Database errors are logged to the PHP error log with the failing query or connection; the client only sees a generic 500.
