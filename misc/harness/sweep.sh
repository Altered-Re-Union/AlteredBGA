#!/bin/sh
# Full card sweep, one shard per faction running in parallel. Results in results/:
# costs.csv (queries and time per card), fingerprints.csv (notifications and database hashes),
# junit_*.xml (outcomes). SWEEP_PICK=last makes the naive player take the last options.
# GAME_DIR=/path/to/another/checkout sweeps another version of the game code.
cd "$(dirname "$0")"
mkdir -p results && rm -f results/cost_*.csv results/junit_*.xml results/phpunit_*.txt results/costs.csv results/fingerprints*.csv results/evaluation_writes.txt
docker compose up -d --wait db >/dev/null

shard() {
  f=$1
  rm -f "results/cost_$f.csv" "results/fingerprints_$f.csv"
  # The JUnit report is written inside the container then copied: opening it on the shared
  # folder from several containers at once sometimes fails
  docker compose run --rm -T -e SWEEP="^${f}_" -e DB_NAME="sweep_$f" -e SWEEP_PICK="${SWEEP_PICK:-first}" \
    -e SWEEP_OUT="/harness/results/cost_$f.csv" -e SWEEP_FINGERPRINTS="/harness/results/fingerprints_$f.csv" \
    php sh -c "phpunit -c phpunit-sweep.xml --log-junit /tmp/junit.xml; cp /tmp/junit.xml /harness/results/junit_$f.xml" \
    > "results/phpunit_$f.txt" 2>&1
}

for f in AX BR LY MU OD YZ; do shard $f & done
wait
# A shard stops at the first fatal error: run the ones that did not finish once more
failed=0
for f in AX BR LY MU OD YZ; do
  if ! grep -q "^Tests:" "results/phpunit_$f.txt"; then
    echo "shard $f did not finish, running it again"
    shard $f
    grep -q "^Tests:" "results/phpunit_$f.txt" || { echo "shard $f failed, see results/phpunit_$f.txt"; failed=1; }
  fi
done
cat results/cost_*.csv | sort -t, -k2 -n -r > results/costs.csv
cat results/fingerprints_*.csv | sort > results/fingerprints.csv
grep -h "^Tests:" results/phpunit_*.txt
exit $failed
