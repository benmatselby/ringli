# Ringli

CLI tool to display some Circle CI data.

## Requirements

- [PHP version 8.1+](https://www.php.net)
- API Token from CircleCI. You can get this information from [here](https://circleci.com/docs/managing-api-tokens/).

## Environment variables

In order to connect to Circle CI you require the following variables.

```bash
export CIRCLE_CI_TOKEN=""
export CIRCLE_CI_ORG="" # vcs-slug/org-name e.g. gh/benmatselby
```

## Installation via Git

```shell
git clone https://github.com/benmatselby/ringli.git
cd ringli
make clean install
bin/ringcli.php list
```
