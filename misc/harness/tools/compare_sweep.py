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

def fingerprints(directory):
    path = os.path.join(directory, 'fingerprints.csv')
    if not os.path.exists(path):
        return {}
    with open(path) as f:
        return {row[0]: row[2:] for row in csv.reader(f)}


fb, fa = fingerprints(before_dir), fingerprints(after_dir)
if fb and fa:
    differing = sorted(c for c in set(fb) & set(fa) if fb[c] != fa[c])
    print(f'fingerprints: {len(set(fb) & set(fa))} cards compared, {len(differing)} differ')
    for card in differing:
        parts = [name for name, i in (('outcome', 0), ('notifications', 1), ('database', 2)) if fb[card][i] != fa[card][i]]
        print(f'  {card}: {", ".join(parts)} ({fb[card][0]} -> {fa[card][0]})')
    missing = sorted(set(fb) ^ set(fa))
    if missing:
        print(f'  only in one sweep: {missing[:20]}')

cb, ca = costs(before_dir), costs(after_dir)
common = set(cb) & set(ca)
qb, qa = sum(cb[c][0] for c in common), sum(ca[c][0] for c in common)
tb, ta = sum(cb[c][1] for c in common), sum(ca[c][1] for c in common)
print(f'queries: {qb} -> {qa} ({qa / qb:.1%}), time: {tb / 1000:.0f}s -> {ta / 1000:.0f}s ({ta / tb:.1%})')
print('slowest cards after:')
for card in sorted(common, key=lambda c: -ca[c][1])[:10]:
    print(f'  {card}: {cb[card][0]} q / {cb[card][1]} ms -> {ca[card][0]} q / {ca[card][1]} ms')
