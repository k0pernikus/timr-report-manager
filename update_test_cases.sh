#!/bin/bash
php timr.php format:oddoo -c tests/csv/day_report/day_report.csv > tests/csv/day_report/odoo_expected.txt
php timr.php format:oddoo -c tests/csv/enter_and_exit/enter_and_exit.csv > tests/csv/enter_and_exit/expected_output.txt
php timr.php format:redmine -c tests/csv/day_report/day_report.csv > tests/csv/day_report/redmine_expected.txt
php timr.php format:redmine -c tests/csv/tags/tickets_containing_upper_and_lower_case.csv > tests/csv/tags/redmine_report_only_with_one_checkbox_tag.txt
