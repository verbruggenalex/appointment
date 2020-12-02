#!/bin/sh

#
# Run the hook command.
# Note: this will be replaced by the real command during copy.
#

# Fetch the GIT diff and format it as command input:
DIFF=$(git -c diff.mnemonicprefix=false -c diff.noprefix=false --no-pager diff -r -p -m -M --full-index --no-color --staged | cat)

# Grumphp env vars
export GIT_WORKING_DIR="$(git rev-parse --show-toplevel)"

# Run GrumPHP
(cd "${HOOK_EXEC_PATH}" && printf "%s\n" "${DIFF}" | "./vendor/bin/phpcs")


