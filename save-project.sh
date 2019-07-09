#!/bin/bash

# Save the git project to a specific repo (e.g. github, bitbucket, ...)
function save-project() {
    git remote rm origin
    git remote add origin $1
    git push
}

save-project git@gitlab.com:pH-7/lifyzer-data-parser-system.git
save-project git@bitbucket.org:pH_7/lifyzer-data-parser-system.git
save-project git@github.com:Lifyzer/Data-Parser-System.git