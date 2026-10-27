"""Compares two card sweeps: outcome of each card (from the JUnit reports) and query cost.

python3 tools/compare_sweep.py baseline results
"""
import csv
import glob
import os
import re
import sys
import xml.etree.ElementTree as ET


def outcomes(directory):
    result = {}
    for path in glob.glob(os.path.join(directory, '**', 'junit_*.xml'), recursive=True):
        for case in ET.parse(path).iter('testcase'):
            card = re.sub(r'.*"(.*)".*', r'\1', case.get('name'))
            status = 'pass'
            for child in case:
                if child.tag in ('error', 'failure'):
                    status = 'fail: ' + (child.text or '').strip().split('\n')[0][:150]
                elif child.tag == 'skipped':
                    status = 'skipped/incomplete'
            result[card] = status
    return result


def costs(directory):
    with open(os.path.join(directory, 'costs.csv')) as f:
        return {row[0]: (int(row[1]), int(row[2])) for row in csv.reader(f)}


before_dir, after_dir = sys.argv[1], sys.argv[2]
before, after = outcomes(before_dir), outcomes(after_dir)
changed = {c: (before.get(c), after.get(c)) for c in set(before) | set(after) if before.get(c) != after.get(c)}
print(f'{len(after)} cards, outcome changed for {len(changed)}')
for card, (b, a) in sorted(changed.items()):
    print(f'  {card}: {b} -> {a}')

cb, ca = costs(before_dir), costs(after_dir)
common = set(cb) & set(ca)
qb, qa = sum(cb[c][0] for c in common), sum(ca[c][0] for c in common)
tb, ta = sum(cb[c][1] for c in common), sum(ca[c][1] for c in common)
print(f'queries: {qb} -> {qa} ({qa / qb:.1%}), time: {tb / 1000:.0f}s -> {ta / 1000:.0f}s ({ta / tb:.1%})')
print('slowest cards after:')
for card in sorted(common, key=lambda c: -ca[c][1])[:10]:
    print(f'  {card}: {cb[card][0]} q / {cb[card][1]} ms -> {ca[card][0]} q / {ca[card][1]} ms')
