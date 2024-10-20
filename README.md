# timr-report-manager

Tool to reformat timr time tracking to export it to external time tracking tools

## Seals of Approvals

[![unit tests](https://github.com/k0pernikus/timr-report-manager/actions/workflows/php.yml/badge.svg)](https://github.com/k0pernikus/timr-report-manager/actions/workflows/php.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/k0pernikus/timr-report-manager/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/k0pernikus/timr-report-manager/?branch=main)

## Use Case
Generating reports to easily and quickly forward them to external time tracking tools.

Timr in its free version has the limitation to only track one activity. One can use the Notes annotation field as a "
task descriptor".

I have the use case to track my time both for:
- total work time over the course of a day (e.g. 'phone call with hannes', 'ticket #123', 'writing ticket')
- time spent on specific tasks (e.g. a ticket #123)
- it shows the hours both in the hour:minute (e.g. `5:15`), and as hours (e.g. `5.25`) for working with different time
  tracking tools at the same time

Furthermore, this reports groups the activity by the notes.

# Conventions

## Keywords in Notes

## Location

- enter: means entry of office location, won't be considered working time
    - an entry having same start and same entry will also be considered as an entry/exit point
- exit: time marker when one has left the office

## Ticket

- `#` prefix, or any message containing a tag, e.g. `#123` or `#jobDescription` will shown in the ticketing overview
- other entries without these markers won't show in the ticketing report

## Requirements

- Download your timr csv
- [My page](https://kopernikus.timr.com/timr/reports/workingTime.html)
- You may use: https://__YOUR_USER__.timr.com/timr/reports/workingTime.html
- php installed locally

## Usage

```
$> php timr.php format:odoo --csv path_to.csv
DATE: 2024-10-18
DAY TOTAL: 5:15
    #Ticket123 (2:41)
        09:44 - 10:06
        12:10 - 14:29
    PC Adminstration (0:09)
        10:12 - 10:21
    Call mit Heino (1:08)
        10:22 - 11:30
    #Ticket42 (0:24)
        11:30 - 11:54
    Meeting Max Mustermann (0:53)
        14:29 - 15:22
```

```
$> php timr.php format:redmine --csv path_to.csv
Date: 2024-10-18
Total ticketable hours:3
    #Ticket123: 2.68 h
    #Ticket42: 0.4 h
    
Non Billable Hours: 2
```

The overview is also there to get a quick understanding of hours worked vs hours required.

```
php .\timr.php overview -c .\tests\csv\monthly.csv
2024-10: Expected 60 / Delivered 63.9
```

# ToDos:

## Must Haves:

- [x] in the ticket report, only show activities having a ticket id number included
- [x] support keywords, `exit`, `enter`, ...
  - [ ] `enter`, `exit` can happen multiple times a day
- [ ] ... and tags `#{any_tag_starting_with_hashtag}`
- [ ] tags should be discovered even if only part of the text (yet only one tag may be part of the note)
- [x] automatically summarize activities having the same end and start date when having the same note
- [ ] should show expectations on a week and monthly basis
- [ ] support different languages



## Nice to haves:

- [ ] also show break times over the course of a day
- [ ] vacations, sick days, and holidays instead of hardcoded
- [ ] be able to use both "Type" as "Notes" as notes as task descriptor, at best via flag
  - free tier only allows two types anyway, maybe WONTFIX
- [x] formatting
- [ ] distinguish / filter out user
- [ ] support different languages

## DX Experience

- [ ] Codestyle
- [ ] code coverage
- [x] phpstan
- [x] resolve __DIR__ issue when coding on windows using WSL
- [x] nice badges
- [x] github action for unit tests
- [x] scrutinzer
- [ ] scrutinizer code coverage
- [ ] code coverage