#!/bin/zsh
# GoFull dev backend — run by launchd (com.gofull.backend), NOT by a
# terminal. Keeps the API + Reverb + queue + scheduler alive independent of
# VS Code / Terminal windows, so closing the editor never kills the server.
#
# Manage it:
#   launchctl kickstart -k gui/$UID/com.gofull.backend   # restart
#   launchctl bootout   gui/$UID/com.gofull.backend      # stop for good
# Logs: storage/logs/dev-stack.log

export PATH="/Users/usermac/.nvm/versions/node/v24.19.0/bin:/Users/usermac/Library/Application Support/Herd/bin:/opt/homebrew/bin:/usr/bin:/bin:/usr/sbin:/sbin"
export LANG="en_US.UTF-8"

cd /Users/usermac/Desktop/GoFull || exit 1

# concurrently uses --kill-others: if any process dies, all exit, and
# launchd's KeepAlive restarts the whole stack cleanly.
exec composer run dev
