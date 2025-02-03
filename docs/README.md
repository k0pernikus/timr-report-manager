# timr-report-manager

[![unit tests](https://github.com/k0pernikus/timr-report-manager/actions/workflows/php.yml/badge.svg)](https://github.com/k0pernikus/timr-report-manager/actions/workflows/php.yml)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/k0pernikus/timr-report-manager/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/k0pernikus/timr-report-manager/?branch=main)
[![Code Coverage](https://scrutinizer-ci.com/g/k0pernikus/timr-report-manager/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/k0pernikus/timr-report-manager/?branch=main)

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Tool to reformat timr time tracking to export it to external time tracking tools

## Use Case

Generating reports to easily and quickly forward them to external time tracking tools.

Timr in its free version has the limitation to only track one activity. One can use the Notes annotation field as a "
task descriptor" via `#`.

I have the use case to track my time both for:

- total work time over the course of a day (e.g. 'phone call with hannes', 'ticket #123', 'writing ticket')
- time spent on specific tasks (e.g. a ticket #123)
- it shows the hours both in the hour:minute (e.g. `5:15`), and as hours (e.g. `5.25`) for working with different time
  tracking tools at the same time

# Conventions

## Keywords in Notes

### Location

- `enter`: means entry of office location, won't be considered working time
- `exit`: time marker when one has left the office

### Ticket

- `#` prefix, or any message containing a tag, e.g. `#123` or `#jobDescription` will be shown in the ticketing overview
- other entries without these markers won't show in the ticketing report
- only one tag per time entry allowed

# Requirements

- Download your timr csv
- [My page](https://kopernikus.timr.com/timr/reports/workingTime.html)
- You may use: https://__YOUR_USER__.timr.com/timr/reports/workingTime.html
- php^8.0

# Installation

```
git clone git@github.com:k0pernikus/timr-report-manager.git
cd timr-report-manager
composer install
php timr.php
``` 

# Usage

```
$> php timr.php format:odoo --csv path_to.csv
ODOO_EXAMPLE
```

```
$> php timr.php format:redmine --csv path_to.csv
REDMINE_EXAMPLE
```

The overview is also there to get a quick understanding of hours worked vs hours required.

```
php .\timr.php overview -c .\tests\csv\monthly.csv
2024-10: Expected 60 / Delivered 63.9
```

# ToDos

## Must Haves

### Extended Features

- [ ] sync with external services,
    - [ ] e.g. redmine
    - [ ] odoo

### Basic Features

- [x] in the ticket report, only show activities having a ticket id number included
- [x] support keywords, `exit`, `enter`, ...
- [x] ... and tags `#{any_tag_starting_with_hashtag}`
- [x] tags should be discovered even if only part of the text
- [x] yet only one tag may be part of the note
- [x] automatically summarize activities having the same end and start date when having the same note
- [x] redmine formatter should round up to the next quarter

## Nice to haves

- [ ] support different languages
- [ ] vacations, sick days, and holidays instead of hardcoded
- [ ] distinguish / filter out user
- [x] RedmineFormatter round up value should be input flag
- [x] also show break times over the course of a day
- [x] formatting

## DX Experience

- [x] Code style, slightly changed PSR12 style
- [x] phpstan
- [x] resolve __DIR__ issue when coding on windows using WSL
- [x] nice badges
- [x] GitHub action for unit tests
- [x] scrutinizer
- [x] automatically update README.md examples
- [x] scrutinizer code coverage
- [x] code coverage