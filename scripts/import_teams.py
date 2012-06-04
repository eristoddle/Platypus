#
# MySQL Source:      team, player 
# Mongo Destination: teams, leagues, users
#

import pymongo
from bson.objectid import ObjectId
import MySQLdb as mdb
import pprint, sys, json, os, datetime
from mongo_tools import getMongoId

config_json = open('../config/config.json')
config      = json.load(config_json)
config_json.close()

mysql_config = config['mysql']
mongo_config = config['mongo']
image_folder = '../webroot/img/teams/';

identical_fields = []

name_changes = {
    'team_draft_id': 'draft_number'
}

# Load MySQL
old_con  = mdb.connect(mysql_config['server'], mysql_config['username'], mysql_config['password'], mysql_config['database'])
mysql_cur = old_con.cursor(mdb.cursors.DictCursor)

mysql_cur.execute("SELECT * FROM team ORDER BY team_id")
mysql_record = mysql_cur.fetchone()

# Load Mongo
new_con      = pymongo.Connection()
platypus_db  = new_con[mongo_config['database']]
teams_coll   = platypus_db['teams']
users_coll   = platypus_db['users']
leagues_coll = platypus_db['leagues']

# Clear Existing:
teams_coll.remove()
users_coll.update({}, {'$unset':{'teams': 1}})

# Erase images from previous import
for the_file in os.listdir(image_folder):
    file_path = os.path.join(image_folder, the_file)
    try:
        if os.path.isfile(file_path):
            os.unlink(file_path)
    except Exception, e:
        print e

#Setup Caching
league_cache = {}
user_cache   = {}
team_cache   = {}


while mysql_record != None:
    new_team   = {'mysql_id': int(mysql_record['team_id']), 'captains': []}
    photo_data = None

    # Strip nulls and empty strings, convert data:
    for f in mysql_record.keys():
        val = mysql_record[f]

        if type(val) is long:
            val = int(val)

        # Strip Nulls and Blanks
        if val == None or val == '':
            del mysql_record[f]
            continue

        # Do the conversion
        if f in identical_fields:
            new_team[f] = val
            continue

        if f in name_changes:
            new_f = name_changes[f]
            new_team[new_f] = val
            continue

        if f == 'name':
            new_team['name'] = unicode(val)

        if f == 'league_id':
            league_doc_id = getMongoId(leagues_coll, val, league_cache)

            if league_doc_id != None:
                new_team['league_id'] = league_doc_id

        if f == 'captain' or f == 'cocaptain':
            insert_pos = {'captain':0, 'cocaptain':1}

            captain_id = getMongoId(users_coll, val, user_cache, 'mysql_ids')
            
            if captain_id != None:
                new_team['captains'].insert(insert_pos[f], captain_id)

        if f in ['rank', 'wins', 'losses', 'ptdiff']:
            if f == 'ptdiff':
                f = 'point_differential'

            if 'stats' in new_team:
                new_team['stats'][f] = val;
            else:
                new_team['stats'] = {f: val}

        if f == 'Photo':
            photo_data = val
            new_team['photo'] = 'on_disk'
            del mysql_record[f]


    print "Saving " + new_team['name']
    new_id = teams_coll.save(new_team)

    # write image to disk
    if photo_data != None:
        image = open(image_folder + str(new_id) + '.jpg', 'wb');
        image.write(photo_data)
        image.close()
        print "Wrote photo for " + str(new_id)

    mysql_record = mysql_cur.fetchone()

# Associate Players and Teams
mysql_cur.execute('SELECT * FROM player WHERE league_id is not null AND c_id is not null AND team_id is not null ORDER BY c_id')
mysql_record = mysql_cur.fetchone()

write_count = 0

while mysql_record != None:
    user_id   = int(mysql_record['c_id'])
    team_id   = int(mysql_record['team_id'])

    user_doc_id   = None
    team_doc_id   = None

    user_doc_id = getMongoId(users_coll, user_id, user_cache, 'mysql_ids')
    team_doc_id = getMongoId(teams_coll, team_id, team_cache)
    
    if user_doc_id != None and team_doc_id != None:
        users_coll.update({'_id': user_doc_id}, {'$push': {'teams'   : team_doc_id}})
        teams_coll.update({'_id': team_doc_id}, {'$push': {'players' : user_doc_id}})
        write_count += 1
        if write_count % 25 == 0:
            sys.stdout.write('#')
        if write_count % (25*25) == 0:
            print

    # put teams in league
    mysql_record = mysql_cur.fetchone()