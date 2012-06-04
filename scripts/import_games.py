#
# Source:      game, game_team  MySQL
# Destination: games            Mongo
#

import pymongo
from bson.objectid import ObjectId
from decimal import Decimal
import MySQLdb as mdb
import pprint, sys, json, os, datetime
from mongo_tools import getMongoId

config_json = open('../config/config.json')
config      = json.load(config_json)
config_json.close()

mysql_config = config['mysql']
mongo_config = config['mongo']

identical_fields = []

name_changes = {
    'game_id': 'mysql_id',
    'round': 'round_number'
}

# Load MySQL
old_con   = mdb.connect(mysql_config['server'], mysql_config['username'], mysql_config['password'], mysql_config['database'])
game_cur  = old_con.cursor(mdb.cursors.DictCursor)
score_cur = old_con.cursor(mdb.cursors.DictCursor)
game_cur.execute("SELECT * FROM game ORDER BY game_id")
game_record = game_cur.fetchone()

new_con      = pymongo.Connection()
platypus_db  = new_con[mongo_config['database']]
games_coll   = platypus_db['games']
leagues_coll = platypus_db['leagues']
fields_coll  = platypus_db['field_sites']
teams_coll   = platypus_db['teams']
users_coll   = platypus_db['users']

# Clear Existing:
games_coll.remove()

league_cache = {}
field_cache  = {}
team_cache   = {}
user_cache   = {}
write_count  = 0

# This is where the magic happens
while game_record != None:
    new_game = {}

    # Strip nulls and empty strings, convert data:
    for f in game_record.keys():
        val = game_record[f]

        if type(val) is long:
            val = int(val)

        if type(val) is Decimal:
            val = float(val)

        # Strip Nulls and Blanks
        if val == None or val == '':
            del game_record[f]
            continue

        # Do the conversion
        if f in identical_fields:
            new_game[f] = val
            continue

        if f in name_changes:
            new_f = name_changes[f]
            new_game[new_f] = val
            continue

        if f == 'time':
            tz_shift = datetime.timedelta(hours=4) # Times are stored in mysql incorrectly
            new_val = val + tz_shift
            new_game['game_time'] = new_val

        if f == 'league_id':
            league_doc_id = getMongoId(leagues_coll, val, league_cache)

            if league_doc_id != None:
                new_game['league_id'] = league_doc_id

        if f == 'field_id':
            field_doc_id = getMongoId(fields_coll, val, field_cache)

            if field_doc_id != None:
                new_game['fieldsite_id'] = field_doc_id

        if f == 'field_num':
            new_game['field'] = str(val)

    # Pull in score data
    if new_game['mysql_id'] != None:
        team_list = set()
        score_list = {}
        score_cur.execute("SELECT * FROM game_team WHERE game_id=" + str(new_game['mysql_id']))
        all_scores = score_cur.fetchall()
        for score in all_scores:
            if score['forfeit'] == -1:
                # Bad Score Data, skip
                continue

            own_id = getMongoId(teams_coll, int(score['team_id']), team_cache)
            opp_id = getMongoId(teams_coll, int(score['opponent']), team_cache)
            team_list.add(own_id)
            team_list.add(opp_id)

            # Score Data
            if score['own_points'] != None:
                score_list[str(own_id)] = int(score['own_points'])

            if score['opponent_points'] != None:
                score_list[str(opp_id)] = int(score['opponent_points'])

            if score['won'] != None and score['won'] == 1:
                new_game['winner'] = own_id

            if score['forfeit'] == 1:
                new_game['forfeit'] = True

            if score['score_reporter'] != None:
                reporter_id = getMongoId(users_coll, int(score['score_reporter']), user_cache, 'mysql_ids')
                if reporter_id != None:
                    new_game['reporter'] = reporter_id

        new_game['teams'] = list(team_list)
        if len(score_list) > 0:
            new_game['scores'] = score_list

    games_coll.save(new_game)

    write_count += 1
    if write_count % 25 == 0:
        sys.stdout.write('#')
    if write_count % (25*25) == 0:
        print

    game_record = game_cur.fetchone()

print

