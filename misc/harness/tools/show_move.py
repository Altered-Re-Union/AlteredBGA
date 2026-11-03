"""Prints the notifications of one request of a replay: python3 tools/show_move.py <table> <move> [width]"""
import json
import sys

table, move = sys.argv[1], int(sys.argv[2])
width = int(sys.argv[3]) if len(sys.argv) > 3 else 220
data = json.load(open(f'replays/{table}.json'))['data']
for packet in sorted(data['logs'], key=lambda p: int(p['packet_id'])):
    if packet['move_id'] is None or int(packet['move_id']) != move:
        continue
    for n in packet['data']:
        if n['type'] in ('updateReflexionTime',):
            continue
        args = n['args'] if n['type'] != 'gameStateChange' else {'id': n['args'].get('id'), 'name': n['args'].get('name'), 'active': n['args'].get('active_player')}
        print(packet['channel'].split('/')[-1], n['type'], json.dumps(args, ensure_ascii=False)[:width])
