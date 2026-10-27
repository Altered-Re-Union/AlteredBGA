#!/bin/sh
# Full card sweep, one shard per faction running in parallel. Results in results/
set -e
cd "$(dirname "$0")"
mkdir -p results && rm -f results/cost_*.csv results/junit_*.xml results/phpunit_*.txt results/costs.csv
docker compose up -d --wait db >/dev/null
for f in AX BR LY MU OD YZ; do
  docker compose run --rm -T -e SWEEP="^${f}_" -e DB_NAME="sweep_$f" -e SWEEP_OUT="/harness/results/cost_$f.csv" \
    php phpunit --filter CardSweepTest --log-junit "/harness/results/junit_$f.xml" > "results/phpunit_$f.txt" 2>&1 &
done
wait
cat results/cost_*.csv | sort -t, -k2 -n -r > results/costs.csv
grep -h "^Tests:" results/phpunit_*.txt
