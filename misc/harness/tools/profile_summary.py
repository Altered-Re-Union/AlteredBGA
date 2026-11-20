"""Summarises an Xdebug cachegrind profile: functions by inclusive and self time.

python3 tools/profile_summary.py results/profile/cachegrind.out.<...> [top]
"""
import collections
import re
import sys

path = sys.argv[1]
top = int(sys.argv[2]) if len(sys.argv) > 2 else 25
names = {}
self_time = collections.Counter()
incl_time = collections.Counter()
calls = collections.Counter()
current = None
pending_call = None


def resolve(kind, value):
    # Compressed names: "(12) name" defines id 12, "(12)" reuses it
    m = re.match(r'\((\d+)\)(?: (.*))?', value)
    if not m:
        return value
    key = (kind, m.group(1))
    if m.group(2):
        names[key] = m.group(2)
    return names.get(key, value)


with open(path) as f:
    for line in f:
        line = line.rstrip('\n')
        if line.startswith('fn='):
            current = resolve('fn', line[3:])
            pending_call = None
        elif line.startswith('cfn='):
            pending_call = resolve('fn', line[4:])
        elif line.startswith('calls='):
            calls[pending_call] += int(line[6:].split()[0])
            pending_call = ('cost', pending_call)
        elif line and line[0].isdigit() and current:
            parts = line.split()
            cost = int(parts[1]) if len(parts) > 1 else 0
            if isinstance(pending_call, tuple):
                incl_time[current] += cost  # time spent in a callee counts for the caller
                pending_call = None
            else:
                self_time[current] += cost
                incl_time[current] += cost

total = sum(self_time.values())
print(f'total {total / 1e7:.2f}s (units of 10ns)')
print('-- inclusive')
for fn, t in incl_time.most_common(top):
    print(f'{t / total:6.1%} {calls[fn]:>8} {fn}')
print('-- self')
for fn, t in self_time.most_common(top):
    print(f'{t / total:6.1%} {calls[fn]:>8} {fn}')
